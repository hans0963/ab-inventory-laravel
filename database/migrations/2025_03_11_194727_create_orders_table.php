<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up()
    {
        Schema::create('orders', function (Blueprint $table) {
            $table->id();
            $table->foreignId('customer_id')->constrained('customers')->onDelete('cascade');
            $table->foreign('id')->references('id')->on('employees')->onDelete('cascade');
            $table->dateTime('order_date')->useCurrent();
            $table->string('order_status', 10)->default('Completed');
            $table->integer('total_products');
            $table->integer('total');
            $table->enum('payment_type', ['Cash', 'Card', 'Online']);
            $table->timestamps();
        });
    }

    public function down()
    {
        Schema::dropIfExists('orders');
    }
};

