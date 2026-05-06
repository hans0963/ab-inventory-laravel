<?php

namespace Database\Seeders;

use App\Models\User;
// use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        // Create role-based users
        User::firstOrCreate(
            ['email' => 'admin@example.com'],
            [
                'name' => 'Administrator',
                'password' => bcrypt('password'),
                'role' => 'admin',
            ]
        );

        User::firstOrCreate(
            ['email' => 'manager@example.com'],
            [
                'name' => 'Operations Manager',
                'password' => bcrypt('password'),
                'role' => 'manager',
            ]
        );

        User::firstOrCreate(
            ['email' => 'cashier@example.com'],
            [
                'name' => 'Front Desk Cashier',
                'password' => bcrypt('password'),
                'role' => 'cashier',
            ]
        );

        // Seed Core Entities
        $this->command->info('Seeding categories and products...');
        \App\Models\Category::factory()
            ->count(7)
            ->create()
            ->each(function ($category) {
                \App\Models\Product::factory()
                    ->count(rand(3, 8))
                    ->create(['category_id' => $category->id]);
            });

        $this->command->info('Seeding suppliers and employees...');
        \App\Models\Supplier::factory()->count(10)->create();
        \App\Models\Employee::factory()->count(8)->create();
        \App\Models\Customer::factory()->count(20)->create();
        \App\Models\RawMaterial::factory()->count(15)->create();

        $this->command->info('Seeding sales and inventory movements...');
        $products = \App\Models\Product::all();
        $employees = \App\Models\Employee::all();
        $suppliers = \App\Models\Supplier::all();

        foreach ($products as $product) {
            \App\Models\Sale::factory()->count(rand(2, 5))->create([
                'product_id' => $product->id,
                'employee_id' => $employees->random()->id
            ]);

            \App\Models\InventoryMovement::factory()->count(rand(1, 3))->create([
                'product_id' => $product->id,
                'employee_id' => $employees->random()->id,
                'supplier_id' => $suppliers->random()->id
            ]);
        }
        
        // Run seeders for complex transactions
        $this->command->info('Running transaction seeders...');
        $this->call([
            OrderSeeder::class,
            PurchaseSeeder::class,
        ]);
    }
}
