<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateAdvicesTable extends Migration
{
    /**
     * Run the migrations.
     *用来记录提出的合理化建议及给予的处理结果的表
     * @return void
     */
    public function up()
    {
        Schema::create('advices', function (Blueprint $table) {
            $table->increments('id');
            $table->string('title')->comment('建议的标题');
            $table->text('content')->comment('建议的内容');
            $table->integer('suggest_id')->unsigned()->comment('提出建议人');
            $table->foreign('suggest_id')->references('id')->on('users');
            $table->integer("rater_id")->unsigned()->comment("建议评论人");
            $table->foreign("rater_id")->references('id')->on('users');
            $table->tinyInteger("is_processed")->default(0)->comment("是否处理了");
            $table->tinyInteger("is_accept")->default(0)->comment("是否接纳建议");
            $table->tinyInteger("score")->nullable()->comment("给分");
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
        Schema::dropIfExists('advices');
    }
}
