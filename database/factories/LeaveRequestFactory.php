<?php

namespace Database\Factories;

use App\Enums\LeaveRequestStatus;
use App\Models\LeaveBalance;
use App\Models\LeaveType;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\LeaveRequest>
 */
class LeaveRequestFactory extends Factory
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
            'leave_balance_id' => LeaveBalance::factory()->create()->id,
            'start_date' => '2024-01-01',
            'end_date' => '2024-01-01',
            'duration' => 1,
            'overall_status' => LeaveRequestStatus::pending->value
        ];
    }
}
