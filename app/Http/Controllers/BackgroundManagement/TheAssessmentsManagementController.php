<?php

namespace App\Http\Controllers\BackgroundManagement;

use App\Advice;
use App\AppResponse;
use App\Assessment;
use App\AssessmentStaffFinalScore;
use App\Http\Controllers\Controller;
use App\StaffScore;
use Illuminate\Http\Request;

class TheAssessmentsManagementController extends Controller
{

    //获取全部assessment
    public function index (){
        $assessments = Assessment::orderBy('year', "DESC")->orderBy("month", "DESC")->get();
        return $assessments;
    }

    //改变assessment状态
    public function update ($id, Request $request){
        $assessment = Assessment::find($id)->update(['is_completed' => $request->get('is_completed')]);
//        if($request->get('is_completed') == 0){
//            \DB::beginTransaction();
//            try{
//                AssessmentStaffFinalScore::where('assessment_id', $id)->delete();
//                \DB::commit();
//            }catch(Exception $exception){
//                \DB::rollback();
//                return AppResponse::result(false, $exception->getMessage());
//            }
//        }

        if($request->get('is_completed') == 1){  //计算出最终得分并写入
            $this->getFinalScore($id);
        }
    }

    //删除assessment
    public function destroy($id){
//        $assessemnt = Assessment::find($id)->delete();
        \DB::beginTransaction();
        try{
            StaffScore::where('assessment_id', $id)->delete();
            AssessmentStaffFinalScore::where('assessment_id', $id)->delete();
            Assessment::destroy($id);
            \DB::commit();
            return AppResponse::result(true);
        }catch(Exception $exception){
            \DB::rollback();
            return AppResponse::result(false, $exception->getMessage());
        }

    }

    //获取完成的最后得分
    public function getFinalScore($id){
        $staffScores = StaffScore::select([
            \DB::raw('assessments.id as assessment_id'),
            'assessments.year',
            'assessments.month',
            'staff_scores.staff_user_id',
            \DB::raw("sum(
                                 (if(staff_scores.quality_score is null, 0, staff_scores.quality_score) + if(staff_scores.attitude_score is null, 0, staff_scores.attitude_score)
                                 )*if(staff_scores.percentage is null, 0, staff_scores.percentage)*0.01
                          )as sumScore"),
            \DB::raw("sum(
                                 if(staff_scores.quality_score is null, 0, staff_scores.quality_score
                                 )* if(staff_scores.percentage is null, 0, staff_scores.percentage) * 0.01
                          )as sum_quality_score"),
            \DB::raw("sum(
                                 if(staff_scores.attitude_score is null, 0, staff_scores.attitude_score
                                 ) * if(staff_scores.percentage is null, 0, staff_scores.percentage) * 0.01
                          ) as sum_attitude_score")
        ])->leftJoin('assessments', 'assessments.id', '=' ,'staff_scores.assessment_id')
            ->whereIn('assessments.id', [$id])
            ->groupBy('staff_scores.staff_user_id')
            ->get();

        foreach($staffScores as $staffScore){    //把合理化建议分数加入
            $staffScore->tempDate = $staffScore['year'];
            if($staffScore['month'] < 10){
                $staffScore['tempDate'] = $staffScore['tempDate'].'-0'.$staffScore['month'];
            }else{
                $staffScore['tempDate'] = $staffScore['tempDate'].'-'.$staffScore['month'];
            }
            $advice = Advice::select([
                \DB::raw("if(sum(score) > 30, 30, sum(score)) as sumScore"),
                "advices.suggest_user_id"
            ])
                ->whereRaw("date_format(created_at, '%Y-%m') = '".$staffScore['tempDate']."'")
                ->whereIn('advices.suggest_user_id',[$staffScore['staff_user_id']])
                ->groupBy("advices.suggest_user_id")
                ->first();

            $staffScore['sumScore'] = $advice['sumScore'] + $staffScore['sumScore'];
            $staffScore->advices_sum_score = $advice['sumScore'];

            //相关的数据填入新的数据表中
            $count = AssessmentStaffFinalScore::where('assessment_id', $staffScore['assessment_id'])->where('user_id', $staffScore['staff_user_id'])->count();
            if($count > 0){
                AssessmentStaffFinalScore::where('assessment_id', $staffScore['assessment_id'])->where('user_id', $staffScore['staff_user_id'])->update(['sum_score'=>$staffScore['sumScore'], 'quality_score'=>$staffScore['sum_quality_score'],
                    'attitude_score'=>$staffScore['sum_attitude_score'], 'advices_score'=>$staffScore['advices_sum_score']
                ]);
            }else{
                AssessmentStaffFinalScore::create([ 'user_id'=>$staffScore['staff_user_id'], 'assessment_id'=>$staffScore['assessment_id'],
                    'sum_score'=>$staffScore['sumScore'], 'quality_score'=>$staffScore['sum_quality_score'],
                    'attitude_score'=>$staffScore['sum_attitude_score'], 'advices_score'=>$staffScore['advices_sum_score'],
                    'assessment_date'=>$staffScore['tempDate'].'-01'
                ]);
            }
        }
    }
}