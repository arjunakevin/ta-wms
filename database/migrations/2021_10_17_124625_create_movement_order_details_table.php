<?php

use App\Models\MovementOrderDetail;
use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateMovementOrderDetailsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('movement_order_details', function (Blueprint $table) {
            $table->id();
            $table->foreignId('movement_order_id')->constrained()->cascadeOnDelete();
            $table->foreignId('product_id')->constrained()->cascadeOnDelete();
            $table->foreignId('source_inventory_id');
            $table->foreignId('source_location_id');
            $table->foreignId('destination_inventory_id');
            $table->foreignId('destination_location_id');
            $table->boolean('is_pick')->default(0);
            $table->integer('base_quantity');
            $table->integer('status')->default(MovementOrderDetail::STATUS_OPEN);
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
        Schema::dropIfExists('movement_order_details');
    }
}
