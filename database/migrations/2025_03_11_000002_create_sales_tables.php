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
            $table->decimal('discount_percentage', 5, 2);
            $table->text('description')->nullable();
            $table->enum('status', ['Active', 'Inactive'])->default('Active');
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
            $table->decimal('vat_rate', 5, 2)->default(0);
            $table->decimal('vat_amount', 15, 2)->default(0);
            $table->decimal('total_amount', 15, 2);
            $table->string('receipt_number')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('sales');
        Schema::dropIfExists('order_details');
        Schema::dropIfExists('orders');
        Schema::dropIfExists('discount_types');
    }
};
