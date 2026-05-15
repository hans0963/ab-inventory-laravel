<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('products', function (Blueprint $table) {
            // Add status field: Active, Inactive
            $table->enum('status', ['Active', 'Inactive'])->default('Active')->after('selling_price');
            
            // Add expiration date for products
            $table->date('expiration_date')->nullable()->after('status');
            
            // Drop buying_price column if it exists
            if (Schema::hasColumn('products', 'buying_price')) {
                $table->dropColumn('buying_price');
            }
            
            // Add unique constraint on product_name to prevent duplicates
            $table->unique('product_name');
        });
    }

    public function down(): void
    {
        Schema::table('products', function (Blueprint $table) {
            $table->dropUnique(['product_name']);
            $table->dropColumn(['status', 'expiration_date']);
            $table->integer('buying_price')->default(0)->after('category_id');
        });
    }
};
