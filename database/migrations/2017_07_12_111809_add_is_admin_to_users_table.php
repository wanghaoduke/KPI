<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class AddIsAdminToUsersTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        if(!Schema::hasColumn('users', 'is_admin')){
            Schema::table('users', function (Blueprint $table) {
                //
                $table->integer('is_admin')->default(0);   //是否是管理员
            });
        }
        $password = 111111;
        $newPassword = bcrypt($password);
        DB::table('users')->insert(['name'=>'管理员','phone'=>'12222222222','password'=>$newPassword,'department'=>1,'Jurisdiction'=>1,'status'=>1,'is_admin'=>1]);
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
            $table->dropColumn('is_admin');
        });
        DB::table('users')->where('phone','=','12222222222')->delete();
    }
}
