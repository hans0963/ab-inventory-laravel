<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations - safely handles existing tables/columns
     */
    public function up(): void
    {
        // Check if purchase_receiving_links already exists
        if (!Schema::hasTable('purchase_receiving_links')) {
            Schema::create('purchase_receiving_links', function (Blueprint $table) {
                $table->id();
                $table->foreignId('purchase_id')->constrained('purchases')->onDelete('cascade');
                $table->foreignId('inventory_receiving_id')->constrained('inventory_receivings')->onDelete('cascade');
                $table->integer('items_received')->default(0);
                $table->decimal('amount_received', 12, 2)->default(0);
                $table->timestamps();
                
                $table->unique(['purchase_id', 'inventory_receiving_id'], 'prl_purchase_receiving_unique');
            });
        }

        // Check if purchases table needs updates
        if (Schema::hasTable('purchases')) {
            Schema::table('purchases', function (Blueprint $table) {
                // Safely add columns only if they don't exist
                if (!Schema::hasColumn('purchases', 'po_number')) {
                    $table->string('po_number')->unique();
                }
                if (!Schema::hasColumn('purchases', 'status')) {
                    $table->enum('status', ['Pending', 'Partial', 'Complete', 'Approved', 'Cancelled'])->default('Pending');
                }
                if (!Schema::hasColumn('purchases', 'total_amount')) {
                    $table->decimal('total_amount', 12, 2)->default(0);
                }
                if (!Schema::hasColumn('purchases', 'notes')) {
                    $table->text('notes')->nullable();
                }
                if (!Schema::hasColumn('purchases', 'created_by')) {
                    $table->unsignedBigInteger('created_by')->nullable();
                }
                if (!Schema::hasColumn('purchases', 'created_date')) {
                    $table->dateTime('created_date')->nullable();
                }
                if (!Schema::hasColumn('purchases', 'approved_by')) {
                    $table->unsignedBigInteger('approved_by')->nullable();
                }
                if (!Schema::hasColumn('purchases', 'approved_date')) {
                    $table->dateTime('approved_date')->nullable();
                }
            });

            // Add foreign keys if they don't exist
            if (!Schema::hasColumn('purchases', 'created_by')) return;
            
            try {
                Schema::table('purchases', function (Blueprint $table) {
                    $table->foreign('created_by')
                        ->references('id')
                        ->on('users')
                        ->onDelete('set null');
                });
            } catch (\Exception $e) {
                // Foreign key may already exist
            }

            try {
                Schema::table('purchases', function (Blueprint $table) {
                    $table->foreign('approved_by')
                        ->references('id')
                        ->on('users')
                        ->onDelete('set null');
                });
            } catch (\Exception $e) {
                // Foreign key may already exist
            }
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('purchase_receiving_links');
    }
};
