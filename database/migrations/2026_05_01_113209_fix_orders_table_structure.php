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
        Schema::table('orders', function (Blueprint $table) {
            // Check if employee_id already exists (it shouldn't based on the error)
            if (!Schema::hasColumn('orders', 'employee_id')) {
                $table->unsignedBigInteger('employee_id')->nullable()->after('customer_id');
                $table->foreign('employee_id')->references('id')->on('employees')->onDelete('set null');
            }
            
            // Try to drop the incorrect foreign key on 'id' if it exists
            // Note: Foreign key name depends on the DB, usually 'orders_id_foreign'
            try {
                $table->dropForeign(['id']);
            } catch (\Exception $e) {
                // Ignore if it doesn't exist
            }
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('orders', function (Blueprint $table) {
            $table->dropForeign(['employee_id']);
            $table->dropColumn('employee_id');
        });
    }
};
