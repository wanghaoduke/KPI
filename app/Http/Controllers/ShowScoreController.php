<?php

namespace App\Http\Controllers;

use App\AppResponse;
use App\Assessment;
use App\StaffScore;
use App\User;
use Illuminate\Http\Request;

class ShowScoreController extends Controller
{
    public function __construct(){
        $this->middleware('auth');
    }

    //显示主页
    public function index () {
        $title1 = '评分查询';
        $title2 = null;
        $titleLink1 = '/show_score/index#/';
        $titleLink2 = null;
        return view('showScoreIndex', compact('title1','title2','titleLink1','titleLink2'));
    }
    
    //获得最近的assessment
    public function getLastAssessmentDate(){
        $lastAssessment = Assessment::where('is_completed', 1)->orderBy('year',"DESC")->orderBy('month',"DESC")->first();
        if($lastAssessment){
            $lastDate = date("Y-m", strtotime($lastAssessment->year.'-'.$lastAssessment->month));
            return $lastDate;
        }else{
            return AppResponse::result(false, '没有任何数据，请先增加考评！');
        }
    }

    //获取显示数据
    public function getPeriodAllScores(Request $request){
//        \Log::info($request->all());
        $startDate = $request->get('startDate');
        $endDate = $request->get('endDate');
        $AssessmentIds = Assessment::where('is_completed',1)
            ->whereRaw("(assessments.year * 12 + assessments.month) <= '$endDate' and (assessments.year * 12 + assessments.month) >= '$startDate'")
            ->pluck('assessments.id');
//        \Log::info($AssessmentIds);
//        $AssessmentIds = [37,38];
        if(count($AssessmentIds) > 0){
            $assessmentIdString = '(';
            for($i = 0; $i < count($AssessmentIds); $i++){
                if($i == 0){
                    $assessmentIdString = $assessmentIdString.$AssessmentIds[$i];
                }else{
                    $assessmentIdString = $assessmentIdString.','.$AssessmentIds[$i];
                }
                if($i == count($AssessmentIds) - 1){
                    $assessmentIdString = $assessmentIdString.")";
                }
            }
//            \Log::info($assessmentIdString);
            $sql = "(select staff_scores.staff_id,
                          sum(
                                 (if(staff_scores.ability is null, 0, staff_scores.ability) + if(staff_scores.responsibility is null, 0, staff_scores.responsibility) + if(staff_scores.prototype is null, 0,staff_scores.prototype) 
                                      + if(staff_scores.finished_product is null, 0, staff_scores.finished_product)+ if(staff_scores.development_quality is null, 0, staff_scores.development_quality) 
                                      + if(staff_scores.develop_efficiency is null, 0, staff_scores.develop_efficiency)
                                 )*if(staff_scores.percentage is null, 0, staff_scores.percentage)*0.01
                          )as sumScore,
                          sum(
                                 (staff_scores.ability)*if(staff_scores.percentage is null, 0, staff_scores.percentage)*0.01
                          )as abilitySumScore,
                          sum(
                                 (staff_scores.responsibility)*if(staff_scores.percentage is null, 0, staff_scores.percentage)*0.01
                          )as responsibilitySumScore,
                          sum(
                                 (staff_scores.prototype)*if(staff_scores.percentage is null, 0, staff_scores.percentage)*0.01
                          )as prototypeSumScore,
                          sum(
                                 (staff_scores.finished_product)*if(staff_scores.percentage is null, 0, staff_scores.percentage)*0.01
                          )as finishedProductSumScore,
                          sum(
                                 (staff_scores.development_quality)*if(staff_scores.percentage is null, 0, staff_scores.percentage)*0.01
                          )as developmentQualitySumScore,
                          sum(
                                 (staff_scores.develop_efficiency)*if(staff_scores.percentage is null, 0, staff_scores.percentage)*0.01
                          )as developEfficiencySumScore
                      from staff_scores
                      where staff_scores.assessment_id in $assessmentIdString
                      group by staff_scores.staff_id
                     ) as tt";
            $staffScores = User::select([
                'users.id as id',
                'users.name as name',
                \DB::raw("(case when users.department = 3 then '策划部' when users.department = 4 then '开发部' end) as department"),
                'tt.staff_id as staff_id',
                'tt.sumScore',
                'tt.abilitySumScore',
                'tt.responsibilitySumScore',
                'tt.prototypeSumScore',
                'tt.finishedProductSumScore',
                'tt.developmentQualitySumScore',
                'tt.developEfficiencySumScore'
            ])->leftJoin(\DB::raw($sql), 'tt.staff_id', '=', 'users.id')
                ->whereNotNull('tt.staff_id');

            //根据选的组来取数据
            switch($request->get('department')){
                case 'all':
                    $data = $staffScores->get();
                    break;
                case 'plan':
                    $data = $staffScores->where('users.department','3')->get();
                    break;
                case 'development':
                    $data = $staffScores->where('users.department','4')->get();
                    break;
                default:
                    $data = $staffScores->get();
            }

            //获取平均分数
            for($i = 0; $i < count($data); $i++){
                $countAssessment = StaffScore::where('staff_id', $data[$i]['staff_id'])->whereIn('assessment_id',$AssessmentIds)->distinct('assessment_id')->count('assessment_id');
//                \Log::info($countAssessment);
                if($data[$i]['sumScore']){
                    $data[$i]->avgScore = $data[$i]['sumScore'] / $countAssessment;
                }else{
                    $data[$i]->avgScore = null;
                }
                if($data[$i]['abilitySumScore']){
                    $data[$i]->abilityAvgScore = $data[$i]['abilitySumScore'] / $countAssessment;
                }else{
                    $data[$i]->abilityAvgScore = null;
                }
                if($data[$i]['responsibilitySumScore']){
                    $data[$i]->responsibilityAvgScore = $data[$i]['responsibilitySumScore'] / $countAssessment;
                }else{
                    $data[$i]->responsibilityAvgScore = null;
                }
                if($data[$i]['prototypeSumScore']){
                    $data[$i]->prototypeAvgScore = $data[$i]['prototypeSumScore'] / $countAssessment;
                }else{
                    $data[$i]->prototypeAvgScore = null;
                }
                if($data[$i]['finishedProductSumScore']){
                    $data[$i]->finishedProductAvgScore = $data[$i]['finishedProductSumScore'] / $countAssessment;
                }else{
                    $data[$i]->finishedProductAvgScore = null;
                }
                if($data[$i]['developmentQualitySumScore']){
                    $data[$i]->developmentQualityAvgScore = $data[$i]['developmentQualitySumScore'] / $countAssessment;
                }else{
                    $data[$i]->developmentQualityAvgScore = null;
                }
                if($data[$i]['developEfficiencySumScore']){
                    $data[$i]->developEfficiencyAvgScore = $data[$i]['developEfficiencySumScore'] / $countAssessment;
                }else{
                    $data[$i]->developEfficiencyAvgScore = null;
                }
            }
            return AppResponse::result(true, $data);
        }else{
            return AppResponse::result(false, '没有任何数据，请先增加考评或者换查询时间段！');
        }
    }
}