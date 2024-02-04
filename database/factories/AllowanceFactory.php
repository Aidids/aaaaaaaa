<?php

namespace Database\Factories;

use App\Models\TravelClaim;
use Carbon\Carbon;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Allowance>
 */
class AllowanceFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition()
    {
        $allowance_type = [
            'MealAllowance' => [
                'allowance_type' => 'Meal Allowance',
                'allowance_rate' => fake()->randomElement([25,50,75]),
                'meal_total_hours' =>  fake()->randomElement([4,8,12]),
            ],

            'Outstation' => [
                'allowance_type' => 'Outstation',
                'allowance_rate' => 70,
                'meal_total_hours' => null,
            ],

            'Oversea(Executive)' => [
                'allowance_type' => 'Oversea (Executive)',
                'allowance_rate' => 200.00,
                'meal_total_hours' => null,
            ],

            'Oversea(Management)' => [
                'allowance_type' => 'Oversea (Management)',
                'allowance_rate' => 250.00,
                'meal_total_hours' => null,
            ],

            'Oversea(Sr.Management)' => [
                'allowance_type' => 'Oversea (Sr.Management)',
                'allowance_rate' => 350.00,
                'meal_total_hours' => null,
            ],

            'Offshore(Category 2)' => [
                'allowance_type' => 'Offshore (Category 2)',
                'allowance_rate' => 100.00,
                'meal_total_hours' => null,
            ],

            'others' => [
                'allowance_type' => 'Others (' . fake()->bs() . ')',
                'allowance_rate' => fake()->randomFloat(2, 50, 1000),
                'meal_total_hours' => null,
            ],
        ];

        $randomize = fake()->randomElement($allowance_type);

        $startDate = fake()->dateTimeBetween('-1 week', 'now');
        $endDate = fake()->dateTimeBetween('+1 week', '+2 week');

        $days = Carbon::parse($startDate)->diffInDays(Carbon::parse($endDate));

        return [
            'start_date' => $startDate,
            'end_date' => $endDate,
            'allowance_type' => $randomize['allowance_type'],
            'allowance_rate' => $randomize['allowance_rate'],
            'meal_total_hours' => ($randomize['meal_total_hours']),
            'amount' => $days * $randomize['allowance_rate'],
            'remark' => fake()->bs(),
        ];
    }
}
