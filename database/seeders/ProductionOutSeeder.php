<?php

namespace Database\Seeders;

use App\Models\ProductionOut;
use App\Models\ProductionOutItem;
use App\Models\Product;
use App\Models\User;
use Illuminate\Database\Seeder;

class ProductionOutSeeder extends Seeder
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

        $products = Product::where('inventory_type', 'Finished Product')
            ->where('quantity', '>', 10)
            ->take(3)
            ->get();

        if ($products->isEmpty()) {
            $this->command->warn('No Finished Products with sufficient stock found.');
            return;
        }

        $reasons = ['Repackaging', 'Quality Check', 'Sample Testing', 'Returns'];

        // Sample Production OUT records
        $outs = [
            [
                'production_out_no' => 'POUT-' . now()->format('Ymd') . '-001',
                'date' => now()->subDays(4),
                'reason' => 'Repackaging',
                'notes' => 'Bulk repackaging for retail distribution',
                'status' => 'Approved',
                'created_by' => $user->id,
                'created_date' => now()->subDays(4),
                'approved_by' => $user->id,
                'approved_date' => now()->subDays(3)
            ],
            [
                'production_out_no' => 'POUT-' . now()->format('Ymd') . '-002',
                'date' => now()->subDays(1),
                'reason' => 'Quality Check',
                'notes' => 'Routine quality inspection samples',
                'status' => 'Approved',
                'created_by' => $user->id,
                'created_date' => now()->subDays(1),
                'approved_by' => $user->id,
                'approved_date' => now()
            ],
            [
                'production_out_no' => 'POUT-' . now()->format('Ymd') . '-003',
                'date' => now(),
                'reason' => 'Sample Testing',
                'notes' => 'Samples for market testing',
                'status' => 'Pending',
                'created_by' => $user->id,
                'created_date' => now(),
                'approved_by' => null,
                'approved_date' => null
            ]
        ];

        foreach ($outs as $outData) {
            $out = ProductionOut::create($outData);

            // Add items
            $selectedProducts = $products->random(min(2, $products->count()));

            foreach ($selectedProducts as $product) {
                $maxQty = min($product->quantity, 100);
                $quantity = rand(5, $maxQty);
                $unitPrice = $product->selling_price;

                ProductionOutItem::create([
                    'production_out_id' => $out->id,
                    'product_id' => $product->id,
                    'quantity' => $quantity,
                    'unit_price' => $unitPrice,
                    'total_value' => $quantity * $unitPrice
                ]);
            }
        }

        $this->command->info('Production OUT sample data created successfully!');
    }
}
