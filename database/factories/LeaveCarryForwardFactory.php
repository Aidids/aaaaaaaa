<?php

namespace Database\Factories;

use App\Models\LeaveType;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\LeaveCarryForward>
 */
class LeaveCarryForwardFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition()
    {
        return [
            'start_period' => fake()->randomNumber(),
            'end_period' => fake()->randomNumber(),
            'amount' => fake()->randomNumber(),
        ];
    }
}
