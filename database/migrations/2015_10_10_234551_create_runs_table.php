<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateRunsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('runs', function (Blueprint $table) {
	        $table->increments('id');
	        $table->timestamps();
	        $table->dateTime('started_at');
	        $table->dateTime('completed_at');
		$table->boolean('is_running');
		$table->integer('appliance_id')->unsigned();
		$table->integer('load_curve_id')->unsigned()->nullable();

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
        Schema::drop('runs');
    }
}
