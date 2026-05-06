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
        $suppliers = \App\Models\Supplier::all();
        $employees = \App\Models\Employee::all();
        $products = \App\Models\Product::all();
        
        if ($suppliers->isEmpty() || $employees->isEmpty() || $products->isEmpty()) {
            $this->command->warn('Skipping PurchaseSeeder: Dependencies not found.');
            return;
        }

        // Create 15 formal procurement records
        for ($i = 1; $i <= 15; $i++) {
            $purchase = \App\Models\Purchase::create([
                'purchase_date' => now()->subDays(rand(1, 45)),
                'supplier_id' => $suppliers->random()->id,
                'employee_id' => $employees->random()->id,
                'reference' => 'PO-' . now()->year . '-' . str_pad($i, 4, '0', STR_PAD_LEFT)
            ]);
            
            // Add purchase details
            $itemCount = rand(3, 7);
            $selectedProducts = $products->random($itemCount);

            foreach ($selectedProducts as $product) {
                $quantity = rand(10, 50);
                $price = $product->buying_price;
                
                \App\Models\PurchaseDetail::create([
                    'purchase_id' => $purchase->id,
                    'product_id' => $product->id,
                    'quantity' => $quantity,
                    'price' => $price,
                    'total' => $quantity * $price
                ]);
            }
        }
    }
}
