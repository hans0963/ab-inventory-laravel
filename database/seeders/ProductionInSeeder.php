<?php

namespace Database\Seeders;

use App\Models\ProductionIn;
use App\Models\ProductionInItem;
use App\Models\Product;
use App\Models\User;
use Carbon\Carbon;
use Illuminate\Database\Seeder;

class ProductionInSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $user = User::first() ?? User::create([
            'name' => 'Manager User',
            'email' => 'manager@example.com',
            'password' => bcrypt('password'),
            'role' => 'manager',
            'status' => 'Active'
        ]);

        $products = Product::where('inventory_type', 'Finished Product')->take(3)->get();

        if ($products->isEmpty()) {
            $this->command->warn('No Finished Products found. Please create products first.');
            return;
        }

        // Sample Production IN records
        $batches = [
            [
                'production_in_no' => 'PIN-' . now()->format('Ymd') . '-001',
                'date' => now()->subDays(5),
                'notes' => 'Regular production run - Morning shift',
                'total_inventory_value' => 0,
                'status' => 'Approved',
                'created_by' => $user->id,
                'created_date' => now()->subDays(5),
                'approved_by' => $user->id,
                'approved_date' => now()->subDays(4)
            ],
            [
                'production_in_no' => 'PIN-' . now()->format('Ymd') . '-002',
                'date' => now()->subDays(3),
                'notes' => 'Special batch - Premium quality',
                'total_inventory_value' => 0,
                'status' => 'Approved',
                'created_by' => $user->id,
                'created_date' => now()->subDays(3),
                'approved_by' => $user->id,
                'approved_date' => now()->subDays(2)
            ],
            [
                'production_in_no' => 'PIN-' . now()->format('Ymd') . '-003',
                'date' => now(),
                'notes' => 'Pending approval',
                'total_inventory_value' => 0,
                'status' => 'Pending',
                'created_by' => $user->id,
                'created_date' => now(),
                'approved_by' => null,
                'approved_date' => null
            ]
        ];

        foreach ($batches as $batchData) {
            $batch = ProductionIn::create($batchData);

            // Add items to batch
            $selectedProducts = $products->random(min(2, $products->count()));
            $totalValue = 0;

            foreach ($selectedProducts as $product) {
                $quantity = rand(50, 200);
                $unitPrice = $product->selling_price;
                $itemValue = $quantity * $unitPrice;
                $totalValue += $itemValue;

                ProductionInItem::create([
                    'production_in_id' => $batch->id,
                    'product_id' => $product->id,
                    'quantity' => $quantity,
                    'unit_price' => $unitPrice,
                    'total_value' => $itemValue,
                    'expiration_date' => now()->addMonths(rand(3, 12))
                ]);
            }

            $batch->update(['total_inventory_value' => $totalValue]);
        }

        $this->command->info('Production IN sample data created successfully!');
    }
}
