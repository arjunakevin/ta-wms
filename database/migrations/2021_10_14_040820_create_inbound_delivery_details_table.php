<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateInboundDeliveryDetailsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('inbound_delivery_details', function (Blueprint $table) {
            $table->id();
            $table->foreignId('inbound_delivery_id')->constrained()->cascadeOnDelete();
            $table->foreignId('product_id')->constrained();
            $table->string('line_id');
            $table->integer('base_quantity');
            $table->integer('open_quantity')->default(0);
            $table->timestamps();

            $table->unique(['inbound_delivery_id', 'line_id']);
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('inbound_delivery_details');
    }
}
