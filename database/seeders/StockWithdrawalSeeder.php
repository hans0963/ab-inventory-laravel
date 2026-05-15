<?php

namespace Database\Seeders;

use App\Models\StockWithdrawal;
use App\Models\StockWithdrawalItem;
use App\Models\Product;
use App\Models\User;
use Illuminate\Database\Seeder;

class StockWithdrawalSeeder extends Seeder
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

        $products = Product::where('quantity', '>', 5)->take(4)->get();

        if ($products->isEmpty()) {
            $this->command->warn('No Products with sufficient stock found.');
            return;
        }

        $withdrawalReasons = [
            'Internal Use' => 'Used for internal testing and QA',
            'Damaged' => 'Items damaged during handling or storage',
            'Expired' => 'Stock expired and removed from inventory',
            'Wastage' => 'Natural wastage and spillage'
        ];

        // Sample withdrawals for each reason
        foreach ($withdrawalReasons as $reason => $note) {
            $withdrawal = StockWithdrawal::create([
                'withdrawal_no' => StockWithdrawal::generateWithdrawalNo(),
                'date' => now()->subDays(rand(1, 10)),
                'reason' => $reason,
                'notes' => $note,
                'total_quantity' => 0,
                'total_value' => 0,
                'status' => rand(0, 1) === 0 ? 'Pending' : 'Approved',
                'created_by' => $user->id,
                'created_date' => now()->subDays(rand(1, 10)),
                'approved_by' => rand(0, 1) === 0 ? null : $user->id,
                'approved_date' => rand(0, 1) === 0 ? null : now()->subDays(rand(0, 5))
            ]);

            // Add 1-2 items per withdrawal
            $selectedProducts = $products->random(rand(1, 2));
            $totalQty = 0;
            $totalVal = 0;

            foreach ($selectedProducts as $product) {
                $maxQty = min($product->quantity, 50);
                $quantity = rand(1, $maxQty);
                $unitPrice = $product->cost_price ?? $product->selling_price * 0.6;
                $itemValue = $quantity * $unitPrice;

                StockWithdrawalItem::create([
                    'stock_withdrawal_id' => $withdrawal->id,
                    'product_id' => $product->id,
                    'quantity' => $quantity,
                    'unit_price' => $unitPrice,
                    'total_value' => $itemValue
                ]);

                $totalQty += $quantity;
                $totalVal += $itemValue;
            }

            $withdrawal->update([
                'total_quantity' => $totalQty,
                'total_value' => $totalVal
            ]);
        }

        $this->command->info('Stock Withdrawal sample data created successfully!');
    }
}
