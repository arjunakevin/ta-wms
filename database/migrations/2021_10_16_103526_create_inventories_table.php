<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateInventoriesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('inventories', function (Blueprint $table) {
            $table->id();
            $table->foreignId('location_id')->nullable()->constrained();
            $table->nullableMorphs('documentable');
            $table->foreignId('product_id')->constrained();
            $table->integer('base_quantity');
            $table->integer('pick_quantity')->default(0);
            $table->integer('put_quantity')->default(0);
            $table->timestamp('posting_date');
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
        Schema::dropIfExists('inventories');
    }
}
