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
        
        $categories = [
            ['category_name' => 'Breads & Rolls', 'description' => 'Freshly baked daily breads, from classic pandesal to artisan rolls.'],
            ['category_name' => 'Cakes & Pastries', 'description' => 'Sweet treats, celebration cakes, and delicate pastries for every occasion.'],
            ['category_name' => 'Savory Bites', 'description' => 'Savory snacks, meat-filled buns, and quick salty cravings.'],
            ['category_name' => 'Cookies & Biscuits', 'description' => 'Crunchy, chewy, and perfectly baked cookies and traditional biscuits.'],
            ['category_name' => 'Beverages', 'description' => 'Refreshments to pair perfectly with your favorite bakeshop treats.'],
        ];

        foreach ($categories as $catData) {
            $category = \App\Models\Category::create($catData);
            
            \App\Models\Product::factory()
                ->count(rand(5, 10))
                ->create(['category_id' => $category->id]);
        }

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
        }
        
        // Run seeders for complex transactions
        $this->command->info('Running transaction seeders...');
        $this->call([
            OrderSeeder::class,
            PurchaseSeeder::class,
        ]);
    }
}
