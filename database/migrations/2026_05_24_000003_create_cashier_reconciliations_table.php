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
        Schema::create('cashier_reconciliations', function (Blueprint $table) {
            $table->id();
            $table->foreignId('employee_id')->nullable()->constrained()->nullOnDelete();
            $table->foreignId('user_id')->nullable()->constrained()->nullOnDelete();
            $table->enum('period_type', ['daily', 'monthly', 'yearly', 'custom'])->default('daily');
            $table->date('date_from');
            $table->date('date_to');
            $table->decimal('opening_cash', 15, 2)->default(0);
            $table->decimal('cash_sales_total', 15, 2)->default(0);
            $table->decimal('non_cash_sales_total', 15, 2)->default(0);
            $table->decimal('voided_cash_total', 15, 2)->default(0);
            $table->decimal('total_sales', 15, 2)->default(0);
            $table->unsignedInteger('transaction_count')->default(0);
            $table->decimal('expected_cash', 15, 2)->default(0);
            $table->decimal('actual_cash_count', 15, 2)->default(0);
            $table->decimal('variance', 15, 2)->default(0);
            $table->text('cashier_notes')->nullable();
            $table->enum('status', ['Submitted', 'Approved', 'Flagged'])->default('Submitted');
            $table->foreignId('reviewed_by')->nullable()->constrained('users')->nullOnDelete();
            $table->timestamp('reviewed_at')->nullable();
            $table->text('review_notes')->nullable();
            $table->timestamps();

            $table->index(['employee_id', 'date_from', 'date_to']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('cashier_reconciliations');
    }
};
