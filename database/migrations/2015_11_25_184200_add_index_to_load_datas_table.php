<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class AddIndexToLoadDatasTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('load_datas', function (Blueprint $table) {
            //
            $table->integer('idx')->unsigned();

            $table->index(['load_curve_id', 'idx']);
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('load_datas', function (Blueprint $table) {
            //
        });
    }
}
