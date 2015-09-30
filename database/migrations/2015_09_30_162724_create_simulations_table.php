<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateSimulationsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('simulations', function (Blueprint $table) {
            $table->increments('id');
            $table->timestamps();
	    $table->tinyInteger('bus')->unsigned();
            $table->tinyInteger('addr')->unsigned();
            $table->tinyInteger('min')->unsigned();
            $table->tinyInteger('max')->unsigned();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::drop('simulations');
    }
}
