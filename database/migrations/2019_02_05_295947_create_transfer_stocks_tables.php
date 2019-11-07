<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateTransferStocksTables extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('transfer_stocks', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->string('number');
            $table->date('date');
            $table->string('reference')->nullable();

            $table->text('description')->nullable();
            $table->string('status')->default('OPEN');

            $table->integer('revise_id')->nullable();
            $table->integer('revise_number')->nullable();

            $table->integer('created_by')->nullable();
            $table->timestamps();
            $table->softDeletes();
        });

        Schema::create('transfer_stock_items', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->bigInteger('transfer_stock_id');

            $table->integer('item_id');
            $table->integer('unit_id');
            $table->decimal('unit_rate')->default(1);
            $table->decimal('quantity');

            $table->string('from');
            $table->string('to');

            $table->timestamps();
            $table->softDeletes();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('transfer_stocks');
        Schema::dropIfExists('transfer_stock_items');
    }
}
