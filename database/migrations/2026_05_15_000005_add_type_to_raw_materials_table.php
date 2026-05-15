<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        // Add status to raw_materials if not already present
        if (!Schema::hasColumn('raw_materials', 'status')) {
            Schema::table('raw_materials', function (Blueprint $table) {
                $table->enum('status', ['Active', 'Inactive'])->default('Active')->after('unit');
            });
        }
    }

    public function down(): void
    {
        Schema::table('raw_materials', function (Blueprint $table) {
            if (Schema::hasColumn('raw_materials', 'status')) {
                $table->dropColumn('status');
            }
        });
    }
};
