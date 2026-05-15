<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up()
    {
        // Add columns to purchases table
        Schema::table('purchases', function (Blueprint $table) {
            $table->string('po_number')->unique()->after('reference');
            $table->enum('status', ['Pending', 'Partial', 'Complete'])->default('Pending')->after('reference');
            $table->decimal('total_amount', 12, 2)->default(0)->after('status');
            $table->text('notes')->nullable()->after('total_amount');
            $table->unsignedBigInteger('created_by')->nullable()->after('notes');
            $table->dateTime('created_date')->nullable()->after('created_by');
            $table->unsignedBigInteger('approved_by')->nullable()->after('created_date');
            $table->dateTime('approved_date')->nullable()->after('approved_by');
            
            $table->foreign('created_by')->references('id')->on('users')->onDelete('set null');
            $table->foreign('approved_by')->references('id')->on('users')->onDelete('set null');
        });

        // Create purchase_receiving_links table for tracking partial receivings
        Schema::create('purchase_receiving_links', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('purchase_id');
            $table->unsignedBigInteger('inventory_receiving_id');
            $table->integer('items_received')->default(0); // Count of items received via this receiving
            $table->decimal('amount_received', 12, 2)->default(0);
            $table->timestamps();

            $table$table->unique(['purchase_id', 'inventory_receiving_id'], 'pur_rec_link_unique');
            $table->foreign('purchase_id')->references('id')->on('purchases')->onDelete('cascade');
            $table->foreign('inventory_receiving_id')->references('id')->on('inventory_receivings')->onDelete('cascade');
        });
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
