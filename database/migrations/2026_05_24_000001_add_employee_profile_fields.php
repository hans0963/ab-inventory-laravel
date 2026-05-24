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
        Schema::table('employees', function (Blueprint $table) {
            $table->date('date_hired')->nullable()->after('position');
            $table->string('emergency_contact')->nullable()->after('date_hired');
            $table->enum('status', ['Active', 'Inactive', 'Archived'])->default('Active')->after('emergency_contact');
        });

        if (DB::getDriverName() === 'mysql') {
            DB::statement("ALTER TABLE users MODIFY role ENUM('admin', 'manager', 'cashier', 'baker', 'hr') NOT NULL DEFAULT 'cashier'");
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        if (DB::getDriverName() === 'mysql') {
            DB::statement("UPDATE users SET role = 'cashier' WHERE role = 'baker'");
            DB::statement("ALTER TABLE users MODIFY role ENUM('admin', 'manager', 'cashier', 'hr') NOT NULL DEFAULT 'cashier'");
        }

        Schema::table('employees', function (Blueprint $table) {
            $table->dropColumn(['date_hired', 'emergency_contact', 'status']);
        });
    }
};
