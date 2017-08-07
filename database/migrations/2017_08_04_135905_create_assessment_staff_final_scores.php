<?php

use App\Advice;
use App\Assessment;
use App\StaffScore;
use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateAssessmentStaffFinalScores extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    
    public function up()
    {
        Schema::create('assessment_staff_final_scores', function (Blueprint $table) {
            $table->increments('id');
            $table->integer('user_id')->unsigned()->comment('得分人');
            $table->foreign('user_id')->references('id')->on('users');
            $table->integer('assessment_id')->unsigned()->comment('关联评分月份');
            $table->foreign('assessment_id')->references('id')->on('assessments');
            $table->decimal('sum_score', 5, 2)->nullable()->comment('总得分');
            $table->decimal('quality_score', 5, 2)->nullable()->comment('质量得分');
            $table->decimal('attitude_score', 5, 2)->nullable()->comment('态度得分');
            $table->decimal('advices_score', 5, 2)->nullable()->comment('合理化建议得分');
            $table->date('assessment_date')->comment('评分的月份');
            $table->timestamps();
        });

        $assessments = Assessment::where('assessments.is_completed', 1)->get(); //已经完成的考评
        foreach( $assessments as $assessment ){    //每个考评的人员分数等数据

            $staffScores = StaffScore::select([
                \DB::raw('assessments.id as assessment_id'),
                'assessments.year',
                'assessments.month',
                'staff_scores.staff_user_id',
                \DB::raw("sum(
                                 (if(staff_scores.quality_score is null, 0, staff_scores.quality_score) + if(staff_scores.attitude_score is null, 0, staff_scores.attitude_score)
                                 )*if(staff_scores.percentage is null, 0, staff_scores.percentage)*0.01
                          )as sumScore"),
                \DB::raw("sum(
                                 if(staff_scores.quality_score is null, 0, staff_scores.quality_score
                                 )* if(staff_scores.percentage is null, 0, staff_scores.percentage) * 0.01
                          )as sum_quality_score"),
                \DB::raw("sum(
                                 if(staff_scores.attitude_score is null, 0, staff_scores.attitude_score
                                 ) * if(staff_scores.percentage is null, 0, staff_scores.percentage) * 0.01
                          ) as sum_attitude_score")
            ])->leftJoin('assessments', 'assessments.id', '=' ,'staff_scores.assessment_id')
                ->whereIn('assessments.id', [$assessment->id])
                ->groupBy('staff_scores.staff_user_id')
                ->get();

            foreach($staffScores as $staffScore){    //把合理化建议分数加入
                $staffScore->tempDate = $staffScore['year'];
                if($staffScore['month'] < 10){
                    $staffScore['tempDate'] = $staffScore['tempDate'].'-0'.$staffScore['month'];
                }else{
                    $staffScore['tempDate'] = $staffScore['tempDate'].'-'.$staffScore['month'];
                }
                $advice = Advice::select([
                    \DB::raw("if(sum(score) > 30, 30, sum(score)) as sumScore"),
                    "advices.suggest_user_id"
                ])
                    ->whereRaw("date_format(created_at, '%Y-%m') = '".$staffScore['tempDate']."'")
                    ->whereIn('advices.suggest_user_id',[$staffScore['staff_user_id']])
                    ->groupBy("advices.suggest_user_id")
                    ->first();

                $staffScore['sumScore'] = $advice['sumScore'] + $staffScore['sumScore'];
                $staffScore->advices_sum_score = $advice['sumScore'];

                //相关的数据填入新的数据表中
                DB::table('assessment_staff_final_scores')->insert([ 'user_id'=>$staffScore['staff_user_id'], 'assessment_id'=>$staffScore['assessment_id'],
                                                                     'sum_score'=>$staffScore['sumScore'], 'quality_score'=>$staffScore['sum_quality_score'],
                                                                     'attitude_score'=>$staffScore['sum_attitude_score'], 'advices_score'=>$staffScore['advices_sum_score'],
                                                                     'assessment_date'=>$staffScore['tempDate'].'-01'
                                                                   ]);
            }
        }
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('assessment_staff_final_scores');
    }
}
