<?php

namespace Database\Factories;

use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\LeaveDeductionHistory>
 */
class LeaveDeductionHistoryFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition()
    {
        return [
            'user_id' => User::factory()->create()->id,
            'remark' => fake()->bs(),
            'duration' => 1,
            'hr_ic_id' => User::factory()->create(['is_admin' => 1])->id,
            'hr_ic_date' => fake()->date('Y-m-d'),
        ];
    }
}
