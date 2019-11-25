<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class AddRelationOutgoingToIncoming extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('outgoing_good_items', function (Blueprint $table) {
            $table->bigInteger('incoming_good_item_id')->nullable()->after('unit_rate');
        });

        Schema::table('incoming_good_items', function (Blueprint $table) {
            $table->decimal('amount_outgoing')->default(0)->after('unit_rate');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('outgoing_good_items', function (Blueprint $table) {
            $table->dropColumn('incoming_good_item_id');
        });

        Schema::table('incoming_good_items', function (Blueprint $table) {
            $table->dropColumn('amount_outgoing');
        });
    }
}
