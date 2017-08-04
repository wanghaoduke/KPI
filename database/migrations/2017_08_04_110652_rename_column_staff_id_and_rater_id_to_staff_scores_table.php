<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class RenameColumnStaffIdAndRaterIdToStaffScoresTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('staff_scores', function (Blueprint $table) {
            //
            $table->renameColumn('staff_id', 'staff_user_id');
            $table->renameColumn('rater_id', 'rater_user_id');
        });
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
            $table->renameColumn('staff_user_id', 'staff_id');
            $table->renameColumn('rater_user_id', "rater_id");
        });
    }
}
