<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateBillingCyclesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('billing_cycles', function (Blueprint $table) {
	        $table->increments('id');
	        $table->timestamps();
		$table->dateTime('begin_date');
		$table->dateTime('end_date');
		$table->dateTime('charges_updated');
		$table->boolean('is_current');
		$table->double('demand_charge');
		$table->double('usage_charge');
		$table->integer('electricity_rate_id')->unsigned();

		$table->foreign('electricity_rate_id')->references('id')->on('electricity_rates');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::drop('billing_cycles');
    }
}
