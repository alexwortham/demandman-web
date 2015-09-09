<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;

class CreateSensorsTable extends Migration {

	public function up()
	{
		Schema::create('sensors', function(Blueprint $table) {
			$table->increments('id');
			$table->timestamps();
			$table->string('name')->unique();
			$table->string('mode');
			$table->string('pin');
		});
	}

	public function down()
	{
		Schema::drop('sensors');
	}
}