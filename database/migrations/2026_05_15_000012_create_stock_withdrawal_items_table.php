<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('stock_withdrawal_items', function (Blueprint $table) {
            $table->id();
            $table->foreignId('stock_withdrawal_id')->constrained('stock_withdrawals')->onDelete('cascade');
            $table->foreignId('product_id')->constrained('products')->onDelete('cascade');
            $table->integer('quantity');
            $table->decimal('unit_price', 10, 2); // Selling price at time of withdrawal
            $table->decimal('total_value', 12, 2); // quantity * unit_price
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('stock_withdrawal_items');
    }
};
