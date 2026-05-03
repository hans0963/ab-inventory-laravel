<?php

namespace Database\Factories;

use App\Models\InventoryMovement;
use App\Models\Products;
use App\Models\Employee;
use App\Models\Supplier;
use Illuminate\Database\Eloquent\Factories\Factory;

class InventoryMovementFactory extends Factory
{
    protected $model = InventoryMovement::class;

    public function definition(): array
    {
        $balanceForwarded = $this->faker->numberBetween(100, 500);
        $pullOut = $this->faker->numberBetween(0, 20);
        $newLuto = $this->faker->numberBetween(50, 200);
        $newBalance = $balanceForwarded - $pullOut;
        $totalInventory = $newBalance + $newLuto;

        return [
            'product_id' => Products::factory(),
            'employee_id' => Employee::factory(),
            'supplier_id' => Supplier::factory(),
            'date' => $this->faker->dateTimeBetween('-30 days', 'now'),
            'balance_forwarded' => $balanceForwarded,
            'pull_out' => $pullOut,
            'new_balance' => $newBalance,
            'new_luto' => $newLuto,
            'total_inventory' => $totalInventory,
        ];
    }
}
