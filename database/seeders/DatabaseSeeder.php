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
        
        
        $categoriesData = [
            'Bread and Rolls' => [
                'description' => 'The staple of most bakeries, including loaves, baguettes, sourdough, buns, rolls, and specialty artisan bread.',
                'products' => ['Artisan Sourdough', 'French Baguette', 'Whole Wheat Loaf', 'Classic Pandesal', 'Dinner Rolls', 'Brioche Buns', 'Ciabatta']
            ],
            'Pastries' => [
                'description' => 'Items made from fat-based doughs, including laminated doughs like croissants and Danish, as well as puff pastry, tarts, and turnovers.',
                'products' => ['Butter Croissant', 'Pain au Chocolat', 'Apple Turnover', 'Cheese Danish', 'Blueberry Tart', 'Palmier', 'Cinnamon Roll']
            ],
            'Cakes' => [
                'description' => 'Includes layer cakes, pound cakes, foam cakes (like chiffon), cheesecakes, and specialty items like wedding cakes.',
                'products' => ['Red Velvet Cake', 'Chocolate Ganache Cake', 'Strawberry Shortcake', 'New York Cheesecake', 'Ube Chiffon Cake', 'Mocha Pound Cake']
            ],
            'Cookies and Biscuits' => [
                'description' => 'A major category comprising dropped, rolled, pressed, and bar cookies, often categorized as dry or sweet goods.',
                'products' => ['Chocolate Chip Cookies', 'Oatmeal Raisin Cookies', 'Double Chocolate Crinkles', 'Butter Shortbread', 'Snickerdoodles', 'Biscotti']
            ],
            'Quick Breads and Muffins' => [
                'description' => 'Baked goods that use chemical leaveners (like baking powder) rather than yeast, such as muffins, scones, banana bread, and coffee cakes.',
                'products' => ['Banana Walnut Bread', 'Blueberry Muffin', 'Lemon Poppyseed Scone', 'Double Chocolate Muffin', 'Zucchini Bread']
            ],
            'Pies and Tarts' => [
                'description' => 'Pastry shells filled with sweet or savory fillings (e.g., fruit pies, custard tarts).',
                'products' => ['Classic Apple Pie', 'Egg Tart', 'Buko Pie', 'Lemon Meringue Tart', 'Peach Mango Pie', 'Pumpkin Pie']
            ],
            'Savory Goods' => [
                'description' => 'Items such as savory pastries, quiches, breadsticks, and pretzels.',
                'products' => ['Chicken Empanada', 'Ham and Cheese Puffs', 'Savory Quiche', 'Garlic Breadsticks', 'Soft Pretzels', 'Sausage Rolls']
            ],
            'Donuts and Fried Products' => [
                'description' => 'Yeasted or cake-based doughs that are fried instead of baked.',
                'products' => ['Glazed Donut', 'Chocolate Frosted Donut', 'Bavarian Cream Filled', 'Spanish Churros', 'Beignets', 'Twisted Donut']
            ],
        ];

        foreach ($categoriesData as $name => $data) {
            $category = \App\Models\Category::create([
                'category_name' => $name,
                'description' => $data['description']
            ]);
            
            foreach ($data['products'] as $productName) {
                \App\Models\Product::factory()->create([
                    'product_name' => $productName,
                    'category_id' => $category->id
                ]);
            }
        }

        $this->command->info('Seeding suppliers and employees...');
        \App\Models\Supplier::factory()->count(10)->create();
        \App\Models\Employee::factory()->count(8)->create();
        
        // Ensure Walk-in Customer exists
        \App\Models\Customer::firstOrCreate(
            ['email' => 'walkin@bakeshop.com'],
            ['name' => 'Walk-in Customer', 'phone' => '0000000000', 'address' => 'Store']
        );
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
            ProductionInSeeder::class,
            ProductionOutSeeder::class,
            StockWithdrawalSeeder::class,
            InventoryReceivingSeeder::class,
        ]);
    }
}
