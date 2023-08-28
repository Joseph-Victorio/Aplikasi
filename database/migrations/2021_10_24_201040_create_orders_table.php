<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateOrdersTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('orders', function (Blueprint $table) {
            $table->id();
            $table->string('order_ref');
            $table->string('customer_name');
            $table->string('customer_phone');
            $table->integer('order_qty');
            $table->integer('order_subtotal');
            $table->integer('order_weight');
            $table->integer('order_total')->default(0);
            $table->string('order_status');
            $table->string('shipping_type')->nullable();
            $table->string('shipping_courier_code')->nullable();
            $table->string('shipping_courier_name')->nullable();
            $table->integer('shipping_cost')->nullable();
            $table->text('shipping_address')->nullable();
            $table->text('note')->nullable();
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
        Schema::dropIfExists('orders');
    }
}
