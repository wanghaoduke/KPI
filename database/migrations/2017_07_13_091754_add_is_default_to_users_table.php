<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class AddIsDefaultToUsersTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        if(!Schema::hasColumn('users', 'is_default_plan')){
            Schema::table("users", function(Blueprint $table){
                $table->integer('is_default_plan')->default(0)->comment("是否是策划组默认的评审员 0位否 1为是");  //是否是策划组默认的评审员 0位否 1为是
                $table->integer('is_default_development')->default(0)->comment("是否是默认的评论员 0是否 1为是");  //是否是默认的评论员 0是否 1为是
                $table->integer('percentage_plan')->nullable()->comment("默认的策划组百分比");  //默认的策划组百分比
                $table->integer('percentage_development')->nullable()->comment("默认的开发组百分比");  //默认的开发组百分比
            });
        }

        DB::table('users')->whereIn('department', [1,2])->update(['is_default_plan'=>1]);
        DB::table('users')->whereIn('department', [1,2])->update(['is_default_development'=>1]);
    }
    
    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('users', function (Blueprint $table) {
            //
            $table->dropColumn('is_default_plan');
            $table->dropColumn('is_default_development');
        });
    }
}
