<?php

namespace Database\Factories;

use App\Models\RawMaterial;
use Illuminate\Database\Eloquent\Factories\Factory;

class RawMaterialFactory extends Factory
{
    protected $model = \App\Models\RawMaterial::class;

    public function definition(): array
    {
        return [
            'material_name' => $this->faker->randomElement([
                'All-Purpose Flour',
                'Refined Sugar',
                'Unsalted Butter',
                'Whole Milk',
                'Active Dry Yeast',
                'Iodized Salt',
                'Fresh Eggs',
                'Ube Halaya',
                'Cheddar Cheese',
                'Desiccated Coconut'
            ]) . ' ' . $this->faker->unique()->numberBetween(1, 1000),
            'quantity' => $this->faker->numberBetween(10, 100),
            'unit' => $this->faker->randomElement(['kg', 'liters', 'tray', 'pack']),
            'expiration_date' => $this->faker->dateTimeBetween('+1 month', '+6 months'),
        ];
    }
}
