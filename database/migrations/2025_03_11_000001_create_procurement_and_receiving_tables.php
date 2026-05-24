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
        Schema::create('purchases', function (Blueprint $table) {
            $table->id();
            $table->date('purchase_date');
            $table->date('expected_delivery_date')->nullable();
            $table->foreignId('supplier_id')->constrained()->onDelete('cascade');
            $table->foreignId('employee_id')->nullable()->constrained()->onDelete('set null');
            $table->string('reference')->nullable();
            $table->string('po_number')->nullable();
            $table->enum('status', ['Draft', 'Pending', 'Pending Approval', 'Approved', 'Rejected', 'Ordered', 'Partial', 'Complete', 'Cancelled'])->default('Pending Approval');
            $table->decimal('total_amount', 15, 2)->default(0);
            $table->text('notes')->nullable();
            $table->foreignId('created_by')->nullable()->constrained('users')->onDelete('set null');
            $table->timestamp('created_date')->nullable();
            $table->foreignId('approved_by')->nullable()->constrained('users')->onDelete('set null');
            $table->timestamp('approved_date')->nullable();
            $table->foreignId('rejected_by')->nullable()->constrained('users')->onDelete('set null');
            $table->timestamp('rejected_date')->nullable();
            $table->text('rejection_reason')->nullable();
            $table->timestamps();
            $table->softDeletes();
        });

        Schema::create('purchase_details', function (Blueprint $table) {
            $table->id();
            $table->foreignId('purchase_id')->constrained()->onDelete('cascade');
            $table->foreignId('product_id')->constrained()->onDelete('cascade');
            $table->integer('quantity');
            $table->decimal('price', 15, 2);
            $table->decimal('total', 15, 2);
            $table->timestamps();
        });

        Schema::create('inventory_receivings', function (Blueprint $table) {
            $table->id();
            $table->string('receiving_no')->unique();
            $table->date('date');
            $table->foreignId('supplier_id')->constrained()->onDelete('cascade');
            $table->foreignId('purchase_id')->nullable()->constrained()->onDelete('set null');
            $table->enum('status', ['Pending', 'Approved', 'Rejected'])->default('Pending');
            $table->text('notes')->nullable();
            $table->integer('total_items')->default(0);
            $table->decimal('total_cost', 15, 2)->default(0);
            $table->foreignId('created_by')->nullable()->constrained('users')->onDelete('set null');
            $table->timestamp('created_date')->nullable();
            $table->foreignId('approved_by')->nullable()->constrained('users')->onDelete('set null');
            $table->timestamp('approved_date')->nullable();
            $table->timestamps();
            $table->softDeletes();
        });

        Schema::create('inventory_receiving_items', function (Blueprint $table) {
            $table->id();
            $table->foreignId('inventory_receiving_id')->constrained()->onDelete('cascade');
            $table->foreignId('product_id')->constrained()->onDelete('cascade');
            $table->string('batch_number')->nullable();
            $table->integer('quantity_ordered')->default(0);
            $table->integer('quantity_received');
            $table->decimal('unit_cost', 15, 2);
            $table->decimal('total_cost', 15, 2)->virtualAs('quantity_received * unit_cost');
            $table->date('expiration_date')->nullable();
            $table->enum('condition', ['Good', 'Damaged', 'Expired', 'Near Expiry'])->default('Good');
            $table->timestamps();
        });

        Schema::create('purchase_receiving_links', function (Blueprint $table) {
            $table->id();
            $table->foreignId('purchase_id')->constrained()->onDelete('cascade');
            $table->foreignId('inventory_receiving_id')->constrained()->onDelete('cascade');
            $table->integer('items_received')->default(0);
            $table->decimal('amount_received', 15, 2)->default(0);
            $table->timestamps();
        });

        Schema::create('product_supplier_prices', function (Blueprint $table) {
            $table->id();
            $table->foreignId('product_id')->constrained()->cascadeOnDelete();
            $table->foreignId('supplier_id')->constrained()->cascadeOnDelete();
            $table->decimal('unit_price', 15, 2)->default(0);
            $table->string('unit')->nullable();
            $table->boolean('is_default')->default(false);
            $table->timestamps();
            $table->unique(['product_id', 'supplier_id']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('purchase_receiving_links');
        Schema::dropIfExists('product_supplier_prices');
        Schema::dropIfExists('inventory_receiving_items');
        Schema::dropIfExists('inventory_receivings');
        Schema::dropIfExists('purchase_details');
        Schema::dropIfExists('purchases');
    }
};
