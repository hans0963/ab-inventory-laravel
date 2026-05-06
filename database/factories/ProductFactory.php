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
        return [
            'product_name' => $this->faker->randomElement([
                'Premium Pandesal',
                'Classic Ensaymada',
                'Spanish Bread',
                'Ube Cheese Pandesal',
                'Cheese Roll',
                'Pan de Coco',
                'Monay',
                'Hopia Baboy',
                'Chocolate Crinkles',
                'Otap',
                'Barquillos',
                'Mamon',
                'Bibingka',
                'Puto Bumbong',
                'Cassava Cake',
                'Egg Pie',
                'Buko Pie',
                'Banana Cake',
                'Carrot Cake',
                'Pianono'
            ]) . ' (' . $this->faker->unique()->numberBetween(1, 10000) . ')',
            'category_id' => Category::factory(),
            'buying_price' => $this->faker->randomFloat(2, 5, 50),
            'selling_price' => $this->faker->randomFloat(2, 60, 150),
            'quantity' => $this->faker->numberBetween(50, 500),
            'stock_alert_threshold' => 20,
        ];
    }
}
