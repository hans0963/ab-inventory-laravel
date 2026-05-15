<?php

namespace Database\Seeders;

use App\Models\DiscountType;
use Illuminate\Database\Seeder;

class DiscountTypeSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $discounts = [
            [
                'discount_name' => 'PWD',
                'discount_percentage' => 20.00,
                'description' => 'Persons with Disabilities discount',
                'status' => 'Active'
            ],
            [
                'discount_name' => 'Senior Citizen',
                'discount_percentage' => 20.00,
                'description' => 'Senior Citizen discount',
                'status' => 'Active'
            ],
            [
                'discount_name' => 'Store Discount',
                'discount_percentage' => 10.00,
                'description' => 'General store promotional discount',
                'status' => 'Active'
            ],
            [
                'discount_name' => 'None',
                'discount_percentage' => 0.00,
                'description' => 'No discount applied',
                'status' => 'Active'
            ]
        ];

        foreach ($discounts as $discount) {
            DiscountType::firstOrCreate(
                ['discount_name' => $discount['discount_name']],
                $discount
            );
        }
    }
}
