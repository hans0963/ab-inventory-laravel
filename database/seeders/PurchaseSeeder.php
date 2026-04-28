<?php

namespace Database\Seeders;

use App\Models\Purchase;
use App\Models\PurchaseDetail;
use App\Models\Supplier;
use App\Models\Employee;
use App\Models\Products;
use Illuminate\Database\Seeder;

class PurchaseSeeder extends Seeder
{
    public function run(): void
    {
        $suppliers = Supplier::all();
        $employees = Employee::all();
        $products = Products::all();
        
        if ($suppliers->isEmpty()) {
            for ($i = 1; $i <= 3; $i++) {
                Supplier::create([
                    'supplier_name' => 'Supplier ' . $i,
                    'email' => 'supplier' . $i . '@example.com',
                    'contact_person' => 'Contact ' . $i,
                    'phone' => '09' . rand(100000000, 999999999),
                    'address' => 'Address ' . $i
                ]);
            }
            $suppliers = Supplier::all();
        }
        
        if ($employees->isEmpty()) {
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
        
        if ($products->isEmpty()) {
            for ($i = 1; $i <= 5; $i++) {
                Products::create([
                    'product_name' => 'Product ' . $i,
                    'product_category' => rand(1, 5),
                    'price' => rand(100, 5000),
                    'quantity' => rand(10, 100)
                ]);
            }
            $products = Products::all();
        }

        // Create 8 test purchases with details
        for ($i = 1; $i <= 8; $i++) {
            $purchase = Purchase::create([
                'purchase_date' => now()->subDays(rand(1, 30)),
                'supplier_id' => $suppliers->random()->id,
                'employee_id' => $employees->random()->id,
                'reference' => 'PO-' . str_pad($i, 4, '0', STR_PAD_LEFT)
            ]);
            
            // Add purchase details
            for ($j = 0; $j < rand(2, 5); $j++) {
                $quantity = rand(5, 50);
                $price = rand(100, 5000);
                PurchaseDetail::create([
                    'purchase_id' => $purchase->id,
                    'product_id' => $products->random()->id,
                    'quantity' => $quantity,
                    'price' => $price,
                    'total' => $quantity * $price
                ]);
            }
        }
    }
}
