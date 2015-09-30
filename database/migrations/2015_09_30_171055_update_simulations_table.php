<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class UpdateSimulationsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('simulations', function (Blueprint $table) {
            //
		$table->integer('appliance_id')->unsigned();
		$table->integer('load_curve_id')->unsigned();

		$table->foreign('appliance_id')->references('id')->on('appliances');
		$table->foreign('load_curve_id')->references('id')->on('load_curves');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('simulations', function (Blueprint $table) {
            //
        });
    }
}
