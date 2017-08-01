<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class AddQualityAndAttitudeToStaffScoresTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        if(!Schema::hasColumn('staff_scores', 'quality_score')){
            Schema::table("staff_scores", function(Blueprint $table){
                $table->integer("quality_score")->nullable()->comment("质量的评分");
            });
        }
        if(!Schema::hasColumn('staff_scores', 'attitude_score')){
            Schema::table("staff_scores", function(Blueprint $table){
                $table->integer("attitude_score")->nullable()->comment("态度的评分");
            });
        }
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('staff_scores', function (Blueprint $table) {
            //
            $table->dropColumn('quality_score');
            $table->dropColumn('attitude_score');
        });
    }
}
