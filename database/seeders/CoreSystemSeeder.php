<?php

namespace Database\Seeders;

use App\Models\Category;
use App\Models\Customer;
use App\Models\DiscountType;
use App\Models\Employee;
use App\Models\Product;
use App\Models\ProductRecipe;
use App\Models\RawMaterial;
use App\Models\Supplier;
use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class CoreSystemSeeder extends Seeder
{
    public function run(): void
    {
        DB::transaction(function () {
            $users = $this->seedUsersAndEmployees();
            $categories = $this->seedCategories();
            $suppliers = $this->seedSuppliers();

            $rawMaterials = $this->seedRawMaterials();
            $products = $this->seedProducts($categories, $suppliers);
            $this->seedRecipes($products, $rawMaterials);
            $this->seedCustomers();
            $this->seedDiscounts();
        });
    }

    private function seedUsersAndEmployees(): array
    {
        $accounts = [
            ['name' => 'System Admin', 'email' => 'admin@example.com', 'role' => 'admin', 'phone' => '09123456789', 'position' => 'Administrator'],
            ['name' => 'HR Manager', 'email' => 'hr@example.com', 'role' => 'hr', 'phone' => '09123456792', 'position' => 'HR Manager'],
            ['name' => 'Operations Manager', 'email' => 'manager@example.com', 'role' => 'manager', 'phone' => '09123456790', 'position' => 'Operations Manager'],
            ['name' => 'Front Desk Cashier', 'email' => 'cashier@example.com', 'role' => 'cashier', 'phone' => '09123456791', 'position' => 'Cashier'],
        ];

        $users = [];

        foreach ($accounts as $account) {
            $user = User::updateOrCreate(
                ['email' => $account['email']],
                [
                    'name' => $account['name'],
                    'password' => bcrypt('password'),
                    'role' => $account['role'],
                ]
            );
            $user->forceFill(['email_verified_at' => now()])->save();

            Employee::updateOrCreate(
                ['employee_email' => $user->email],
                [
                    'user_id' => $user->id,
                    'employee_name' => $user->name,
                    'employee_phone' => $account['phone'],
                    'position' => $account['position'],
                    'date_hired' => now()->subMonths(8)->toDateString(),
                    'status' => 'Active',
                ]
            );

            $users[$account['role']] = $user;
        }

        return $users;
    }

    private function seedCategories(): array
    {
        $names = [
            'Bread' => 'Daily bread and bakery staples',
            'Pastry' => 'Sweet and laminated baked goods',
            'Cakes' => 'Whole cakes and celebration items',
            'Beverages' => 'Drinks sold at the counter',
        ];

        $categories = [];

        foreach ($names as $name => $description) {
            $categories[$name] = Category::updateOrCreate(
                ['category_name' => $name],
                ['description' => $description, 'status' => 'Active']
            );
        }

        return $categories;
    }

    private function seedSuppliers(): array
    {
        $records = [
            'Golden Grain Supply' => ['items' => 'Flour, sugar, dry ingredients', 'terms' => 'Credit'],
            'Dairy Best Trading' => ['items' => 'Butter, eggs, dairy supplies', 'terms' => 'Cash'],
            'BakePack Solutions' => ['items' => 'Boxes, bags, packaging', 'terms' => 'Credit'],
        ];

        $suppliers = [];

        foreach ($records as $company => $details) {
            $suppliers[$company] = Supplier::updateOrCreate(
                ['suppliers_company' => $company],
                [
                    'suppliers_name' => $company . ' Rep',
                    'suppliers_email' => strtolower(str_replace(' ', '.', $company)) . '@example.com',
                    'suppliers_phone' => '09170000000',
                    'suppliers_address' => 'Cebu City',
                    'items_supplied' => $details['items'],
                    'payment_terms' => $details['terms'],
                    'status' => 'Active',
                ]
            );
        }

        return $suppliers;
    }

    private function seedRawMaterials(): array
    {
        $records = [
            'Bread Flour' => ['type' => 'Ingredient', 'quantity' => 50000, 'unit' => 'g', 'threshold' => 10000],
            'White Sugar' => ['type' => 'Ingredient', 'quantity' => 25000, 'unit' => 'g', 'threshold' => 5000],
            'Butter' => ['type' => 'Dairy', 'quantity' => 10000, 'unit' => 'g', 'threshold' => 2000],
            'Eggs' => ['type' => 'Ingredient', 'quantity' => 600, 'unit' => 'pcs', 'threshold' => 120],
            'Cake Box 8x8' => ['type' => 'Packaging', 'quantity' => 200, 'unit' => 'pcs', 'threshold' => 40],
        ];

        $materials = [];

        foreach ($records as $name => $data) {
            $materials[$name] = RawMaterial::updateOrCreate(
                ['material_name' => $name],
                [
                    'type' => $data['type'],
                    'quantity' => $data['quantity'],
                    'unit' => $data['unit'],
                    'expiration_date' => now()->addMonths(4)->toDateString(),
                    'expiry_alert_days' => 7,
                    'status' => 'Active',
                    'stock_alert_threshold' => $data['threshold'],
                    'reorder_level' => $data['threshold'],
                    'reorder_quantity' => $data['threshold'] * 2,
                ]
            );
        }

        return $materials;
    }

    private function seedProducts(array $categories, array $suppliers): array
    {
        $records = [
            'Pandesal' => ['category' => 'Bread', 'price' => 8, 'stock' => 30, 'threshold' => 20],
            'Ensaymada' => ['category' => 'Pastry', 'price' => 35, 'stock' => 18, 'threshold' => 10],
            'Chocolate Cake' => ['category' => 'Cakes', 'price' => 650, 'stock' => 4, 'threshold' => 2],
        ];

        $products = [];
        $supplier = $suppliers['Golden Grain Supply'] ?? null;

        foreach ($records as $name => $data) {
            $products[$name] = Product::updateOrCreate(
                ['product_name' => $name],
                [
                    'category_id' => $categories[$data['category']]->id,
                    'inventory_type' => 'Finished Product',
                    'selling_price' => $data['price'],
                    'quantity' => $data['stock'],
                    'status' => 'Active',
                    'unit' => 'pcs',
                    'expiration_date' => now()->addDays(3)->toDateString(),
                    'expiry_alert_days' => 2,
                    'stock_alert_threshold' => $data['threshold'],
                    'reorder_level' => $data['threshold'],
                    'reorder_quantity' => $data['threshold'] * 2,
                    'default_supplier_id' => $supplier?->id,
                    'supplier_unit_price' => 0,
                ]
            );
        }

        return $products;
    }

    private function seedRecipes(array $products, array $rawMaterials): void
    {
        $recipes = [
            'Pandesal' => [
                'Bread Flour' => 80,
                'White Sugar' => 12,
                'Butter' => 5,
            ],
            'Ensaymada' => [
                'Bread Flour' => 100,
                'White Sugar' => 25,
                'Butter' => 15,
                'Eggs' => 1,
            ],
            'Chocolate Cake' => [
                'Bread Flour' => 500,
                'White Sugar' => 350,
                'Butter' => 200,
                'Eggs' => 6,
                'Cake Box 8x8' => 1,
            ],
        ];

        foreach ($recipes as $productName => $ingredients) {
            $recipe = ProductRecipe::updateOrCreate(
                ['product_id' => $products[$productName]->id],
                ['is_active' => true, 'notes' => 'Seeded standard recipe']
            );

            foreach ($ingredients as $materialName => $quantity) {
                $recipe->ingredients()->updateOrCreate(
                    ['raw_material_id' => $rawMaterials[$materialName]->id],
                    ['quantity_per_unit' => $quantity]
                );
            }
        }
    }

    private function seedCustomers(): void
    {
        $customers = [
            ['name' => 'Walk-in', 'customer_type' => 'Walk-in', 'credit_limit' => 0],
            ['name' => 'Maria Santos', 'email' => 'maria.santos@example.com', 'phone' => '09181234567', 'customer_type' => 'Regular', 'credit_limit' => 0],
            ['name' => 'Cafe Luna', 'email' => 'orders@cafeluna.example', 'phone' => '09189998888', 'customer_type' => 'Credit/Loan Customer', 'credit_limit' => 10000],
        ];

        foreach ($customers as $customer) {
            Customer::updateOrCreate(
                ['name' => $customer['name']],
                [
                    'email' => $customer['email'] ?? null,
                    'phone' => $customer['phone'] ?? null,
                    'address' => 'Cebu City',
                    'customer_type' => $customer['customer_type'],
                    'credit_limit' => $customer['credit_limit'],
                    'current_balance' => 0,
                    'credit_due_date' => now()->addDays(30)->toDateString(),
                    'status' => 'Active',
                ]
            );
        }
    }

    private function seedDiscounts(): void
    {
        DiscountType::updateOrCreate(
            ['discount_name' => 'Senior/PWD Discount'],
            [
                'discount_type' => 'Percentage',
                'discount_percentage' => 20,
                'discount_value' => 20,
                'minimum_purchase_amount' => 0,
                'applicable_to' => 'All',
                'applicable_ids' => null,
                'start_date' => now()->subMonth()->toDateString(),
                'end_date' => now()->addYear()->toDateString(),
                'description' => 'Standard discount for eligible customers.',
                'status' => 'Active',
            ]
        );
    }
}
