<?php

use App\Models\OutboundDelivery;
use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateOutboundDeliveriesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('outbound_deliveries', function (Blueprint $table) {
            $table->id();
            $table->string('reference');
            $table->foreignId('client_id')->constrained();
            $table->string('destination_name');
            $table->string('destination_phone');
            $table->string('destination_address_1');
            $table->string('destination_address_2')->nullable();
            $table->timestamp('request_delivery_date');
            $table->string('po_reference')->nullable();
            $table->text('notes')->nullable();
            $table->integer('status')->default(OutboundDelivery::STATUS_UNCOMMITTED);
            $table->timestamps();

            $table->unique(['reference', 'client_id']);
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('outbound_deliveries');
    }
}
