<?php

namespace Database\Seeders;

use App\Models\Purchase;
use App\Models\PurchaseDetail;
use App\Models\Supplier;
use App\Models\Product;
use App\Models\InventoryReceiving;
use App\Models\PurchaseReceivingLink;
use App\Models\User;
use Illuminate\Database\Seeder;

class EnhancedPurchaseSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $user = User::where('role', 'manager')->first() ?? User::first();
        if (!$user) {
            $this->command->warn('No users found.');
            return;
        }

        $suppliers = Supplier::take(3)->get();
        $products = Product::where('status', 'Active')->take(5)->get();
        $receivings = InventoryReceiving::where('status', 'Approved')->take(4)->get();

        if ($suppliers->isEmpty() || $products->isEmpty()) {
            $this->command->warn('Not enough suppliers or products.');
            return;
        }

        // Create purchases with varying reception statuses
        $purchaseStatuses = ['Pending', 'Approved', 'Partial', 'Complete'];

        foreach ($purchaseStatuses as $index => $status) {
            $purchase = Purchase::create([
                'purchase_date' => now()->subDays(rand(1, 30)),
                'supplier_id' => $suppliers->random()->id,
                'employee_id' => null,
                'reference' => 'PUR-' . now()->format('YmdHis') . '-' . str_pad($index, 3, '0', STR_PAD_LEFT),
                'po_number' => Purchase::generatePONumber(),
                'status' => $status,
                'total_amount' => 0,
                'notes' => "Sample purchase - {$status} status",
                'created_by' => $user->id,
                'created_date' => now()->subDays(rand(1, 30)),
                'approved_by' => $status !== 'Pending' ? $user->id : null,
                'approved_date' => $status !== 'Pending' ? now()->subDays(rand(0, 20)) : null
            ]);

            // Add purchase details
            $selectedProducts = $products->random(rand(2, 4));
            $totalAmount = 0;

            foreach ($selectedProducts as $product) {
                $quantity = rand(10, 50);
                $price = $product->selling_price * 0.65;
                $total = $quantity * $price;
                $totalAmount += $total;

                PurchaseDetail::create([
                    'purchase_id' => $purchase->id,
                    'product_id' => $product->id,
                    'quantity' => $quantity,
                    'price' => $price,
                    'total' => $total
                ]);
            }

            $purchase->update(['total_amount' => $totalAmount]);

            // Link to inventory receivings based on status
            if ($status === 'Partial' && $receivings->count() > 0) {
                $linkedReceivings = $receivings->random(min(2, $receivings->count()));
                foreach ($linkedReceivings as $receiving) {
                    PurchaseReceivingLink::create([
                        'purchase_id' => $purchase->id,
                        'inventory_receiving_id' => $receiving->id,
                        'items_received' => rand(5, 20),
                        'amount_received' => rand(500, 2000)
                    ]);
                }
            } elseif ($status === 'Complete' && $receivings->count() > 0) {
                $linkedReceivings = $receivings->random(min(3, $receivings->count()));
                foreach ($linkedReceivings as $receiving) {
                    PurchaseReceivingLink::create([
                        'purchase_id' => $purchase->id,
                        'inventory_receiving_id' => $receiving->id,
                        'items_received' => rand(20, 50),
                        'amount_received' => rand(1000, 5000)
                    ]);
                }
            }
        }

        $this->command->info('Enhanced purchase sample data created successfully!');
    }
}
