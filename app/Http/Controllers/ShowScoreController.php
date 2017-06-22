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
        \Log::info($request->all());
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
                    $data = $staffScores;
                    break;
                case 'plan':
                    $data = $staffScores->where('users.department','3');
                    break;
                case 'development':
                    $data = $staffScores->where('users.department','4');
                    break;
                default:
                    $data = $staffScores;
            }

            //根据item来排序
            switch($request->get("item")){
                case "all":
                    $data = $data->orderBy("tt.sumScore", "DESC");
                    break;
                case "prototype":
                    $data = $data->orderBy("tt.prototypeSumScore", "DESC");
                    break;
                case "finishedProduct":
                    $data = $data->orderBy("tt.finishedProductSumScore", "DESC");
                    break;
                case "developmentQuality":
                    $data = $data->orderBy("tt.developmentQualitySumScore", "DESC");
                    break;
                case "developEfficiency":
                    $data = $data->orderBy("tt.developEfficiencySumScore", "DESC");
                    break;
                case "ability":
                    $data = $data->orderBy("tt.abilitySumScore", "DESC");
                    break;
                case "responsibility":
                    $data = $data->orderBy("tt.responsibilitySumScore", "DESC");
                    break;
                default:
                    $data = $data->orderBy("tt.sumScore", "DESC");
            }

            $data = $data->get();

            //获取平均分数
            for($i = 0; $i < count($data); $i++){
                $countAssessment = StaffScore::where('staff_id', $data[$i]['staff_id'])->whereIn('assessment_id',$AssessmentIds)->distinct('assessment_id')->count('assessment_id');
//                \Log::info($countAssessment);
                if($data[$i]['sumScore']){
                    $data[$i]->avgScore = round($data[$i]['sumScore'] / $countAssessment, 2);
                    $data[$i]->sumScore = round($data[$i]['sumScore'], 2);
                }else{
                    $data[$i]->avgScore = null;
                }

                if($data[$i]['abilitySumScore']){
                    $data[$i]->abilityAvgScore = round($data[$i]['abilitySumScore'] / $countAssessment, 2);
                    $data[$i]->abilitySumScore = round($data[$i]['abilitySumScore'], 2);
                }else{
                    $data[$i]->abilityAvgScore = null;
                }

                if($data[$i]['responsibilitySumScore']){
                    $data[$i]->responsibilityAvgScore = round($data[$i]['responsibilitySumScore'] / $countAssessment, 2);
                    $data[$i]->responsibilitySumScore = round($data[$i]['responsibilitySumScore'], 2);
                }else{
                    $data[$i]->responsibilityAvgScore = null;
                }

                if($data[$i]['prototypeSumScore']){
                    $data[$i]->prototypeAvgScore = round($data[$i]['prototypeSumScore'] / $countAssessment, 2);
                    $data[$i]->prototypeSumScore = round($data[$i]['prototypeSumScore'], 2);
                }else{
                    $data[$i]->prototypeAvgScore = null;
                }

                if($data[$i]['finishedProductSumScore']){
                    $data[$i]->finishedProductAvgScore = round($data[$i]['finishedProductSumScore'] / $countAssessment, 2);
                    $data[$i]->finishedProductSumScore = round($data[$i]['finishedProductSumScore'], 2);
                }else{
                    $data[$i]->finishedProductAvgScore = null;
                }

                if($data[$i]['developmentQualitySumScore']){
                    $data[$i]->developmentQualityAvgScore = round($data[$i]['developmentQualitySumScore'] / $countAssessment, 2);
                    $data[$i]->developmentQualitySumScore = round($data[$i]['developmentQualitySumScore'], 2);
                }else{
                    $data[$i]->developmentQualityAvgScore = null;
                }
                
                if($data[$i]['developEfficiencySumScore']){
                    $data[$i]->developEfficiencyAvgScore = round($data[$i]['developEfficiencySumScore'] / $countAssessment, 2);
                    $data[$i]->developEfficiencySumScore = round($data[$i]['developEfficiencySumScore'], 2);
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