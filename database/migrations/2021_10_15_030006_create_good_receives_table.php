<?php

use App\Models\GoodReceive;
use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateGoodReceivesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('good_receives', function (Blueprint $table) {
            $table->id();
            $table->foreignId('inbound_delivery_id')->constrained();
            $table->string('reference');
            $table->timestamp('receive_date');
            $table->text('notes')->nullable();
            $table->integer('status')->default(GoodReceive::STATUS_DRAFT);
            $table->timestamps();

            $table->unique(['inbound_delivery_id', 'reference']);
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('good_receives');
    }
}
