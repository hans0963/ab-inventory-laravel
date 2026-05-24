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
        Schema::table('sales', function (Blueprint $table) {
            $table->index(['created_at', 'payment_type'], 'sales_created_payment_idx');
            $table->index(['employee_id', 'created_at'], 'sales_employee_created_idx');
            $table->index(['void_status', 'created_at'], 'sales_void_created_idx');
            $table->index(['product_id', 'created_at'], 'sales_product_created_idx');
        });

        Schema::table('production_ins', function (Blueprint $table) {
            $table->index(['status', 'date'], 'production_ins_status_date_idx');
            $table->index(['created_by', 'status'], 'production_ins_creator_status_idx');
        });

        Schema::table('production_outs', function (Blueprint $table) {
            $table->index(['status', 'date'], 'production_outs_status_date_idx');
            $table->index(['created_by', 'status'], 'production_outs_creator_status_idx');
        });

        Schema::table('stock_withdrawals', function (Blueprint $table) {
            $table->index(['status', 'date'], 'stock_withdrawals_status_date_idx');
            $table->index(['created_by', 'status'], 'stock_withdrawals_creator_status_idx');
        });

        Schema::table('inventory_receivings', function (Blueprint $table) {
            $table->index(['status', 'date'], 'inventory_receivings_status_date_idx');
            $table->index(['created_by', 'status'], 'inventory_receivings_creator_status_idx');
        });

        Schema::table('purchases', function (Blueprint $table) {
            $table->index(['status', 'purchase_date'], 'purchases_status_date_idx');
            $table->index(['created_by', 'status'], 'purchases_creator_status_idx');
        });

        Schema::table('cashier_reconciliations', function (Blueprint $table) {
            $table->index(['status', 'date_to'], 'cashier_reconciliations_status_date_idx');
        });

        Schema::table('notifications', function (Blueprint $table) {
            $table->index(['user_id', 'read_at', 'created_at'], 'notifications_user_read_created_idx');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('notifications', function (Blueprint $table) {
            $table->dropIndex('notifications_user_read_created_idx');
        });

        Schema::table('cashier_reconciliations', function (Blueprint $table) {
            $table->dropIndex('cashier_reconciliations_status_date_idx');
        });

        Schema::table('purchases', function (Blueprint $table) {
            $table->dropIndex('purchases_status_date_idx');
            $table->dropIndex('purchases_creator_status_idx');
        });

        Schema::table('inventory_receivings', function (Blueprint $table) {
            $table->dropIndex('inventory_receivings_status_date_idx');
            $table->dropIndex('inventory_receivings_creator_status_idx');
        });

        Schema::table('stock_withdrawals', function (Blueprint $table) {
            $table->dropIndex('stock_withdrawals_status_date_idx');
            $table->dropIndex('stock_withdrawals_creator_status_idx');
        });

        Schema::table('production_outs', function (Blueprint $table) {
            $table->dropIndex('production_outs_status_date_idx');
            $table->dropIndex('production_outs_creator_status_idx');
        });

        Schema::table('production_ins', function (Blueprint $table) {
            $table->dropIndex('production_ins_status_date_idx');
            $table->dropIndex('production_ins_creator_status_idx');
        });

        Schema::table('sales', function (Blueprint $table) {
            $table->dropIndex('sales_created_payment_idx');
            $table->dropIndex('sales_employee_created_idx');
            $table->dropIndex('sales_void_created_idx');
            $table->dropIndex('sales_product_created_idx');
        });
    }
};
