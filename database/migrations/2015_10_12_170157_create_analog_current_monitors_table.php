<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateAnalogCurrentMonitorsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('analog_current_monitors', function (Blueprint $table) {
	        $table->increments('id');
	        $table->timestamps();
		$table->tinyInteger('ain_number');
		$table->string('name')->nullable();
		$table->integer('appliance_id')->unsigned();

		$table->foreign('appliance_id')->references('id')->on('appliances');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::drop('analog_current_monitors');
    }
}
