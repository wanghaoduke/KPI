<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateStaffScoresTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('staff_scores', function (Blueprint $table) {
            $table->increments('id');
            $table->integer('staff_id')->unsigned()->comment('被评分人');
            $table->foreign('staff_id')->references('id')->on('users');
            $table->integer('rater_id')->unsigned()->comment('品分人');
            $table->foreign('rater_id')->references('id')->on('users');
            $table->integer('assessment_id')->unsigned()->comment('是哪个月份的评分');
            $table->foreign('assessment_id')->references('id')->on('assessments');
            $table->integer('percentage')->nullable()->comment('评分占比');
            $table->integer('is_completed')->default(0);
            $table->integer('ability')->nullable()->comment("学习和能力");
            $table->integer('responsibility')->nullable()->comment("责任心");
            $table->integer('prototype')->nullable()->comment("原型质量");
            $table->integer('finished_product')->nullable()->comment("产品质量");
            $table->integer('development_quality')->nullable()->comment("开发质量");
            $table->integer('develop_efficiency')->nullable()->comment("开发效率");
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('staff_scores');
    }
}
