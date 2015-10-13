<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class AddVoltageToAnalogCurrentMonitors extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('analog_current_monitors', function (Blueprint $table) {
		$table->integer('voltage');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('analog_current_monitors', function (Blueprint $table) {
            //
        });
    }
}
