<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class AddApplianceToDemandHistoriesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('demand_histories', function (Blueprint $table) {
            //
            $table->integer('appliance_id')->unsigned()->nullable();

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
        Schema::table('demand_histories', function (Blueprint $table) {
            //
        });
    }
}
