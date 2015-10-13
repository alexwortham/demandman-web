<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class AddSensAndBiasToAcm extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('analog_current_monitors', function (Blueprint $table) {
		$table->double('sensitivity');
		$table->double('bias');
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
