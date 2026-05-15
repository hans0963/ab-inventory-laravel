<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('sales', function (Blueprint $table) {
            // Add customer_id with default "Walk-in" customer
            $table->unsignedBigInteger('customer_id')->nullable()->after('employee_id');
            
            // Add discount fields
            $table->unsignedBigInteger('discount_type_id')->nullable()->after('sold');
            $table->decimal('discount_amount', 10, 2)->default(0)->after('discount_type_id');
            
            // Add payment type with default 'Cash'
            $table->enum('payment_type', ['Cash', 'Credit Card', 'Bank Transfer', 'Check', 'E-Wallet'])
                ->default('Cash')->after('discount_amount');
            
            // Add VAT/Tax computation fields
            $table->decimal('vat_rate', 5, 2)->default(0)->after('payment_type'); // e.g., 12 for 12%
            $table->decimal('vat_amount', 10, 2)->default(0)->after('vat_rate');
            
            // Add total amount field
            $table->decimal('total_amount', 10, 2)->default(0)->after('vat_amount');
            
            // Add receipt number
            $table->string('receipt_number')->nullable()->unique()->after('total_amount');
            
            // Add foreign keys
            $table->foreign('customer_id')->references('id')->on('customers')->onDelete('set null');
            $table->foreign('discount_type_id')->references('id')->on('discount_types')->onDelete('set null');
        });
    }

    public function down(): void
    {
        Schema::table('sales', function (Blueprint $table) {
            $table->dropForeign(['customer_id']);
            $table->dropForeign(['discount_type_id']);
            $table->dropColumn([
                'customer_id',
                'discount_type_id',
                'discount_amount',
                'payment_type',
                'vat_rate',
                'vat_amount',
                'total_amount',
                'receipt_number'
            ]);
        });
    }
};
