<?php

namespace App\Http\Controllers;

use App\AppResponse;
use App\Assessment;
use App\StaffScore;
use App\User;
use App\Advice;
use Illuminate\Http\Request;

class ShowScoreController extends Controller
{
    public function __construct(){
        $this->middleware('auth');
    }

    //显示主页
    public function home () {
        $title1 = '评分查询';
        $title2 = null;
        $titleLink1 = '/show_score/home#/';
        $titleLink2 = null;
        return view('showScoreIndex', compact('title1','title2','titleLink1','titleLink2'));
    }
    
    //获得最近的assessment
    public function getLastAssessmentDate(){
        $lastAssessment = Assessment::where('is_completed', 1)->orderBy('year',"DESC")->orderBy('month',"DESC")->first();
        if($lastAssessment){
            $lastDate = date("Y-m", strtotime($lastAssessment->year.'-'.$lastAssessment->month));
            return $lastDate;
        }else{
            return AppResponse::result(false, '没有任何数据，请先增加考评！');
        }
    }

    //获取显示数据
    public function index(Request $request){
//        \Log::info($request->all());
        $startDate = $request->get('startDate');
        $endDate = $request->get('endDate');
        $startDateStr = $request->get('startDateStr');
        $endDateStr = $request->get('endDateStr');
        $AssessmentIds = Assessment::where('is_completed',1)
            ->whereRaw("(assessments.year * 12 + assessments.month) <= '$endDate' and (assessments.year * 12 + assessments.month) >= '$startDate'")
            ->pluck('assessments.id');
//        \Log::info($AssessmentIds);
//        $AssessmentIds = [37,38];
        if(count($AssessmentIds) > 0){
            $assessmentIdString = '(';
            for($i = 0; $i < count($AssessmentIds); $i++){
                if($i == 0){
                    $assessmentIdString = $assessmentIdString.$AssessmentIds[$i];
                }else{
                    $assessmentIdString = $assessmentIdString.','.$AssessmentIds[$i];
                }
                if($i == count($AssessmentIds) - 1){
                    $assessmentIdString = $assessmentIdString.")";
                }
            }
            $sql = "(select staff_scores.staff_id,sum(staff_scores.is_completed)as completed_count,
                          sum(
                                 (if(staff_scores.quality_score is null, 0, staff_scores.quality_score) + if(staff_scores.attitude_score is null, 0, staff_scores.attitude_score)
                                 )*if(staff_scores.percentage is null, 0, staff_scores.percentage)*0.01
                          )as sumScore,
                          sum(
                                 (staff_scores.quality_score)*if(staff_scores.percentage is null, 0, staff_scores.percentage)*0.01
                          )as qualitySumScore,
                          sum(
                                 (staff_scores.attitude_score)*if(staff_scores.percentage is null, 0, staff_scores.percentage)*0.01
                          )as attitudeSumScore
                         
                      from staff_scores
                      where staff_scores.assessment_id in $assessmentIdString
                      group by staff_scores.staff_id
                     ) as tt";
            $staffScores = User::select([
                'users.id as id',
                'users.name as name',
                \DB::raw("(case when users.department = 3 then '策划组' when users.department = 4 then '开发组' end) as department"),
                'tt.staff_id as staff_id',
                'tt.sumScore',
                'tt.qualitySumScore',
                'tt.attitudeSumScore'
            ])->leftJoin(\DB::raw($sql), 'tt.staff_id', '=', 'users.id')
                ->where('tt.completed_count', '>', 0)
                ->whereNotNull('tt.staff_id');

            //根据选的组来取数据
            switch($request->get('department')){
                case 'all':
                    $data = $staffScores;
                    break;
                case 'plan':
                    $data = $staffScores->where('users.department','3');
                    break;
                case 'development':
                    $data = $staffScores->where('users.department','4');
                    break;
                default:
                    $data = $staffScores;
            }

            $data = $data->get();

            for($j = 0; $j < count($data); $j++){
                //把合理化建议分数加入
                $advice = Advice::select([
                    \DB::raw("if(sum(score) > 30, 30, sum(score)) as sumScore"),
                    "advices.suggest_id"
                ])
                    ->whereRaw("date_format(created_at, '%Y-%m') >= '$startDateStr' and date_format(created_at, '%Y-%m') <= '$endDateStr'")
                    ->whereIn('advices.suggest_id',[$data[$j]['id']])
                    ->where('advices.score', '>', '0')
                    ->groupBy("advices.suggest_id")
                    ->first();
                $data[$j]['sumScore'] = $advice['sumScore'] + $data[$j]['sumScore'];
                $data[$j]->advicesSumScore = $advice["sumScore"];
                $data[$j] = $this->getTheStaffAvgScore($data[$j], $AssessmentIds);  //获取每个员工的每项平均分
            }
            $data = $this->theScoreOrder($data, $request->get("item"));  //不同分项目的不同排序
            return AppResponse::result(true, $data);
        }else{
            return AppResponse::result(false, '没有任何数据，请先增加考评或者换查询时间段！');
        }
    }

    public function getTheStaffAvgScore($staffScores, $AssessmentIds){
        $countAssessment = StaffScore::where('staff_id', $staffScores['staff_id'])->whereIn('assessment_id',$AssessmentIds)->distinct('assessment_id')->count('assessment_id');

        if($staffScores['sumScore']){
            $staffScores->avgScore = round($staffScores['sumScore'] / $countAssessment, 2);
            $staffScores->sumScore = round($staffScores['sumScore'], 2);
        }else{
            $staffScores->avgScore = null;
        }

        if($staffScores['advicesSumScore']){
            $staffScores->advicesAvgScore = round($staffScores['advicesSumScore'] / $countAssessment, 2);
            $staffScores->advicesSumScore = round($staffScores['advicesSumScore'], 2);
        }else{
            $staffScores->advicesAvgScore = null;
        }

        if($staffScores['qualitySumScore']){
            $staffScores->qualityAvgScore = round($staffScores['qualitySumScore'] / $countAssessment, 2);
            $staffScores->qualitySumScore = round($staffScores['qualitySumScore'], 2);
        }else{
            $staffScores->qualityAvgScore = null;
        }

        if($staffScores['attitudeSumScore']){
            $staffScores->attitudeAvgScore = round($staffScores['attitudeSumScore'] / $countAssessment, 2);
            $staffScores->attitudeSumScore = round($staffScores['attitudeSumScore'], 2);
        }else{
            $staffScores->attitudeAvgScore = null;
        }

        return $staffScores;
    }

    public function theScoreOrder($data, $item){
        $tempItem = $item ? $item : 'all';
        if($tempItem == "all"){
            for($i = 0; $i < count($data); $i++){
                for($j = $i + 1; $j < count($data); $j++){
                    if($data[$i]['avgScore'] < $data[$j]['avgScore']){
                        $tempData = $data[$j];
                        $data[$j] = $data[$i];
                        $data[$i] = $tempData;
                    }
                }
            }
        }else{
            for($i = 0; $i < count($data); $i++){
                for($j = $i + 1; $j < count($data); $j++){
                    if($data[$i][$tempItem."AvgScore"] < $data[$j][$tempItem."AvgScore"]){
                        $tempData = $data[$j];
                        $data[$j] = $data[$i];
                        $data[$i] = $tempData;
                    }
                }
            }
        }

        return $data;
    }
}