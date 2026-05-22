<?php

namespace Database\Factories;

use App\Models\Sale;
use App\Models\Product;
use App\Models\Employee;
use Illuminate\Database\Eloquent\Factories\Factory;

class SaleFactory extends Factory
{
    protected $model = Sale::class;

    public function definition(): array
    {
        return [
            'product_id' => Product::factory(),
            'employee_id' => Employee::factory(),
            'date' => $this->faker->dateTimeBetween('-30 days', 'now'),
            'sold' => $this->faker->numberBetween(2, 20),
            'unit_price' => 50,
            'total_amount' => 100,
            'payment_type' => $this->faker->randomElement(['Cash', 'E-Wallet', 'Credit Card']),
            'receipt_number' => 'RCP-' . $this->faker->unique()->numberBetween(100000, 999999),
        ];
    }
}
