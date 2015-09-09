<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;

class CreateAppliancesTable extends Migration {

	public function up()
	{
		Schema::create('appliances', function(Blueprint $table) {
			$table->increments('id');
			$table->timestamps();
			$table->enum('type', array('hvac', 'wheater', 'dishwash', 'dryer'));
			$table->string('name');
		});
	}

	public function down()
	{
		Schema::drop('appliances');
	}
}