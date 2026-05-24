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
        Schema::create('discount_types', function (Blueprint $table) {
            $table->id();
            $table->string('discount_name');
            $table->enum('discount_type', ['Percentage', 'Fixed Amount'])->default('Percentage');
            $table->decimal('discount_percentage', 5, 2);
            $table->decimal('discount_value', 15, 2)->default(0);
            $table->decimal('minimum_purchase_amount', 15, 2)->default(0);
            $table->enum('applicable_to', ['All', 'Category', 'Product'])->default('All');
            $table->json('applicable_ids')->nullable();
            $table->date('start_date')->nullable();
            $table->date('end_date')->nullable();
            $table->text('description')->nullable();
            $table->enum('status', ['Active', 'Inactive', 'Expired'])->default('Active');
            $table->timestamps();
        });

        Schema::create('orders', function (Blueprint $table) {
            $table->id();
            $table->foreignId('customer_id')->nullable()->constrained()->onDelete('set null');
            $table->foreignId('employee_id')->nullable()->constrained()->onDelete('set null');
            $table->date('order_date');
            $table->decimal('total', 15, 2);
            $table->string('payment_type')->nullable();
            $table->integer('total_products')->default(0);
            $table->string('order_status')->default('Pending');
            $table->timestamps();
        });

        Schema::create('order_details', function (Blueprint $table) {
            $table->id();
            $table->foreignId('order_id')->constrained()->onDelete('cascade');
            $table->foreignId('product_id')->constrained()->onDelete('cascade');
            $table->integer('quantity');
            $table->decimal('unit_cost', 15, 2);
            $table->decimal('total', 15, 2);
            $table->timestamps();
        });

        Schema::create('sales', function (Blueprint $table) {
            $table->id();
            $table->foreignId('product_id')->constrained()->onDelete('cascade');
            $table->foreignId('employee_id')->nullable()->constrained()->onDelete('set null');
            $table->foreignId('customer_id')->nullable()->constrained()->onDelete('set null');
            $table->date('date');
            $table->integer('sold');
            $table->decimal('unit_price', 15, 2);
            $table->foreignId('discount_type_id')->nullable()->constrained()->onDelete('set null');
            $table->decimal('discount_amount', 15, 2)->default(0);
            $table->string('payment_type')->nullable();
            $table->date('credit_due_date')->nullable();
            $table->decimal('vat_rate', 5, 2)->default(0);
            $table->enum('vat_type', ['Inclusive', 'Exclusive'])->default('Exclusive');
            $table->decimal('vat_amount', 15, 2)->default(0);
            $table->decimal('subtotal_amount', 15, 2)->default(0);
            $table->decimal('total_amount', 15, 2);
            $table->string('receipt_number')->nullable();
            $table->enum('void_status', ['Active', 'Pending', 'Voided', 'Rejected'])->default('Active');
            $table->text('void_reason')->nullable();
            $table->foreignId('void_requested_by')->nullable()->constrained('users')->nullOnDelete();
            $table->foreignId('void_approved_by')->nullable()->constrained('users')->nullOnDelete();
            $table->timestamp('voided_at')->nullable();
            $table->timestamps();
        });

        Schema::create('customer_credit_payments', function (Blueprint $table) {
            $table->id();
            $table->foreignId('customer_id')->constrained()->cascadeOnDelete();
            $table->foreignId('sale_id')->nullable()->constrained()->nullOnDelete();
            $table->decimal('amount', 15, 2);
            $table->date('payment_date');
            $table->string('payment_method')->nullable();
            $table->text('notes')->nullable();
            $table->foreignId('received_by')->nullable()->constrained('users')->nullOnDelete();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('customer_credit_payments');
        Schema::dropIfExists('sales');
        Schema::dropIfExists('order_details');
        Schema::dropIfExists('orders');
        Schema::dropIfExists('discount_types');
    }
};
