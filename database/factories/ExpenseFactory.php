<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Expense>
 */
class ExpenseFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition()
    {
        $options =  [
            'Accomodation', 'Refreshment',
            'Telephone', 'Laundry', 'Others (' .fake()->bs().')',
        ];
        $randomKey = array_rand($options);
        $type = $options[$randomKey];

        return [
            'description' => $type,
            'account_code' => fake()->uuid(),
            'total_hours' => fake()->randomDigit(),
            'amount' => fake()->randomFloat(2,100,2000),
            'remark' => fake()->bs(),
        ];
    }
}
