<?php

namespace App\Http\Controllers;

use App\AppResponse;
use App\Assessment;
use App\StaffScore;
use App\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\App;

class AdminController extends Controller
{
    public function index(Request $request){
        $title1 = '首页';
        $title2 = '后台管理页面';
        $titleLink1 = '/';
        $titleLink2 = '#/';
        
        return view('admin', compact('title1','title2','titleLink1','titleLink2'));
    }

    //获取全部员工信息 包括离职的
    public function getAllStaffsWithLeave(){
        $staffs = User::select()->where('is_admin', 0)->get();
        return $staffs;
    }

    //获取全部员工信息 不包括离职的
    public function getAllStaffsNoLeave(Request $request){
//        \Log::info($request->all());
        if($request->has('selectedIds')){
            $selectedIds = $request->get('selectedIds');
//            \Log::info($selectedIds);
        }
        $staffs = User::select()->where('is_admin', 0)->where('status', 1)->whereNotIn('id', $selectedIds)->get();
        return $staffs;
    }

    //添加新的评论人
    public function addNewRaters(Request $request){
//        \Log::info($request->all());
        if($request->get('team') == 'plan'){
            $staffs = User::whereIn('id', $request->get('newAddIds'))->update(['is_default_plan' => 1]);
        }
        if($request->get('team') == 'development'){
            $staffs = User::whereIn('id', $request->get('newAddIds'))->update(['is_default_development' => 1]);
        }
    }

    //改变员工的权限
    public function changeStaffJurisdiction($id, Request $request){
        $staff = User::find($id);
        return AppResponse::result($staff->update(['Jurisdiction' => $request->get('jurisdiction')]));
    }
    
    //改变员工的状态
    public function changeStaffStatus($id, Request $request){
        $staff = User::find($id);
        return AppResponse::result($staff->update(['status' => $request->get('status')]));
    }

    //获得策划组的评论员
    public function getPlanRater(){
        $raters = User::where('is_default_plan', 1)->get();
        return $raters;
    }

    //获取开发组的评论员
    public function getDevelopmentRater(){
        $raters = User::where('is_default_development', 1)->get();
        return $raters;
    }

    //保存默认百分比
    public function saveRaterPercentage($id, Request $request){
//        \Log::info($request->all());
//        \Log::info($id);
        $rater = User::find($id);
        if($request->get('team') == 'plan'){
            $rater->update(['percentage_plan' => $request->get('percentage')]);
        }
        if($request->get('team') == 'development'){
            $rater->update(['percentage_development' => $request->get('percentage')]);
        }
    }

    //改变员工是否是高级管理员
    public function changeStaffIsSeniorManager ($id, Request $request){
        $status = intval($request->get('is_senior_Manager'));
        User::whereNotNull('id')->update(['is_senior_manager' => 0]);
        $staff = User::find($id)->update(['is_senior_manager' => $status]);
    }

    //获取全部assessment
    public function getAllAssessmentsDetail (){
        $assessments = Assessment::orderBy('year', "DESC")->orderBy("month", "DESC")->get();
        return $assessments;
    }

    //改变assessment状态
    public function changeAssessmentCompleted ($id, Request $request){
        $assessment = Assessment::find($id)->update(['is_completed' => $request->get('is_completed')]);
    }

    //删除默认的评论员
    public function deleteDefaultRater($id, Request $request){
//        \Log::info($request->all());
//        \Log::info($id);
        $rater = User::find($id);
        if($request->get('team') == 'plan'){
            $rater->update(['percentage_plan' => null, 'is_default_plan' => 0]);
        }
        if($request->get('team') == 'development'){
            $rater->update(['percentage_development' => null, 'is_default_development' => 0]);
        }
    }

    //删除assessment
    public function deleteAssessment($id){
//        $assessemnt = Assessment::find($id)->delete();
        \DB::beginTransaction();
        try{
            StaffScore::where('assessment_id', $id)->delete();
            Assessment::destroy($id);
            \DB::commit();
            return AppResponse::result(true);
        }catch(Exception $exception){
            \DB::rollback();
            return AppResponse::result(false, $exception->getMessage());
        }

    }
}