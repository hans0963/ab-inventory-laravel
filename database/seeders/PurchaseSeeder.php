<?php

namespace Database\Seeders;

use App\Models\Purchase;
use App\Models\PurchaseDetail;
use App\Models\Supplier;
use App\Models\Employee;
use App\Models\Product;
use Illuminate\Database\Seeder;

class PurchaseSeeder extends Seeder
{
    public function run(): void
    {
        $suppliers = Supplier::all();
        $employees = Employee::all();
        $products = Product::all();
        
        if ($suppliers->isEmpty() || $employees->isEmpty() || $products->isEmpty()) {
            $this->command->warn('Skipping PurchaseSeeder: Dependencies not found.');
            return;
        }

        // Create 15 formal procurement records
        for ($i = 1; $i <= 15; $i++) {
            $reference = 'PO-' . now()->year . '-' . str_pad($i, 4, '0', STR_PAD_LEFT) . '-' . rand(1000, 9999);
            $poNumber = 'PO-' . now()->format('Ymd') . '-' . str_pad($i, 3, '0', STR_PAD_LEFT);
            $purchase = Purchase::create([
                'purchase_date' => now()->subDays(rand(1, 45)),
                'supplier_id' => $suppliers->random()->id,
                'employee_id' => $employees->random()->id,
                'reference' => $reference,
                'po_number' => $poNumber,
                'status' => 'Pending',
                'total_amount' => 0
            ]);
            
            // Add purchase details
            $itemCount = rand(3, 7);
            $selectedProducts = $products->random($itemCount);

            foreach ($selectedProducts as $product) {
                $quantity = rand(10, 50);
                // Use selling price discounted by 30% for purchase price
                $price = $product->selling_price * 0.70;
                
                PurchaseDetail::create([
                    'purchase_id' => $purchase->id,
                    'product_id' => $product->id,
                    'quantity' => $quantity,
                    'price' => $price,
                    'total' => $quantity * $price
                ]);
            }

            // Calculate and update total amount
            $purchase->update(['total_amount' => $purchase->calculateTotal()]);
        }
    }
}
