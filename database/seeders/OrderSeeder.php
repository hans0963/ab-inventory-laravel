<?php

namespace Database\Seeders;

use App\Models\Order;
use App\Models\Customer;
use App\Models\Employee;
use Illuminate\Database\Seeder;

class OrderSeeder extends Seeder
{
    public function run(): void
    {
        $customers = Customer::all();
        $employees = Employee::all();
        
        if ($customers->isEmpty()) {
            // Create test customers if none exist
            for ($i = 1; $i <= 5; $i++) {
                Customer::create([
                    'name' => 'Customer ' . $i,
                    'email' => 'customer' . $i . '@example.com',
                    'phone' => '09' . rand(100000000, 999999999),
                    'address' => 'Address ' . $i
                ]);
            }
            $customers = Customer::all();
        }
        
        if ($employees->isEmpty()) {
            // Create test employees if none exist
            for ($i = 1; $i <= 3; $i++) {
                Employee::create([
                    'employee_name' => 'Employee ' . $i,
                    'employee_email' => 'employee' . $i . '@example.com',
                    'employee_phone' => '09' . rand(100000000, 999999999),
                    'position' => ['Manager', 'Staff', 'Supervisor'][rand(0, 2)]
                ]);
            }
            $employees = Employee::all();
        }

        // Create 10 test orders if none exist
        if (Order::count() == 0) {
            for ($i = 1; $i <= 10; $i++) {
                Order::create([
                    'id' => $employees->random()->id,
                    'customer_id' => $customers->random()->id,
                    'order_date' => now()->subDays(rand(1, 30)),
                    'total' => rand(1000, 50000),
                    'total_products' => rand(1, 20),
                    'payment_type' => ['Cash', 'Card', 'Online'][rand(0, 2)],
                    'order_status' => 'Completed'
                ]);
            }
        }
    }
}
