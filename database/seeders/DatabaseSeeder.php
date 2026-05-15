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
        // Create role-based users and link them to employees
        $admin = User::firstOrCreate(
            ['email' => 'admin@example.com'],
            [
                'name' => 'Administrator',
                'password' => 'password',
                'role' => 'admin',
                'email_verified_at' => now(),
            ]
        );
        \App\Models\Employee::firstOrCreate(
            ['employee_email' => $admin->email],
            [
                'user_id' => $admin->id,
                'employee_name' => $admin->name,
                'employee_phone' => '09123456789',
                'position' => 'Administrator'
            ]
        );

        $manager = User::firstOrCreate(
            ['email' => 'manager@example.com'],
            [
                'name' => 'Operations Manager',
                'password' => 'password',
                'role' => 'manager',
                'email_verified_at' => now(),
            ]
        );
        \App\Models\Employee::firstOrCreate(
            ['employee_email' => $manager->email],
            [
                'user_id' => $manager->id,
                'employee_name' => $manager->name,
                'employee_phone' => '09123456790',
                'position' => 'Operations Manager'
            ]
        );

        $cashier = User::firstOrCreate(
            ['email' => 'cashier@example.com'],
            [
                'name' => 'Front Desk Cashier',
                'password' => 'password',
                'role' => 'cashier',
                'email_verified_at' => now(),
            ]
        );
        \App\Models\Employee::firstOrCreate(
            ['employee_email' => $cashier->email],
            [
                'user_id' => $cashier->id,
                'employee_name' => $cashier->name,
                'employee_phone' => '09123456791',
                'position' => 'Cashier'
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
        
        // Seed Raw Materials as Products
        $rawMaterialsCategory = \App\Models\Category::create([
            'category_name' => 'Raw Materials',
            'description' => 'Ingredients and supplies used in production.'
        ]);

        $rawMaterialsData = [
            'Bread Flour' => '25kg bag',
            'All-Purpose Flour' => '25kg bag',
            'White Sugar' => '50kg bag',
            'Brown Sugar' => '50kg bag',
            'Unsalted Butter' => '1kg block',
            'Instant Dry Yeast' => '500g pack',
            'Fresh Whole Milk' => '1L carton',
            'Large Grade A Eggs' => 'Tray of 30',
            'Salt' => '1kg pack',
            'Vanilla Extract' => '500ml bottle',
            'Dark Chocolate Chips' => '1kg pack',
            'Cocoa Powder' => '1kg pack'
        ];

        foreach ($rawMaterialsData as $name => $unit) {
            \App\Models\Product::create([
                'product_name' => $name,
                'category_id' => $rawMaterialsCategory->id,
                'inventory_type' => 'Raw Material',
                'selling_price' => 0, // Raw materials usually don't have a selling price
                'quantity' => rand(50, 200),
                'status' => 'Active',
                'stock_alert_threshold' => 10
            ]);
        }

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
            DiscountTypeSeeder::class,
            OrderSeeder::class,
            PurchaseSeeder::class,
            ProductionInSeeder::class,
            ProductionOutSeeder::class,
            StockWithdrawalSeeder::class,
            InventoryReceivingSeeder::class,
            EnhancedPurchaseSeeder::class,
        ]);
    }
}
