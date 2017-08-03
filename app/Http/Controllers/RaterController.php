<?php

namespace App\Http\Controllers;

use App\Advice;
use App\AppResponse;
use App\Assessment;
use App\StaffScore;
use App\User;
use Illuminate\Support\Facades\Gate;
use Illuminate\Http\Request;

class RaterController extends Controller
{
    //获取添加评选的所有员工 不包括离职员工
    public function index (){
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
    
    //获取员工的评选人员
    public function getRaters ($id, Request $request){
//        \Log::info($request->all());
        $raterIds = StaffScore::where('staff_id', $id)->where('assessment_id', $request->get('assessment_id'))->pluck('rater_id');
        $raters = User::whereIn('id', $raterIds)->get();
        return $raters;
    }

    
    //编辑raters
    public function update ($id, Request $request){
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