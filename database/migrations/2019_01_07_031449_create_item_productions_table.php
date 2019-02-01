<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateItemProductionsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('item_productions', function (Blueprint $table) {
            $table->increments('id');
            $table->integer('production_id');
            $table->string('reference')->nullable();

            $table->integer('item_id');  // =>> the field "belongsTo" relation with "Items" table.
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
        Schema::dropIfExists('item_productions');
    }
}
