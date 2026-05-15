<?php

namespace Database\Factories;

use App\Models\Product;
use App\Models\Category;
use Illuminate\Database\Eloquent\Factories\Factory;

class ProductFactory extends Factory
{
    protected $model = Product::class;

    public function definition(): array
    {
        $productsByCategory = [
            'Bread and Rolls' => ['Artisan Sourdough', 'French Baguette', 'Whole Wheat Loaf', 'Classic Pandesal', 'Dinner Rolls', 'Brioche Buns', 'Ciabatta'],
            'Pastries' => ['Butter Croissant', 'Pain au Chocolat', 'Apple Turnover', 'Cheese Danish', 'Blueberry Tart', 'Palmier', 'Cinnamon Roll'],
            'Cakes' => ['Red Velvet Cake', 'Chocolate Ganache Cake', 'Strawberry Shortcake', 'New York Cheesecake', 'Ube Chiffon Cake', 'Mocha Pound Cake'],
            'Cookies and Biscuits' => ['Chocolate Chip Cookies', 'Oatmeal Raisin Cookies', 'Double Chocolate Crinkles', 'Butter Shortbread', 'Snickerdoodles', 'Biscotti'],
            'Quick Breads and Muffins' => ['Banana Walnut Bread', 'Blueberry Muffin', 'Lemon Poppyseed Scone', 'Double Chocolate Muffin', 'Zucchini Bread'],
            'Pies and Tarts' => ['Classic Apple Pie', 'Egg Tart', 'Buko Pie', 'Lemon Meringue Tart', 'Peach Mango Pie', 'Pumpkin Pie'],
            'Savory Goods' => ['Chicken Empanada', 'Ham and Cheese Puffs', 'Savory Quiche', 'Garlic Breadsticks', 'Soft Pretzels', 'Sausage Rolls'],
            'Donuts and Fried Products' => ['Glazed Donut', 'Chocolate Frosted Donut', 'Bavarian Cream Filled', 'Spanish Churros', 'Beignets', 'Twisted Donut'],
        ];

        return [
            'product_name' => $this->faker->unique()->word(), // Placeholder, will be overridden in seeder
            'category_id' => Category::factory(),
            'buying_price' => 0,
            'selling_price' => $this->faker->randomFloat(2, 25, 250),
            'quantity' => $this->faker->numberBetween(20, 100),
            'stock_alert_threshold' => 10,
        ];
    }
}
