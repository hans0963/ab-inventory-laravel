<?php

namespace Database\Factories;

use App\Models\Sale;
use App\Models\Products;
use App\Models\Employee;
use Illuminate\Database\Eloquent\Factories\Factory;

class SaleFactory extends Factory
{
    protected $model = Sale::class;

    public function definition(): array
    {
        return [
            'product_id' => Products::factory(),
            'employee_id' => Employee::factory(),
            'date' => $this->faker->dateTimeBetween('-30 days', 'now'),
            'sold' => $this->faker->numberBetween(10, 50),
        ];
    }
}
