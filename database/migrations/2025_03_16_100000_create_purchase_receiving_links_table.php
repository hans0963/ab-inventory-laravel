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
        // Skip if table already exists (handled by 2025_03_16_200000_safe migration)
        if (!Schema::hasTable('purchase_receiving_links')) {
            Schema::create('purchase_receiving_links', function (Blueprint $table) {
                $table->id();
                $table->foreignId('purchase_id')->constrained('purchases')->onDelete('cascade');
                $table->foreignId('inventory_receiving_id')->constrained('inventory_receivings')->onDelete('cascade');
                $table->integer('items_received')->default(0); // Total items received in this receiving
                $table->decimal('amount_received', 12, 2)->default(0); // Total amount received
                $table->timestamps();
                
                // Unique constraint to prevent duplicate links (using short name to stay under 64-char limit)
                $table->unique(['purchase_id', 'inventory_receiving_id'], 'prl_purchase_receiving_unique');
            });
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('purchase_receiving_links');
    }
};
