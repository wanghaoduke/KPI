<?php

namespace App\Http\Controllers;

use App\Advice;
use App\AppResponse;
use App\Assessment;
use App\StaffScore;
use App\User;
use Illuminate\Support\Facades\Gate;
use Illuminate\Http\Request;

class AssessmentController extends Controller
{

//    public function __construct(){
//        $this->middleware('auth');
//    }

//    显示主页面
    public function index(){
        $title1 = '考核管理';
        $title2 = null;
        $titleLink1 = '/assessment#/';
        $titleLink2 = null;
        if(Gate::denies('assessment_manage')){
            return redirect('/')->withErrors(['gateError' => '您没有进入的权限！']);
        }
        return view('kpiManage', compact('title1','title2','titleLink1','titleLink2'));
    }

    //创建新的kpi考核
//    public function createAssessment($id, Request $request){
//        $title1 = '考核管理';
//        $title2 = '添加考核';
//        $titleLink1 = '/assessment_manage';
//        $titleLink2 = '/create_assessment/' + $id;
//        $planUsers = User::where('Jurisdiction', 0)->where('department', 3)->where('status', 1)->get();
//        $developmentUsers = User::where('Jurisdiction', 0)->where('department', 4)->where('status', 1)->get();
//        return view('createAssessment', compact('title1','title2','titleLink1','titleLink2','planUsers','developmentUsers','id'));
//    }

    //添加月份assessment
    public function store(Request $request){
//        \Log::info($request->all());
        $count = Assessment::where('year', $request->get('year'))->where('month', $request->get('month'))->count();
//        \Log::info($count);
        if($count > 0){
            $data = [];
            $data['error'] = '月份有重复！';
            return $data;
        }
        $assessment = Assessment::create($request->all());
        $staffs = User::whereIn('department', [3,4])->where('status', 1)->get();
        $planRaters = User::where('is_default_plan',1)->where('status', 1)->get();
        $developmentRaters = User::where('is_default_development',1)->where('status', 1)->get();

        if($staffs->count() > 0){
            foreach ($staffs as $staff){
                if($staff['department'] == 3){
                    foreach ($planRaters as $rater){
                        $staffScore = [];
                        $staffScore['assessment_id'] = $assessment->id;
                        $staffScore['staff_user_id'] = $staff->id;
                        $staffScore['rater_user_id'] = $rater->id;
                        $staffScore['percentage'] = $rater->percentage_plan;
                        $createStaffScore = StaffScore::create($staffScore);

                    }
                }
                if($staff['department'] == 4){
                    foreach ($developmentRaters as $rater){
                        $staffScore = [];
                        $staffScore['assessment_id'] = $assessment->id;
                        $staffScore['staff_user_id'] = $staff->id;
                        $staffScore['rater_user_id'] = $rater->id;
                        $staffScore['percentage'] = $rater->percentage_development;
                        $createStaffScore = StaffScore::create($staffScore);

                    }
                }
            }
        }else{
            $data = [];
            $data['error'] = '要先创建被评的员工！';
            return $data;
        }
        return $assessment;
    }

    //更改考核的状态
    public function update($id){
//        \Log::info($id);
        $assessment = Assessment::where('id', $id);
//        Assessment::where('id', $id)->update(['is_completed' => 1]);
        $this->getFinalScore($id);
        return AppResponse::result($assessment->update(['is_completed' => 1]));
    }

    //获取所有的assessment
    public function getAllAssessments (){
        $assessments = Assessment::select([
            'assessments.year',
            'assessments.month',
            'assessments.is_completed',
            'assessments.created_at',
            'assessments.updated_at',
            'assessments.id',
            \DB::raw("(select count(distinct(rater_user_id)) 
                         from staff_scores
                         where staff_scores.assessment_id = assessments.id
                         group by staff_scores.assessment_id 
                      ) as count_rater"),
            \DB::raw("(select count(distinct(staff_scores.rater_user_id)) from staff_scores
                         where staff_scores.assessment_id = assessments.id
                         and staff_scores.is_completed = 0) as count_no_give_rater")
        ])
            ->orderBy('year','DESC')
            ->orderBy('month','DESC')
            ->get();
        $advices = Advice::select([
            '*',
            \DB::raw("date_format(created_at, '%Y')as year"),
            \DB::raw("date_format(created_at, '%m')as month"),
        ])->where("advices.is_processed", 0)
            ->get();

        $countArray = [];
        for($i = 0; $i < count($assessments); $i++){
            $countArray[$i] = 0;
            for($j = 0; $j < count($advices); $j++){
                if((intval($assessments[$i]['year']) == intval($advices[$j]['year'])) && (intval($assessments[$i]['month']) == intval($advices[$j]['month']))){
                    $countArray[$i] = $countArray[$i] + 1;
                }
            }
            $assessments[$i]->not_processed_advices_count = $countArray[$i];
        }
        return $assessments;
    }

    //获取assessment详情
    public function show ($id, Request $request){
        $raters = User::whereIn('department',[1,2])->where('status', 1)->get();
        $staffIds = StaffScore::distinct('staff_user_id')->where('assessment_id', $id)->pluck('staff_user_id');
       
        $planStaffs = User::with(['staffScores' => function($query) use ($id){
            $query->select([
                'staff_scores.id',
                'staff_scores.rater_user_id',
                'staff_scores.staff_user_id',
                'staff_scores.percentage',
                'users.name',
            ])->leftJoin('users', 'users.id', '=', 'staff_scores.rater_user_id')
                ->where('assessment_id', $id);
        }
        ])->whereIn('users.id', $staffIds)
            ->where('department', 3)
            ->get();

        for($i = 0; $i < count($planStaffs); $i++){
            $sumPercentage = 0;
            for($j = 0; $j < count($planStaffs[$i]['staffScores']); $j++){
                $sumPercentage = $sumPercentage + $planStaffs[$i]['staffScores'][$j]['percentage'];
            }
            if($sumPercentage == 100){
                $planStaffs[$i]->isAdded = true;
            }else{
                $planStaffs[$i]->isAdded = false;
            }
        }
        
        $developmentStaffs = User::with(['staffScores' => function($query) use ($id){
            $query->select([
                'staff_scores.id',
                'staff_scores.rater_user_id',
                'staff_scores.staff_user_id',
                'staff_scores.percentage',
                'users.name',
            ])
                ->leftJoin('users','users.id','=','staff_scores.rater_user_id')
                ->where('assessment_id', $id);
        }
        ])->whereIn('users.id', $staffIds)
            ->where('department', 4)
            ->get();

        for($i = 0; $i < count($developmentStaffs); $i++){
            $sumPercentage = 0;
            for($j = 0; $j < count($developmentStaffs[$i]['staffScores']); $j++){
                $sumPercentage = $sumPercentage + $developmentStaffs[$i]['staffScores'][$j]['percentage'];
            }
            if($sumPercentage == 100){
                $developmentStaffs[$i]->isAdded = true;
            }else{
                $developmentStaffs[$i]->isAdded = false;
            }
        }

        $assessment = Assessment::where('id', $id)->first();

        return [$planStaffs, $developmentStaffs, $raters, $assessment];
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
            \DB::table('assessment_staff_final_scores')->insert([ 'user_id'=>$staffScore['staff_user_id'], 'assessment_id'=>$staffScore['assessment_id'],
                'sum_score'=>$staffScore['sumScore'], 'quality_score'=>$staffScore['sum_quality_score'],
                'attitude_score'=>$staffScore['sum_attitude_score'], 'advices_score'=>$staffScore['advices_sum_score'],
                'assessment_date'=>$staffScore['tempDate'].'-01'
            ]);
        }
    }
 
}