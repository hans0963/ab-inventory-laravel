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
        Schema::create('inventory_receivings', function (Blueprint $table) {
            $table->id();
            $table->string('receiving_no')->unique(); // PRC-YYYYMMDD-XXX format
            $table->date('date');
            $table->foreignId('supplier_id')->nullable()->constrained('suppliers')->onDelete('set null');
            $table->foreignId('purchase_id')->nullable()->constrained('purchases')->onDelete('set null');
            $table->text('notes')->nullable();
            $table->integer('total_items')->default(0); // Count of items received
            $table->decimal('total_cost', 12, 2)->default(0); // Sum of all item costs
            $table->enum('status', ['Pending', 'Approved', 'Rejected'])->default('Pending');
            
            // Audit trail
            $table->foreignId('created_by')->constrained('users')->onDelete('cascade');
            $table->date('created_date');
            $table->foreignId('approved_by')->nullable()->constrained('users')->onDelete('set null');
            $table->dateTime('approved_date')->nullable();
            
            $table->timestamps();
            $table->softDeletes();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('inventory_receivings');
    }
};
