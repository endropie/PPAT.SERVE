<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateIncomingGoodsTables extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('incoming_goods', function (Blueprint $table) {
            $table->increments('id');
            $table->string('number');
            $table->string('registration');
            $table->date('date');
            $table->time('time');

            $table->integer('customer_id');
            $table->string('reference_number')->nullable();
            $table->date('reference_date')->nullable();

            $table->enum('transaction', ['REGULER', 'RETURN']);
            $table->enum('order_mode', ['PO', 'NONE', 'ACCUMULATE']);

            $table->integer('vehicle_id')->nullable();
            $table->tinyInteger('rit')->nullable();
            $table->text('description')->nullable();
            $table->string('status')->default('OPEN');

            $table->integer('revise_id')->nullable();
            $table->integer('revise_number')->nullable();

            $table->integer('created_by')->nullable();
            $table->timestamps();
            $table->softDeletes();
        });

        Schema::create('incoming_good_items', function (Blueprint $table) {
            $table->increments('id');
            $table->integer('incoming_good_id');

            $table->integer('item_id');
            $table->float('quantity');
            $table->integer('unit_id');
            $table->float('unit_rate')->default(1);

            $table->text('note')->nullable();
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
        Schema::dropIfExists('incoming_goods');
        Schema::dropIfExists('incoming_good_items');
    }
}
