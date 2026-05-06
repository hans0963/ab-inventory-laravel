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
        $customers = \App\Models\Customer::all();
        $employees = \App\Models\Employee::all();
        $products = \App\Models\Product::all();
        
        if ($customers->isEmpty() || $employees->isEmpty() || $products->isEmpty()) {
            $this->command->warn('Skipping OrderSeeder: Dependencies not found.');
            return;
        }

        // Create 30 formal orders
        for ($i = 1; $i <= 30; $i++) {
            $order = \App\Models\Order::create([
                'customer_id' => $customers->random()->id,
                'employee_id' => $employees->random()->id,
                'order_date' => now()->subDays(rand(0, 60)),
                'total' => 0, // Will update after details
                'total_products' => 0,
                'payment_type' => $i % 3 == 0 ? 'Bank Transfer' : ($i % 2 == 0 ? 'Credit Card' : 'Cash'),
                'order_status' => 'Completed'
            ]);

            $orderTotal = 0;
            $itemsCount = rand(2, 6);
            $selectedProducts = $products->random($itemsCount);
            
            foreach ($selectedProducts as $product) {
                $qty = rand(1, 5);
                $subtotal = $product->selling_price * $qty;
                
                \App\Models\OrderDetail::create([
                    'order_id' => $order->id,
                    'product_id' => $product->id,
                    'quantity' => $qty,
                    'unit_cost' => $product->selling_price,
                    'total' => $subtotal
                ]);
                
                $orderTotal += $subtotal;
            }

            $order->update([
                'total' => $orderTotal,
                'total_products' => $itemsCount
            ]);
        }
    }
}
