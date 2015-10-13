<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateDemandHistoriesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('demand_histories', function (Blueprint $table) {
	        $table->increments('id');
	        $table->timestamps();
		$table->dateTime('start_time');
		$table->dateTime('end_time');
		$table->double('watts');
		$table->double('watt_hours');
		$table->double('demand_charge');
		$table->double('usage_charge');
		$table->integer('billing_cycle_id')->unsigned();

		$table->foreign('billing_cycle_id')->references('id')->on('billing_cycles');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::drop('demand_histories');
    }
}
