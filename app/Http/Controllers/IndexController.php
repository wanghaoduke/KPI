<?php

namespace App\Http\Controllers;

use App\Advice;
use App\Assessment;
use App\StaffScore;
use App\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class IndexController extends Controller
{
    public function index () {
        $title1 = '首页';
        $title2 = null;
        $titleLink1 = '/';
        $titleLink2 = null;

        $assessment = Assessment::where('is_completed', 1)->orderBy('year',"DESC")->orderBy('month', "DESC")->first();
//        \Log::info($assessment);
        if($assessment){
            //策划组
            $planStaffs = $staffIds = StaffScore::leftJoin('users', 'users.id', '=', 'staff_scores.staff_id')
                ->distinct()
                ->where('staff_scores.assessment_id', $assessment->id)
                ->where("users.department", 3)
                ->pluck('staff_scores.staff_id');
            $showPlanStaffCount = round(count($planStaffs) / 3);
            $planScores = User::select([
                "users.id",
                "users.name",
                "tt.score",
                "tt.completed_count"
            ])->leftJoin(\DB::raw("(select staff_scores.staff_id, sum(staff_scores.is_completed)as completed_count, 
                                    sum(
                                      (
                                          if(staff_scores.quality_score is null, 0, staff_scores.quality_score) + if(staff_scores.attitude_score is null, 0, staff_scores.attitude_score)
                                      )*if(staff_scores.percentage is null, 0, staff_scores.percentage)*0.01
                                    )as score
                                   from staff_scores 
                                   where staff_scores.assessment_id = '$assessment->id' group by staff_scores.staff_id
                                 ) as tt"),
                'tt.staff_id', '=', 'users.id')
                ->where('users.department',3)
                ->where('tt.completed_count', '>', 0)
                ->orderBy('tt.score',"DESC")
//                ->limit($showPlanStaffCount)
                ->get();
//        \Log::info($planScores->toArray());
            $dateStr = $assessment->year;
            if($assessment->month < 10){
                $dateStr = strval($dateStr)."-0".$assessment->month;
            }else{
                $dateStr = strval($dateStr)."-".$assessment->month;
            }
            $advices = Advice::select([
                \DB::raw("sum(score) as sumScore"),
                "advices.suggest_id"
            ])
                ->whereRaw("date_format(created_at, '%Y-%m')= '$dateStr'")
                ->groupBy("advices.suggest_id")
                ->get();
//            \Log::info($advices);
//            \Log::info($planScores->toArray());
            for($i = 0; $i < count($advices); $i++){
                if($advices[$i]["sumScore"] > 30){
                    $advices[$i]["sumScore"] = 30;
                }
                for($j = 0; $j < count($planScores); $j++){
                    $planScores[$j]["score"] = round($planScores[$j]["score"], 2);
                    if($advices[$i]['suggest_id'] == $planScores[$j]['id']){
                        $planScores[$j]["score"] = round($planScores[$j]["score"] + $advices[$i]["sumScore"], 2);
                    }
                }
            }

            for($n = 0 ; $n < count($planScores); $n++){
                for($m = 0 ; $m < count($planScores) - $n - 1; $m++){
                    if(floatval($planScores[$m]['score']) < floatval($planScores[$m+1]['score'])){
                        $tempScore = $planScores[$m];
                        $planScores[$m] = $planScores[$m+1];
                        $planScores[$m+1] = $tempScore;
                    }
                }
            }
            $planScores = array_slice($planScores->toArray(), 0, $showPlanStaffCount);

            //开发组
            $developmentStaff = $staffIds = StaffScore::leftJoin('users', 'users.id', '=', 'staff_scores.staff_id')
                ->distinct()
                ->where('staff_scores.assessment_id', $assessment->id)
                ->where("users.department", 4)
                ->pluck('staff_scores.staff_id');
            $showDevelopmentStaffCount = round(count($developmentStaff)/3);
            $developmentScores = User::select([
                "users.id",
                "users.name",
                "tt.score",
                "tt.completed_count"
            ])->leftJoin(\DB::raw("(select staff_scores.staff_id, sum(staff_scores.is_completed)as completed_count, 
                                    sum(
                                      (
                                          if(staff_scores.quality_score is null, 0, staff_scores.quality_score) + if(staff_scores.attitude_score is null, 0, staff_scores.attitude_score)
                                      )*if(staff_scores.percentage is null, 0, staff_scores.percentage)*0.01
                                    )as score
                                   from staff_scores 
                                   where staff_scores.assessment_id = '$assessment->id' group by staff_scores.staff_id
                                 ) as tt"),
                'tt.staff_id', '=', 'users.id')
                ->where('users.department',4)
                ->where('tt.completed_count', '>', 0)
                ->orderBy('tt.score',"DESC")
//                ->limit($showDevelopmentStaffCount)
                ->get();

//            \Log::info($developmentScores->toArray());
            for($i = 0; $i < count($advices); $i++){
                if($advices[$i]["sumScore"] > 30){
                    $advices[$i]["sumScore"] = 30;
                }
                for($j = 0; $j < count($developmentScores); $j++){
                    $developmentScores[$j]["score"] = round($developmentScores[$j]["score"], 2);
                    if($advices[$i]['suggest_id'] == $developmentScores[$j]['id']){
                        $developmentScores[$j]["score"] = round($developmentScores[$j]["score"] + $advices[$i]["sumScore"], 2);
                    }
                }
            }
//            \Log::info($developmentScores->toArray());

            for($n = 0 ; $n < count($developmentScores); $n++){
                for($m = 0 ; $m < count($developmentScores) - $n - 1; $m++){
                    if(floatval($developmentScores[$m]['score']) < floatval($developmentScores[$m+1]['score'])){
                        $tempScore = $developmentScores[$m];
                        $developmentScores[$m] = $developmentScores[$m+1];
                        $developmentScores[$m+1] = $tempScore;
                    }
                }
            }
            $developmentScores = array_slice($developmentScores->toArray(), 0, $showDevelopmentStaffCount);
        }else{
            $developmentScores = null;
            $planScores = null;
        }

//        \Log::info($developmentScores);
        //判断是否显示评分系统
        if(Auth::check()){
            $count = StaffScore::leftJoin('assessments', 'assessments.id', '=', 'staff_scores.assessment_id')->where('rater_id', auth::user()->id)->where('assessments.is_completed', 0)->count();
        }
        return view('kpiIndex', compact('title1', 'title2', 'titleLink1', 'titleLink2', 'assessment', 'planScores', 'developmentScores', 'count'));
    }

//    public function rulesDown(){
//        return response()->download(realpath(base_path('public')).'/downFiles/rules.docx');
//    }
}