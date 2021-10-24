<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateOutboundDeliveryDetailsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('outbound_delivery_details', function (Blueprint $table) {
            $table->id();
            $table->foreignId('outbound_delivery_id')->constrained()->cascadeOnDelete();
            $table->foreignId('product_id')->constrained();
            $table->string('line_id');
            $table->integer('base_quantity');
            $table->integer('open_quantity')->default(0);
            $table->timestamps();

            $table->unique(['outbound_delivery_id', 'line_id']);
            $table->unique(['outbound_delivery_id', 'product_id']);
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('outbound_delivery_details');
    }
}
