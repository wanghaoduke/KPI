<?php

namespace App\Http\Controllers;

use App\Assessment;
use App\StaffScore;
use App\User;
use Illuminate\Http\Request;

class IndexController extends Controller
{
    public function index () {
        $title1 = '首页';
        $title2 = null;
        $titleLink1 = '/';
        $titleLink2 = null;

        $assessment = Assessment::where('is_completed', 1)->orderBy('year',"DESC")->orderBy('month', "DESC")->first();
//        \Log::info($assessment);
        //策划组
        $planScores = User::select([
            "users.id",
            "users.name",
            "tt.score"
        ])->leftJoin(\DB::raw("(select staff_scores.staff_id, 
                                    sum(
                                      (if(staff_scores.ability is null, 0, staff_scores.ability) + if(staff_scores.responsibility is null, 0, staff_scores.responsibility) + if(staff_scores.prototype is null, 0,staff_scores.prototype) 
                                      + if(staff_scores.finished_product is null, 0, staff_scores.finished_product)+ if(staff_scores.development_quality is null, 0, staff_scores.development_quality) 
                                      + if(staff_scores.develop_efficiency is null, 0, staff_scores.develop_efficiency))*staff_scores.percentage*0.01
                                    )as score
                                   from staff_scores 
                                   where staff_scores.assessment_id = '$assessment->id' group by staff_scores.staff_id
                                 ) as tt"),
            'tt.staff_id', '=', 'users.id')
            ->where('users.department',3)
            ->orderBy('tt.score',"DESC")
            ->limit(10)
            ->get();
//        \Log::info($planScores);
        //开发组
        $developmentScores = User::select([
            "users.id",
            "users.name",
            "tt.score"
        ])->leftJoin(\DB::raw("(select staff_scores.staff_id, 
                                    sum(
                                      (if(staff_scores.ability is null, 0, staff_scores.ability) + if(staff_scores.responsibility is null, 0, staff_scores.responsibility) + if(staff_scores.prototype is null, 0,staff_scores.prototype) 
                                      + if(staff_scores.finished_product is null, 0, staff_scores.finished_product)+ if(staff_scores.development_quality is null, 0, staff_scores.development_quality) 
                                      + if(staff_scores.develop_efficiency is null, 0, staff_scores.develop_efficiency))*staff_scores.percentage*0.01
                                    )as score
                                   from staff_scores 
                                   where staff_scores.assessment_id = '$assessment->id' group by staff_scores.staff_id
                                 ) as tt"),
            'tt.staff_id', '=', 'users.id')
            ->where('users.department',4)
            ->orderBy('tt.score',"DESC")
            ->limit(10)
            ->get();
//        \Log::info($developmentScores);
        return view('kpiIndex', compact('title1', 'title2', 'titleLink1', 'titleLink2', 'assessment', 'planScores', 'developmentScores'));
    }
}