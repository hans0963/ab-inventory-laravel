<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('production_in_items', function (Blueprint $table) {
            $table->id();
            $table->foreignId('production_in_id')->constrained('production_ins')->onDelete('cascade');
            $table->foreignId('product_id')->constrained('products')->onDelete('cascade');
            $table->integer('quantity');
            $table->decimal('unit_price', 10, 2); // Selling price at time of production
            $table->decimal('total_value', 12, 2); // quantity * unit_price
            $table->date('expiration_date')->nullable(); // Batch-specific expiration
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('production_in_items');
    }
};
