<?php

namespace Database\Factories;

use App\Models\Employee;
use Illuminate\Database\Eloquent\Factories\Factory;

class EmployeeFactory extends Factory
{
    protected $model = Employee::class;

    public function definition(): array
    {
        return [
            'employee_name' => $this->faker->name,
            'employee_email' => $this->faker->unique()->safeEmail,
            'employee_phone' => $this->faker->phoneNumber,
            'position' => $this->faker->randomElement(['General Manager', 'Head Baker', 'Inventory Supervisor', 'Cashier', 'Kitchen Assistant']),
        ];
    }
}
