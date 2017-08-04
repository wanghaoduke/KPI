<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class RenameColumnSuggestIdAndRaterIdToAdvicesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('advices', function (Blueprint $table) {
            //
            $table->renameColumn('suggest_id', 'suggest_user_id');
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
        Schema::table('advices', function (Blueprint $table) {
            //
            $table->renameColumn('suggest_user_id', 'suggest_id');
            $table->renameColumn('rater_user_id', 'rater_id');
        });
    }
}
