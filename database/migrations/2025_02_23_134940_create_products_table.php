<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up() {
        Schema::create('products', function (Blueprint $table) {
            $table->id()->primary();
            $table->string('product_name', 50);
            $table->unsignedBigInteger('category_id')->nullable();
            $table->integer('buying_price')->default(0);
            $table->integer('selling_price')->default(0);
            $table->integer('quantity')->default(0);
            $table->integer('stock_alert_threshold')->default(10);
            $table->timestamps();
        
            $table->foreign('category_id')->references('id')->on('categories')->onDelete('set null');
        });
        
    }

    public function down() {
        Schema::dropIfExists('products');
    }
};
