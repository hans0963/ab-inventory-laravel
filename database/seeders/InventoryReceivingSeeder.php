<?php

namespace Database\Seeders;

use App\Models\InventoryReceiving;
use App\Models\InventoryReceivingItem;
use App\Models\Supplier;
use App\Models\Product;
use App\Models\User;
use Illuminate\Database\Seeder;

class InventoryReceivingSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $user = User::where('role', 'manager')->first() ?? User::first();
        if (!$user) {
            $this->command->warn('No users found. Please create users first.');
            return;
        }

        $suppliers = Supplier::take(3)->get();
        if ($suppliers->isEmpty()) {
            $this->command->warn('No suppliers found. Please create suppliers first.');
            return;
        }

        $products = Product::where('status', 'Active')->take(5)->get();
        if ($products->isEmpty()) {
            $this->command->warn('No active products found.');
            return;
        }

        $conditions = ['Good', 'Damaged', 'Expired'];

        // Sample Inventory Receivings
        $receivingData = [
            [
                'date' => now()->subDays(10),
                'supplier_id' => $suppliers->random()->id,
                'status' => 'Approved',
                'notes' => 'Regular stock replenishment delivery'
            ],
            [
                'date' => now()->subDays(5),
                'supplier_id' => $suppliers->random()->id,
                'status' => 'Approved',
                'notes' => 'Emergency order fulfillment'
            ],
            [
                'date' => now()->subDays(2),
                'supplier_id' => $suppliers->random()->id,
                'status' => 'Approved',
                'notes' => 'Bulk purchase discount order'
            ],
            [
                'date' => now(),
                'supplier_id' => $suppliers->random()->id,
                'status' => 'Pending',
                'notes' => 'Pending approval from management'
            ]
        ];

        foreach ($receivingData as $data) {
            $receiving = InventoryReceiving::create([
                'receiving_no' => InventoryReceiving::generateReceivingNo(),
                'date' => $data['date'],
                'supplier_id' => $data['supplier_id'],
                'purchase_id' => null,
                'notes' => $data['notes'],
                'total_items' => 0,
                'total_cost' => 0,
                'status' => $data['status'],
                'created_by' => $user->id,
                'created_date' => $data['date'],
                'approved_by' => $data['status'] === 'Approved' ? $user->id : null,
                'approved_date' => $data['status'] === 'Approved' ? now() : null
            ]);

            // Add items to receiving
            $selectedProducts = $products->random(rand(2, 4));
            $totalCost = 0;
            $totalItems = 0;

            foreach ($selectedProducts as $product) {
                $qtyOrdered = rand(50, 200);
                $qtyReceived = rand(40, $qtyOrdered); // May receive less than ordered
                $unitCost = $product->selling_price * 0.6; // Estimate cost as 60% of selling price
                $itemCost = $qtyReceived * $unitCost;
                $condition = $qtyReceived < $qtyOrdered ? 'Damaged' : $conditions[array_rand($conditions)];

                InventoryReceivingItem::create([
                    'inventory_receiving_id' => $receiving->id,
                    'product_id' => $product->id,
                    'quantity_ordered' => $qtyOrdered,
                    'quantity_received' => $qtyReceived,
                    'unit_cost' => $unitCost,
                    'total_cost' => $itemCost,
                    'expiration_date' => now()->addMonths(rand(3, 12)),
                    'condition' => $condition
                ]);

                $totalCost += $itemCost;
                $totalItems += $qtyReceived;
            }

            $receiving->update([
                'total_cost' => $totalCost,
                'total_items' => $totalItems
            ]);
        }

        $this->command->info('Inventory Receiving sample data created successfully!');
    }
}
