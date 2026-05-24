<?php

namespace Database\Seeders;

use App\Models\Employee;
use App\Models\User;
// use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        // Create role-based users and link them to employees
        $admin = User::updateOrCreate(
            ['email' => 'admin@example.com'],
            [
                'name' => 'System Admin',
                'password' => bcrypt('password'),
                'role' => 'admin',
            ]
        );
        $admin->forceFill(['email_verified_at' => now()])->save();
        Employee::firstOrCreate(
            ['employee_email' => $admin->email],
            [
                'user_id' => $admin->id,
                'employee_name' => $admin->name,
                'employee_phone' => '09123456789',
                'position' => 'Administrator'
            ]
        );

        $hr = User::updateOrCreate(
            ['email' => 'hr@example.com'],
            [
                'name' => 'HR Manager',
                'password' => bcrypt('password'),
                'role' => 'hr',
            ]
        );
        $hr->forceFill(['email_verified_at' => now()])->save();
        Employee::firstOrCreate(
            ['employee_email' => $hr->email],
            [
                'user_id' => $hr->id,
                'employee_name' => $hr->name,
                'employee_phone' => '09123456792',
                'position' => 'HR Manager'
            ]
        );

        $manager = User::updateOrCreate(
            ['email' => 'manager@example.com'],
            [
                'name' => 'Operations Manager',
                'password' => bcrypt('password'),
                'role' => 'manager',
            ]
        );
        $manager->forceFill(['email_verified_at' => now()])->save();
        Employee::firstOrCreate(
            ['employee_email' => $manager->email],
            [
                'user_id' => $manager->id,
                'employee_name' => $manager->name,
                'employee_phone' => '09123456790',
                'position' => 'Operations Manager'
            ]
        );

        $cashier = User::updateOrCreate(
            ['email' => 'cashier@example.com'],
            [
                'name' => 'Front Desk Cashier',
                'password' => bcrypt('password'),
                'role' => 'cashier',
            ]
        );
        $cashier->forceFill(['email_verified_at' => now()])->save();
        Employee::firstOrCreate(
            ['employee_email' => $cashier->email],
            [
                'user_id' => $cashier->id,
                'employee_name' => $cashier->name,
                'employee_phone' => '09123456791',
                'position' => 'Cashier'
            ]
        );
    }
}
