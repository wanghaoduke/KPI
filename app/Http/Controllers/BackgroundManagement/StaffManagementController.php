<?php

namespace App\Http\Controllers\BackgroundManagement;

use App\AppResponse;
use App\Http\Controllers\Controller;
use App\User;
use Illuminate\Http\Request;

class StaffManagementController extends Controller
{

    //获取全部员工信息 包括离职的
    public function index(Request $request){
//        \Log::info(auth()->user()->id);
        $staffs = User::select()->where('id', '!=', auth()->user()->id);
        switch($request->get('status')){
            case 'all':
                $staffs = $staffs->get();
                break;
            case 'onWork':
                $staffs = $staffs->where('status', 1)->get();
                break;
            case 'leave':
                $staffs = $staffs->where('status', 0)->get();
                break;
            default:
                $staffs = $staffs->get();
        }
        return $staffs;
    }

    //更新
    public function update($id, Request $request){
//        \Log::info($request->all());
        $staff = User::find($id);
        if($request->has('is_senior_manager')){
            User::whereNotNull('id')->update(['is_senior_manager' => 0]);
        }
        return AppResponse::result($staff->update($request->all()));
    }

    //更改员工的后台权限
//    public function changeIsAdmin($id, Request $request){
////        \Log::info($id);
////        \Log::info($request->all());
//        $staff = User::find($id)->update(['is_admin' => $request->get('is_admin')]);
//    }

    //后台更改员工的分组
//    public function saveStaffDepartment($id, Request $request){
//        $staff = User::find($id)->update(['department' => $request->get('department')]);
//    }

    //改变员工的权限
//    public function changeStaffJurisdiction($id, Request $request){
//        $staff = User::find($id);
//        return AppResponse::result($staff->update(['Jurisdiction' => $request->get('jurisdiction')]));
//    }

    //改变员工的状态
//    public function changeStaffStatus($id, Request $request){
//        $staff = User::find($id);
//        return AppResponse::result($staff->update(['status' => $request->get('status')]));
//    }

    //改变员工是否是高级管理员
//    public function changeStaffIsSeniorManager ($id, Request $request){
//        $status = intval($request->get('is_senior_Manager'));
//        User::whereNotNull('id')->update(['is_senior_manager' => 0]);
//        $staff = User::find($id)->update(['is_senior_manager' => $status]);
//    }
}