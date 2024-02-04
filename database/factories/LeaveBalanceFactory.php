<?php

namespace Database\Factories;

use App\Models\LeaveType;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\LeaveBalance>
 */
class LeaveBalanceFactory extends Factory
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
            'leave_type_id' => LeaveType::factory()->create()->id,
            'balance' => 14,
            'total' => 14,
        ];
    }
}
