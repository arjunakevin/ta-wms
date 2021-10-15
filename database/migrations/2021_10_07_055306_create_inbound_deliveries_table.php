<?php

use App\Models\InboundDelivery;
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateInboundDeliveriesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('inbound_deliveries', function (Blueprint $table) {
            $table->id();
            $table->string('reference');
            $table->foreignId('client_id')->constrained();
            $table->timestamp('arrival_date')->nullable();
            $table->timestamp('po_date')->nullable();
            $table->text('notes')->nullable();
            $table->integer('status')->default(InboundDelivery::STATUS_UNRECEIVED);
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
        Schema::dropIfExists('inbound_deliveries');
    }
}
