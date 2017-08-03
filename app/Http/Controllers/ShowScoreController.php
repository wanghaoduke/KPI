<?php

namespace App\Http\Controllers;

use App\AppResponse;
use App\Assessment;
use App\StaffScore;
use App\User;
use App\Advice;
use Illuminate\Http\Request;

class ShowScoreController extends Controller
{
    public function __construct(){
        $this->middleware('auth');
    }

    //显示主页
    public function home () {
        $title1 = '评分查询';
        $title2 = null;
        $titleLink1 = '/show_score/home#/';
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
    public function index(Request $request){
//        \Log::info($request->all());
        $startDate = $request->get('startDate');
        $endDate = $request->get('endDate');
        $startDateStr = $request->get('startDateStr');
        $endDateStr = $request->get('endDateStr');
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
            $sql = "(select staff_scores.staff_id,sum(staff_scores.is_completed)as completed_count,
                          sum(
                                 (if(staff_scores.quality_score is null, 0, staff_scores.quality_score) + if(staff_scores.attitude_score is null, 0, staff_scores.attitude_score)
                                 )*if(staff_scores.percentage is null, 0, staff_scores.percentage)*0.01
                          )as sumScore,
                          sum(
                                 (staff_scores.quality_score)*if(staff_scores.percentage is null, 0, staff_scores.percentage)*0.01
                          )as qualitySumScore,
                          sum(
                                 (staff_scores.attitude_score)*if(staff_scores.percentage is null, 0, staff_scores.percentage)*0.01
                          )as attitudeSumScore,
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
                \DB::raw("(case when users.department = 3 then '策划组' when users.department = 4 then '开发组' end) as department"),
                'tt.staff_id as staff_id',
                'tt.sumScore',
                'tt.qualitySumScore',
                'tt.attitudeSumScore',
                'tt.abilitySumScore',
                'tt.responsibilitySumScore',
                'tt.prototypeSumScore',
                'tt.finishedProductSumScore',
                'tt.developmentQualitySumScore',
                'tt.developEfficiencySumScore'
            ])->leftJoin(\DB::raw($sql), 'tt.staff_id', '=', 'users.id')
                ->where('tt.completed_count', '>', 0)
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
//            switch($request->get("item")){
//                case "all":
//                    $data = $data->orderBy("tt.sumScore", "DESC");
//                    break;
//                case "prototype":
//                    $data = $data->orderBy("tt.prototypeSumScore", "DESC");
//                    break;
//                case "finishedProduct":
//                    $data = $data->orderBy("tt.finishedProductSumScore", "DESC");
//                    break;
//                case "developmentQuality":
//                    $data = $data->orderBy("tt.developmentQualitySumScore", "DESC");
//                    break;
//                case "developEfficiency":
//                    $data = $data->orderBy("tt.developEfficiencySumScore", "DESC");
//                    break;
//                case "ability":
//                    $data = $data->orderBy("tt.abilitySumScore", "DESC");
//                    break;
//                case "responsibility":
//                    $data = $data->orderBy("tt.responsibilitySumScore", "DESC");
//                    break;
//                default:
//                    $data = $data->orderBy("tt.sumScore", "DESC");
//            }

            $data = $data->get();
            //把合理化建议分数加入
            $advices = Advice::select([
                \DB::raw("sum(score) as sumScore"),
                "advices.suggest_id"
            ])
                ->whereRaw("date_format(created_at, '%Y-%m') >= '$startDateStr' and date_format(created_at, '%Y-%m') <= '$endDateStr'")
                ->groupBy("advices.suggest_id")
                ->get();
//            \Log::info($advices);
            for($i = 0; $i < count($advices); $i++){
                if($advices[$i]["sumScore"] > 30){
                    $advices[$i]["sumScore"] = 30;
                }
                for($j = 0; $j < count($data); $j++){
                    if($advices[$i]['suggest_id'] == $data[$j]['id']){
                        $data[$j]["sumScore"] = $data[$j]["sumScore"] + $advices[$i]["sumScore"];
                        $data[$j]->advicesSumScore = $advices[$i]["sumScore"];
                    }
                }
            }

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

                if($data[$i]['qualitySumScore']){
                    $data[$i]->advicesAvgScore = round($data[$i]['advicesSumScore'] / $countAssessment, 2);
                    $data[$i]->advicesSumScore = round($data[$i]['advicesSumScore'], 2);
                }else{
                    $data[$i]->advicesAvgScore = null;
                }

                if($data[$i]['qualitySumScore']){
                    $data[$i]->qualityAvgScore = round($data[$i]['qualitySumScore'] / $countAssessment, 2);
                    $data[$i]->qualitySumScore = round($data[$i]['qualitySumScore'], 2);
                }else{
                    $data[$i]->qualityAvgScore = null;
                }

                if($data[$i]['attitudeSumScore']){
                    $data[$i]->attitudeAvgScore = round($data[$i]['attitudeSumScore'] / $countAssessment, 2);
                    $data[$i]->attitudeSumScore = round($data[$i]['attitudeSumScore'], 2);
                }else{
                    $data[$i]->attitudeAvgScore = null;
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

            //不同分项目的不同排序
            switch($request->get("item")){
                case "all":
                    for($i = 0; $i < count($data); $i++){
                        for($j = $i + 1; $j < count($data); $j++){
                            $tempData = [];
                            if($data[$i]['avgScore'] < $data[$j]['avgScore']){
                                $tempData = $data[$j];
                                $data[$j] = $data[$i];
                                $data[$i] = $tempData;
                            }
                        }
                    }
                    break;
                case "advices":
                    for($i = 0; $i < count($data); $i++){
                        for($j = $i + 1; $j < count($data); $j++){
                            $tempData = [];
                            if($data[$i]['advicesAvgScore'] < $data[$j]['advicesAvgScore']){
                                $tempData = $data[$j];
                                $data[$j] = $data[$i];
                                $data[$i] = $tempData;
                            }
                        }
                    }
                    break;
                case "quality":
                    for($i = 0; $i < count($data); $i++){
                        for($j = $i + 1; $j < count($data); $j++){
                            $tempData = [];
                            if($data[$i]['qualityAvgScore'] < $data[$j]['qualityAvgScore']){
                                $tempData = $data[$j];
                                $data[$j] = $data[$i];
                                $data[$i] = $tempData;
                            }
                        }
                    }
                    break;
                case "attitude":
                    for($i = 0; $i < count($data); $i++){
                        for($j = $i + 1; $j < count($data); $j++){
                            $tempData = [];
                            if($data[$i]['attitudeAvgScore'] < $data[$j]['attitudeAvgScore']){
                                $tempData = $data[$j];
                                $data[$j] = $data[$i];
                                $data[$i] = $tempData;
                            }
                        }
                    }
                    break;
                case "prototype":
                    for($i = 0; $i < count($data); $i++){
                        for($j = $i + 1; $j < count($data); $j++){
                            $tempData = [];
                            if($data[$i]['prototypeAvgScore'] < $data[$j]['prototypeAvgScore']){
                                $tempData = $data[$j];
                                $data[$j] = $data[$i];
                                $data[$i] = $tempData;
                            }
                        }
                    }
                    break;
                case "finishedProduct":
                    for($i = 0; $i < count($data); $i++){
                        for($j = $i + 1; $j < count($data); $j++){
                            $tempData = [];
                            if($data[$i]['finishedProductAvgScore'] < $data[$j]['finishedProductAvgScore']){
                                $tempData = $data[$j];
                                $data[$j] = $data[$i];
                                $data[$i] = $tempData;
                            }
                        }
                    }
                    break;
                case "developmentQuality":
                    for($i = 0; $i < count($data); $i++){
                        for($j = $i + 1; $j < count($data); $j++){
                            $tempData = [];
                            if($data[$i]['developmentQualityAvgScore'] < $data[$j]['developmentQualityAvgScore']){
                                $tempData = $data[$j];
                                $data[$j] = $data[$i];
                                $data[$i] = $tempData;
                            }
                        }
                    }
                    break;
                case "developEfficiency":
                    for($i = 0; $i < count($data); $i++){
                        for($j = $i + 1; $j < count($data); $j++){
                            $tempData = [];
                            if($data[$i]['developEfficiencyAvgScore'] < $data[$j]['developEfficiencyAvgScore']){
                                $tempData = $data[$j];
                                $data[$j] = $data[$i];
                                $data[$i] = $tempData;
                            }
                        }
                    }
                    break;
                case "ability":
                    for($i = 0; $i < count($data); $i++){
                        for($j = $i + 1; $j < count($data); $j++){
                            $tempData = [];
                            if($data[$i]['abilityAvgScore'] < $data[$j]['abilityAvgScore']){
                                $tempData = $data[$j];
                                $data[$j] = $data[$i];
                                $data[$i] = $tempData;
                            }
                        }
                    }
                    break;
                case "responsibility":
                    for($i = 0; $i < count($data); $i++){
                        for($j = $i + 1; $j < count($data); $j++){
                            $tempData = [];
                            if($data[$i]['responsibilityAvgScore'] < $data[$j]['responsibilityAvgScore']){
                                $tempData = $data[$j];
                                $data[$j] = $data[$i];
                                $data[$i] = $tempData;
                            }
                        }
                    }
                    break;
                default:
                    for($i = 0; $i < count($data); $i++){
                        for($j = $i + 1; $j < count($data); $j++){
                            $tempData = [];
                            if($data[$i]['avgScore'] < $data[$j]['avgScore']){
                                $tempData = $data[$j];
                                $data[$j] = $data[$i];
                                $data[$i] = $tempData;
                            }
                        }
                    }
            }
            \Log::info($data->toArray());
            return AppResponse::result(true, $data);
        }else{
            return AppResponse::result(false, '没有任何数据，请先增加考评或者换查询时间段！');
        }
    }
}