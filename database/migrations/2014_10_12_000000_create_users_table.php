<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateUsersTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('users', function (Blueprint $table) {
            $table->increments('id');
            $table->string('name');
//            $table->string('email')->unique();
            $table->string('phone','32')->unique();
            $table->string('password');
            $table->tinyInteger('department');  //部门 存1234数字 因为已经被写死
            $table->tinyInteger('Jurisdiction')->default(0);  //权限 就两个 也被写死 默认为0
            $table->tinyInteger('status')->default(1);  //员工的状态 1为在职 0为离职
            $table->rememberToken();
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
        Schema::dropIfExists('users');
    }
}
