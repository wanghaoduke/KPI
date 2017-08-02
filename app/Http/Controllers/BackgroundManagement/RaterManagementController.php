<?php

namespace App\Http\Controllers\BackgroundManagement;

use App\AppResponse;
use App\Http\Controllers\Controller;
use App\User;
use Illuminate\Http\Request;

class RaterManagementController extends Controller
{

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

    //获取全部员工信息 不包括离职的和已经被选
    public function getAllStaffsNoLeave(Request $request){
//        \Log::info($request->all());
        if($request->has('selectedIds')){
            $selectedIds = $request->get('selectedIds');
//            \Log::info($selectedIds);
        }
        $staffs = User::select()->where('is_admin', 0)->where('status', 1)->whereNotIn('id', $selectedIds)->get();
        return $staffs;
    }
    
}