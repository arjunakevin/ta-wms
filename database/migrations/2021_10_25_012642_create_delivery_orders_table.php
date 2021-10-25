<?php

use App\Models\DeliveryOrder;
use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateDeliveryOrdersTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('delivery_orders', function (Blueprint $table) {
            $table->id();
            $table->foreignId('outbound_delivery_id')->constrained();
            $table->string('reference');
            $table->timestamp('good_issue_date')->nullable();
            $table->text('notes')->nullable();
            $table->integer('status')->default(DeliveryOrder::STATUS_UNALLOCATED);
            $table->timestamps();

            $table->unique(['outbound_delivery_id', 'reference']);
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('delivery_orders');
    }
}
