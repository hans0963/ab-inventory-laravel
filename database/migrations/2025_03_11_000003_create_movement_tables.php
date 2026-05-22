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
        Schema::create('inventory_movements', function (Blueprint $table) {
            $table->id();
            $table->foreignId('product_id')->constrained()->onDelete('cascade');
            $table->foreignId('employee_id')->nullable()->constrained()->onDelete('set null');
            $table->foreignId('supplier_id')->nullable()->constrained()->onDelete('set null');
            $table->date('date');
            $table->integer('new_luto')->default(0);
            $table->integer('pull_out')->default(0);
            $table->string('transaction_type')->nullable(); // e.g., 'SALE', 'PURCHASE', 'PRODUCTION'
            $table->timestamps();
        });

        Schema::create('raw_material_movements', function (Blueprint $table) {
            $table->id();
            $table->foreignId('raw_material_id')->constrained()->onDelete('cascade');
            $table->foreignId('employee_id')->nullable()->constrained()->onDelete('set null');
            $table->date('date');
            $table->integer('quantity');
            $table->enum('type', ['In', 'Out', 'Adjustment'])->default('In');
            $table->text('notes')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('raw_material_movements');
        Schema::dropIfExists('inventory_movements');
    }
};
