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
            'date_hired' => $this->faker->dateTimeBetween('-3 years', 'now')->format('Y-m-d'),
            'emergency_contact' => $this->faker->name . ' - ' . $this->faker->phoneNumber,
            'status' => 'Active',
        ];
    }
}
