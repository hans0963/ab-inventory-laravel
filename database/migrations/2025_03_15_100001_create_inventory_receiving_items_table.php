<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('inventory_receiving_items', function (Blueprint $table) {
            $table->id();
            $table->foreignId('inventory_receiving_id')->constrained('inventory_receivings')->onDelete('cascade');
            $table->foreignId('product_id')->constrained('products')->onDelete('cascade');
            
            // Quantity tracking
            $table->integer('quantity_ordered')->default(0); // From PO or manual
            $table->integer('quantity_received')->default(0); // Actually received
            
            // Pricing
            $table->decimal('unit_cost', 12, 2);
            $table->decimal('total_cost', 12, 2)->storedAs('quantity_received * unit_cost');
            
            // Product info
            $table->date('expiration_date')->nullable(); // Expiration if applicable
            $table->enum('condition', ['Good', 'Damaged', 'Expired', 'Other'])->default('Good');
            
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('inventory_receiving_items');
    }
};
