<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class AddPredictedToRuns extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('runs', function (Blueprint $table) {
		$table->integer('predicted_curve_id')->unsigned()->nullable();

		$table->foreign('predicted_curve_id')->references('id')->on('load_curves');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('runs', function (Blueprint $table) {
            //
        });
    }
}
