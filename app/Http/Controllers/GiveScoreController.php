<?php

namespace App\Http\Controllers;

use App\Assessment;
use App\StaffScore;
use App\User;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Gate;
use Illuminate\Http\Request;

class GiveScoreController extends Controller
{
    public function __construct(){
        $this->middleware('auth');
    }

    public function show(){
        $title1 = '考核评分首页';
        $title2 = null;
        $titleLink1 = '/score/master#/';
        $titleLink2 = null;
        $count = StaffScore::leftJoin('assessments', 'assessments.id', '=', 'staff_scores.assessment_id')->where('rater_id', auth::user()->id)->where('assessments.is_completed', 0)->count();
//        \Log::info($count);
        if($count == 0){
            return redirect('/')->withErrors(['gateError' => '您没有需要评审的内容！']);
        }
        return view('giveScore', compact('title1','title2','titleLink1','titleLink2'));
    }

    //获取用户的需要评论的assessment
    public function getYourAssessment () {
        $userId = auth::user()->id;
        $assessmentIds = StaffScore::where('rater_id', $userId)->distinct('assessment_id')->pluck('assessment_id');
        \Log::info($assessmentIds);
        $assessments = Assessment::whereIn('id', $assessmentIds)->where('is_completed', 0)->orderBy('year', 'DESC')->orderBy('month', "DESC")->get();
        return $assessments;
    }

    //获取该月份的所有被评详情
    public function getStaffScores ($id) {
//        \Log::info($id);
        $staffScores = StaffScore::select([
            'users.name',
            'staff_scores.rater_id',
            'users.department',
            'staff_scores.id as staff_score_id',
            'assessments.year',
            'assessments.month',
            'staff_scores.ability',
            'staff_scores.responsibility',
            'staff_scores.prototype',
            'staff_scores.finished_product',
            'staff_scores.development_quality',
            'staff_scores.develop_efficiency',
        ])
            ->leftJoin('users', "users.id", "=", "staff_scores.staff_id")
            ->leftJoin('assessments', 'assessments.id', '=', 'staff_scores.assessment_id')
            ->where('assessment_id', $id)
            ->where('rater_id', auth::user()->id)
            ->orderBy('staff_scores.id', "ASC")
            ->get();
        return $staffScores;
    }

    //保存评分记录
    public function saveStaffScores ($id, Request $request){
//        \Log::info($id);
//        \Log::info($request->all());
        $data = [];
        if($request->get('department') == 3){
            $data['prototype'] = $request->get('prototype');
            $data['finished_product'] = $request->get('finished_product');
            $data['ability'] = $request->get('ability');
            $data['responsibility'] = $request->get('responsibility');
            $data['is_completed'] = 1;
        }
        if($request->get('department') == 4){
            $data['development_quality'] = $request->get('development_quality');
            $data['develop_efficiency'] = $request->get('develop_efficiency');
            $data['ability'] = $request->get('ability');
            $data['responsibility'] = $request->get('responsibility');
            $data['is_completed'] = 1;
        }
        $staffScore = StaffScore::findOrFail($request->get('staff_score_id'));
        $staffScore->fill($data);
        $staffScore->update();
    }
}