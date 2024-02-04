<?php

namespace Database\Factories;

use App\Enums\LeaveRequestStatus;
use App\Models\TravelAuthorization;
use App\Models\User;
use Carbon\Carbon;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Eform>
 */
class EformFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition()
    {
        return [
            'first_approver_id' => 180,
            'first_approver_status' => fake()->randomElement(array(LeaveRequestStatus::pending->value,
                LeaveRequestStatus::approved->value)),
            'first_approver_date' => Carbon::now(),
            'second_approver_id' => 183,
            'second_approver_status' => LeaveRequestStatus::pending->value,
            'second_approver_date' => Carbon::now(),
            'overall_status' => LeaveRequestStatus::pending->value,
        ];
    }
}
