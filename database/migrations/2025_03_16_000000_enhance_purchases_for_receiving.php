<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up()
    {
        // All enhancements moved to 2025_03_16_200000_safe_create_purchase_receiving_and_enhance_purchases
        // This migration is deprecated and kept for history only
    }

    public function down()
    {
        Schema::dropIfExists('purchase_receiving_links');
        
        Schema::table('purchases', function (Blueprint $table) {
            $table->dropForeign(['created_by']);
            $table->dropForeign(['approved_by']);
            $table->dropColumn([
                'po_number',
                'status',
                'total_amount',
                'notes',
                'created_by',
                'created_date',
                'approved_by',
                'approved_date',
            ]);
        });
    }
};
