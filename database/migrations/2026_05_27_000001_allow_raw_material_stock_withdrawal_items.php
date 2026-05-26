<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('stock_withdrawal_items', function (Blueprint $table) {
            $table->foreignId('raw_material_id')->nullable()->after('product_id')->constrained('raw_materials')->nullOnDelete();
            $table->foreignId('product_id')->nullable()->change();
        });
    }

    public function down(): void
    {
        Schema::table('stock_withdrawal_items', function (Blueprint $table) {
            $table->dropConstrainedForeignId('raw_material_id');
            $table->foreignId('product_id')->nullable(false)->change();
        });
    }
};
