<?php

namespace Database\Factories;

use App\Enums\LeaveRequestStatus;
use App\Models\LeaveBalance;
use App\Models\User;
use Carbon\Carbon;
use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Foundation\Testing\WithFaker;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\RedeemReplacementLeave>
 */
class RedeemReplacementLeaveFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition()
    {
        $start_date = fake()->date('Y-m-d');
        $end_date = Carbon::parse($start_date)->copy()->endOfMonth()->toDateString();

        return [
            'user_id' => User::factory()->create()->id,
            'remark' => fake()->bs(),
            'start_date' => $start_date,
            'end_date' => $end_date,
            'overall_status' => LeaveRequestStatus::pending->value,
        ];
    }
}
