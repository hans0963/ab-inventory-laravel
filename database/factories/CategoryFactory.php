<?php

namespace Database\Factories;

use App\Models\Category;
use Illuminate\Database\Eloquent\Factories\Factory;

class CategoryFactory extends Factory
{
    protected $model = Category::class;

    public function definition(): array
    {
        return [
            'category_name' => $this->faker->randomElement([
                'Classic Breads',
                'Sweet Pastries',
                'Savory Buns',
                'Custom Cakes',
                'Seasonal Specialties',
                'Artisan Cookies',
                'Traditional Desserts',
                'Breakfast Rolls',
                'Gourmet Loaves',
                'Holiday Treats'
            ]) . ' ' . $this->faker->unique()->numberBetween(1, 1000),
            'description' => $this->faker->sentence(10),
        ];
    }
}
