<?php

namespace Database\Factories;

use App\Models\Supplier;
use Illuminate\Database\Eloquent\Factories\Factory;

class SupplierFactory extends Factory
{
    protected $model = Supplier::class;

    public function definition(): array
    {
        return [
            'suppliers_name' => $this->faker->name,
            'suppliers_company' => $this->faker->company . ' Supplies Corp.',
            'suppliers_email' => $this->faker->unique()->safeEmail,
            'suppliers_phone' => $this->faker->phoneNumber,
            'suppliers_address' => $this->faker->address,
        ];
    }
}
