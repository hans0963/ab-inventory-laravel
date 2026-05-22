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
        Schema::create('production_ins', function (Blueprint $table) {
            $table->id();
            $table->string('production_in_no')->unique();
            $table->date('date');
            $table->text('notes')->nullable();
            $table->decimal('total_inventory_value', 15, 2)->default(0);
            $table->enum('status', ['Pending', 'Approved', 'Rejected'])->default('Pending');
            $table->foreignId('created_by')->nullable()->constrained('users')->onDelete('set null');
            $table->timestamp('created_date')->nullable();
            $table->foreignId('approved_by')->nullable()->constrained('users')->onDelete('set null');
            $table->timestamp('approved_date')->nullable();
            $table->timestamps();
            $table->softDeletes();
        });

        Schema::create('production_in_items', function (Blueprint $table) {
            $table->id();
            $table->foreignId('production_in_id')->constrained()->onDelete('cascade');
            $table->foreignId('product_id')->constrained()->onDelete('cascade');
            $table->integer('quantity');
            $table->decimal('unit_price', 15, 2)->default(0);
            $table->decimal('total_value', 15, 2)->default(0);
            $table->date('expiration_date')->nullable();
            $table->timestamps();
        });

        Schema::create('production_outs', function (Blueprint $table) {
            $table->id();
            $table->string('production_out_no')->unique();
            $table->date('date');
            $table->string('reason')->nullable();
            $table->text('notes')->nullable();
            $table->decimal('total_inventory_value', 15, 2)->default(0);
            $table->enum('status', ['Pending', 'Approved', 'Rejected'])->default('Pending');
            $table->foreignId('created_by')->nullable()->constrained('users')->onDelete('set null');
            $table->timestamp('created_date')->nullable();
            $table->foreignId('approved_by')->nullable()->constrained('users')->onDelete('set null');
            $table->timestamp('approved_date')->nullable();
            $table->timestamps();
            $table->softDeletes();
        });

        Schema::create('production_out_items', function (Blueprint $table) {
            $table->id();
            $table->foreignId('production_out_id')->constrained()->onDelete('cascade');
            $table->foreignId('product_id')->constrained()->onDelete('cascade');
            $table->integer('quantity');
            $table->decimal('unit_price', 15, 2)->default(0);
            $table->decimal('total_value', 15, 2)->default(0);
            $table->timestamps();
        });

        Schema::create('stock_withdrawals', function (Blueprint $table) {
            $table->id();
            $table->string('withdrawal_no')->unique();
            $table->date('date');
            $table->string('reason')->nullable();
            $table->text('notes')->nullable();
            $table->integer('total_quantity')->default(0);
            $table->decimal('total_value', 15, 2)->default(0);
            $table->enum('status', ['Pending', 'Approved', 'Rejected'])->default('Pending');
            $table->foreignId('created_by')->nullable()->constrained('users')->onDelete('set null');
            $table->timestamp('created_date')->nullable();
            $table->foreignId('approved_by')->nullable()->constrained('users')->onDelete('set null');
            $table->timestamp('approved_date')->nullable();
            $table->timestamps();
            $table->softDeletes();
        });

        Schema::create('stock_withdrawal_items', function (Blueprint $table) {
            $table->id();
            $table->foreignId('stock_withdrawal_id')->constrained()->onDelete('cascade');
            $table->foreignId('product_id')->constrained()->onDelete('cascade');
            $table->integer('quantity');
            $table->decimal('unit_price', 15, 2)->default(0);
            $table->decimal('total_value', 15, 2)->default(0);
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('stock_withdrawal_items');
        Schema::dropIfExists('stock_withdrawals');
        Schema::dropIfExists('production_out_items');
        Schema::dropIfExists('production_outs');
        Schema::dropIfExists('production_in_items');
        Schema::dropIfExists('production_ins');
    }
};
