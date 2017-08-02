<?php

namespace App\Http\Controllers\BackgroundManagement;

use App\AppResponse;
use App\Assessment;
use App\Http\Controllers\Controller;
use App\StaffScore;
use Illuminate\Http\Request;

class TheAssessmentsManagementController extends Controller
{
    public function __construct(){
        $this->middleware('admin');
    }

    //获取全部assessment
    public function index (){
        $assessments = Assessment::orderBy('year', "DESC")->orderBy("month", "DESC")->get();
        return $assessments;
    }

    //改变assessment状态
    public function update ($id, Request $request){
        $assessment = Assessment::find($id)->update(['is_completed' => $request->get('is_completed')]);
    }

    //删除assessment
    public function destroy($id){
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