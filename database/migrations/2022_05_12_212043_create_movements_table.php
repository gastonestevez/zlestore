<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateMovementsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('movements', function (Blueprint $table) {
            $table->increments('id');
            $table->integer('product_id');
            $table->integer('quantity');
            $table->timestamps();
            $table->enum('status',['in progress','pending','completed', 'cancelled'])->default('pending');
            $table->integer('remaining_stock');
            
            $table->unsignedBigInteger('origin_warehouse_id')->nullable();  
            $table->foreign('origin_warehouse_id')->references('id')->on('warehouse');
            $table->unsignedBigInteger('destiny_warehouse_id')->nullable();  
            $table->foreign('destiny_warehouse_id')->references('id')->on('warehouse');
            $table->unsignedInteger('user_id')->nullable();  
            $table->foreign('user_id')->references('id')->on('users');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('movements');
    }
}
