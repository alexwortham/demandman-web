<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateLoadDatasTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('load_datas', function (Blueprint $table) {
            $table->increments('id');
            $table->timestamps();
            $table->dateTime('time');
            $table->double('load');
            $table->integer('load_curve_id')->unsigned();
            $table->integer('analog_current_monitor_id')->unsigned();

            $table->foreign('load_curve_id')
                ->references('id')->on('load_curves');
            $table->foreign('analog_current_monitor_id')
                ->references('id')->on('analog_current_monitors');

            $table->index(['analog_current_monitor_id', 'time']);
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::drop('load_datas');
    }
}
