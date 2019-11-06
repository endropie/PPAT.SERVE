<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateItemsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('items', function (Blueprint $table) {
            $table->increments('id');

            $table->string('code'); //->unique();
            $table->integer('customer_id')->nullable();
            $table->integer('brand_id')->nullable();
            $table->integer('specification_id')->nullable();

            $table->string('part_name')->nullable();
            $table->string('part_alias')->nullable();
            $table->string('part_number')->nullable();

            $table->string('load_type')->nullable();
            $table->integer('load_capacity')->nullable()->default(0);
            $table->integer('packing_duration')->nullable()->default(0);
            $table->decimal('sa_dm')->nullable()->default(0);
            $table->decimal('weight',20,2)->nullable()->default(0);

            $table->decimal('price', 20, 2)->default(0);

            $table->integer('category_item_id')->nullable();
            $table->integer('type_item_id')->nullable();
            $table->integer('size_id')->nullable();
            $table->integer('unit_id')->nullable();

            $table->text('description')->nullable();
            $table->boolean('enable')->default(1);
            $table->integer('created_by')->nullable();
            $table->timestamps();
        });

        Schema::create('item_prelines', function (Blueprint $table) {
            $table->increments('id');
            $table->integer('line_id');
            $table->string('note')->nullable();
            $table->integer('item_id')->unsigned();
            $table->boolean('ismain')->default(0);

            $table->timestamps();
        });

        Schema::create('item_units', function (Blueprint $table) {
            $table->increments('id');
            $table->integer('item_id');
            $table->integer('unit_id');
            $table->decimal('rate',12,4)->default(1);
            $table->timestamps();
        });

        Schema::create('item_stocks', function (Blueprint $table) {
            $table->increments('id');
            $table->integer('item_id');
            $table->string('stockist', 10);
            $table->decimal('total',12,4)->default(0);
            $table->timestamps();

            $table->unique(['item_id','stockist']);
        });

        Schema::create('item_stockables', function (Blueprint $table) {
            $table->increments('id');
            $table->morphs('base');
            $table->integer('item_id');
            $table->string('stockist', 10);
            $table->decimal('unit_amount',12,4)->default(0);
            $table->timestamps();

            // $table->unique(['item_id','stockist']);
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('items');
        Schema::dropIfExists('item_prelines');
        Schema::dropIfExists('item_units');
        Schema::dropIfExists('item_stocks');
        Schema::dropIfExists('item_stockables');
    }
}
