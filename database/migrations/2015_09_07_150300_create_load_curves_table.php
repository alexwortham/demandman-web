<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;

class CreateLoadCurvesTable extends Migration {

	public function up()
	{
		Schema::create('load_curves', function(Blueprint $table) {
			$table->increments('id');
			$table->timestamps();
			$table->string('name');
			$table->longText('data');
		});
	}

	public function down()
	{
		Schema::drop('load_curves');
	}
}
