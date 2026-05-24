<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::table('discount_types', function (Blueprint $table) {
            $table->enum('discount_type', ['Percentage', 'Fixed Amount'])->default('Percentage')->after('discount_name');
            $table->decimal('discount_value', 15, 2)->default(0)->after('discount_percentage');
            $table->decimal('minimum_purchase_amount', 15, 2)->default(0)->after('discount_value');
            $table->enum('applicable_to', ['All', 'Category', 'Product'])->default('All')->after('minimum_purchase_amount');
            $table->json('applicable_ids')->nullable()->after('applicable_to');
            $table->date('start_date')->nullable()->after('applicable_ids');
            $table->date('end_date')->nullable()->after('start_date');
        });

        DB::table('discount_types')->update([
            'discount_type' => 'Percentage',
            'discount_value' => DB::raw('discount_percentage'),
            'applicable_to' => 'All',
        ]);

        if (DB::getDriverName() === 'mysql') {
            DB::statement("ALTER TABLE discount_types MODIFY status ENUM('Active', 'Inactive', 'Expired') NOT NULL DEFAULT 'Active'");
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        if (DB::getDriverName() === 'mysql') {
            DB::statement("UPDATE discount_types SET status = 'Inactive' WHERE status = 'Expired'");
            DB::statement("ALTER TABLE discount_types MODIFY status ENUM('Active', 'Inactive') NOT NULL DEFAULT 'Active'");
        }

        Schema::table('discount_types', function (Blueprint $table) {
            $table->dropColumn([
                'discount_type',
                'discount_value',
                'minimum_purchase_amount',
                'applicable_to',
                'applicable_ids',
                'start_date',
                'end_date',
            ]);
        });
    }
};
