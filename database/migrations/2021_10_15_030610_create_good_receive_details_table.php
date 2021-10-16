<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateGoodReceiveDetailsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('good_receive_details', function (Blueprint $table) {
            $table->id();
            $table->foreignId('good_receive_id')->constrained()->cascadeOnDelete();
            $table->foreignId('inbound_detail_id')->constrained('inbound_delivery_details');
            $table->integer('base_quantity');
            $table->integer('receive_quantity')->default(0);
            $table->integer('open_check_quantity')->default(0);
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
        Schema::dropIfExists('good_receive_details');
    }
}
