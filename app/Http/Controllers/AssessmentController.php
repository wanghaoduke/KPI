<?php

namespace App\Http\Controllers;

use App\Assessment;
use App\StaffScore;
use App\User;
use Illuminate\Support\Facades\Gate;
use Illuminate\Http\Request;

class AssessmentController extends Controller
{

    public function __construct(){
        $this->middleware('auth');
    }

    //显示主页面
    public function show(){
        $title1 = '考核管理';
        $title2 = null;
        $titleLink1 = '/assessment_manage#/';
        $titleLink2 = null;
        if(Gate::denies('assessment_manage')){
            return redirect()->back()->withErrors(['gateError' => '您没有进入的权限！']);
        }
        return view('kpiManage', compact('title1','title2','titleLink1','titleLink2'));
    }

    //创建新的kpi考核
    public function createAssessment($id, Request $request){
        $title1 = '考核管理';
        $title2 = '添加考核';
        $titleLink1 = '/assessment_manage';
        $titleLink2 = '/create_assessment/' + $id;
        $planUsers = User::where('Jurisdiction', 0)->where('department', 3)->where('status', 1)->get();
        $developmentUsers = User::where('Jurisdiction', 0)->where('department', 4)->where('status', 1)->get();
        return view('createAssessment', compact('title1','title2','titleLink1','titleLink2','planUsers','developmentUsers','id'));
    }

    //添加月份assessment
    public function createMonthAssessment(Request $request){
        \Log::info($request->all());
        $count = Assessment::where('year', $request->get('year'))->where('month', $request->get('month'))->count();
        \Log::info($count);
        if($count > 0){
            $data = [];
            $data['error'] = '月份有重复！';
            return $data;
        }
        $assessment = Assessment::create($request->all());
        $staffs = User::where('Jurisdiction', 0)->whereIn('department', [3,4])->where('status', 1)->get();
        $raters = User::whereIn('department',[1,2])->where('status', 1)->get();

        if($staffs->count() > 0){
            foreach ($staffs as $staff){
                foreach ($raters as $rater){
                    $staffScore = [];
                    $staffScore['assessment_id'] = $assessment->id;
                    $staffScore['staff_id'] = $staff->id;
                    $staffScore['rater_id'] = $rater->id;
                    $createStaffScore = StaffScore::create($staffScore);
                }
            }
        }else{
            $data = [];
            $data['error'] = '要先创建被评的员工！';
            return $data;
        }
        return $assessment;
    }

    //获取assessment详情
    /**
     * @param $id
     * @param Request $request
     */
    public function getAssessmentDetail ($id, Request $request){
        $raters = User::whereIn('department',[1,2])->where('status', 1)->get();
        $staffIds = StaffScore::distinct('staff_id')->where('assessment_id', $id)->pluck('staff_id');
       
        $planStaffs = User::with(['staffScores' => function($query) use ($id){
            $query->select([
                'staff_scores.id',
                'staff_scores.rater_id',
                'staff_scores.staff_id',
                'users.name',
            ])->leftJoin('users', 'users.id', '=', 'staff_scores.rater_id')
                ->where('assessment_id', $id);
        }
        ])->whereIn('users.id', $staffIds)
            ->where('department', 3)
            ->get();
        
        $developmentStaffs = User::with(['staffScores' => function($query) use ($id){
            $query->select([
                'staff_scores.id',
                'staff_scores.rater_id',
                'staff_scores.staff_id',
                'users.name',
            ])
                ->leftJoin('users','users.id','=','staff_scores.rater_id')
                ->where('assessment_id', $id);
        }
        ])->whereIn('users.id', $staffIds)
            ->where('department', 4)
            ->get();

        $assessment = Assessment::where('id', $id)->first();

        return [$planStaffs, $developmentStaffs, $raters, $assessment];
    }

    //获取员工的评选人员
    public function getRaters ($id, Request $request){
        \Log::info($request->all());
        $raterIds = StaffScore::where('staff_id', $id)->where('assessment_id', $request->get('assessment_id'))->pluck('rater_id');
        $raters = User::whereIn('id', $raterIds)->get();
        return $raters;
    }

    //获取添加评选的所有员工 不包括离职员工
    public function getAllStaffs (){
        $staffs = User::where('status', 1)->get();
        return $staffs;
    }

    //获取评选人员的详情
    public function getSelectedStaffDetails ($id, Request $request) {
//        \Log::info($request->all());
        $staffs = User::select([
            'users.id as id',
            'users.name as name',
            'staff_scores.staff_id',
            'staff_scores.rater_id',
            'staff_scores.percentage'
        ])
            ->leftJoin('staff_scores', 'users.id', '=', 'staff_scores.rater_id')
            ->where('staff_scores.staff_id', $request->get('staffId'))
            ->where('staff_scores.assessment_id', $id)
            ->get();

        return $staffs;

    }

    //编辑raters
    public function editRaters ($id, Request $request){
//        \Log::info($request->all());
        $selectedIds = [];
        $selectedStaffs = $request->get('selectedStaffs');
        $staffScores = StaffScore::where('assessment_id',$id)
            ->where('staff_id',$request->get('staffId'))
            ->get();

        //添加新记录
        for($i = 0; $i < count($selectedStaffs); $i++){
            $staffScore = StaffScore::where('staff_id',$request->get('staffId'))
                ->where('assessment_id',$id)
                ->where('rater_id',$selectedStaffs[$i]['id'])
                ->get();
//            \Log::info($staffScore);
//            \Log::info($staffScore->count());
            if($staffScore->count() > 0){            //是已存在的记录 则更新
                $tempData = [];
                $tempData['percentage'] = $selectedStaffs[$i]['percentage'];
                $tempStaffScore = StaffScore::findOrFail($staffScore[0]['id']);
                $tempStaffScore->fill($tempData);
                $tempStaffScore->update();
                array_push($selectedIds, $tempStaffScore->id);
            }else{                                 //是不存在的记录则创建
                $tempData = [];
                $tempData['percentage'] = $selectedStaffs[$i]['percentage'];
                $tempData['staff_id'] = $request->get('staffId');
                $tempData['rater_id'] = $selectedStaffs[$i]['id'];
                $tempData['assessment_id'] = $id;
                $tempStaffScore = StaffScore::create($tempData);
                array_push($selectedIds, $tempStaffScore->id);
            }
        }

        //已经去掉的数据 要删除
        $destoryStaffScores = StaffScore::where('staff_id',$request->get('staffId'))
            ->where('assessment_id',$id)
            ->whereNotIn('id',$selectedIds)
            ->delete();
    }
}