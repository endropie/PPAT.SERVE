<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateForecastsTables extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('forecasts', function (Blueprint $table) {
            $table->increments('id');
            $table->string('number');
            $table->date('begin_date');
            $table->date('until_date');

            $table->integer('customer_id');

            $table->text('description')->nullable();
            $table->string('status')->default('OPEN');

            $table->timestamps();
        });

        Schema::create('forecast_items', function (Blueprint $table) {
            $table->increments('id');
            $table->integer('forecast_id');

            $table->integer('item_id');
            $table->integer('unit_id');
            $table->float('unit_rate')->default(1);
            $table->float('quantity');
            $table->float('price')->default(0);
            $table->string('note')->nullable();

            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('forecasts');
        Schema::dropIfExists('forecast_items');
    }
}
