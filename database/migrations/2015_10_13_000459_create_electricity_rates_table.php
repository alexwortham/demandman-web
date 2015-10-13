<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateElectricityRatesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('electricity_rates', function (Blueprint $table) {
	        $table->increments('id');
	        $table->timestamps();
		$table->double('usage_rate');
		$table->double('demand_rate');
		$table->tinyInteger('day_of_month');
		$table->tinyInteger('demand_delta');
		$table->boolean('is_current');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::drop('electricity_rates');
    }
}
