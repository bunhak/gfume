<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateOrderDetailsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('order_details', function (Blueprint $table) {
            $table->uuid('id');
            $table->string('order_id');
            $table->string('cart_id');
            $table->double('qty');
            $table->double('price');
            $table->string('item_id');
            $table->string('shop_id');
            $table->string('item_detail_id');
            $table->string('exchange_rate_id');
            $table->double('exchange_rate');
            $table->string('delivery_fee_id');
            $table->double('delivery_fee');
            $table->dateTime('delivery_date');
            $table->string('bonus_id')->nullable();
            $table->double('bonus')->default(0);
            $table->double('bonus_total')->default(0);
            $table->string('discount_id')->nullable();
            $table->double('discount')->default(0);
            $table->string('user_alert')->default('PENDING');
            $table->string('seller_alert')->default('PENDING');
            $table->double('sub_total');
            $table->double('total');
            $table->string('status')->default('PENDING');
            $table->string('created_by');
            $table->string('updated_by')->nullable();
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
        Schema::dropIfExists('order_details');
    }
}
