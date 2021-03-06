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
            $table->uuid('id');
            $table->double('paid_by_balance')->default(0);
            $table->double('paid_by_money')->default(0);
            $table->double('sub_total')->default(0);
            $table->double('discount')->default(0);
            $table->double('delivery_fee')->default(0);
            $table->double('total')->default(0);
            $table->string('status')->default('PENDING');
            $table->boolean('is_deleted')->default(false);
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
        Schema::dropIfExists('orders');
    }
}
