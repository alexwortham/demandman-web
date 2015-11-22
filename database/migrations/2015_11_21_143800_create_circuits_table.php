<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateCircuitsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('circuits', function (Blueprint $table) {
            $table->increments('id');
            $table->timestamps();
            $table->string('name')->nullable();
            $table->tinyInteger('number')->unsigned();
            $table->tinyInteger('mask')->unsigned();
            $table->tinyInteger('bus')->unsigned();
            $table->tinyInteger('addr')->unsigned();
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
        Schema::drop('circuits');
    }
}
