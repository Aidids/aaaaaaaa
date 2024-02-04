<?php

namespace Database\Factories;

use App\Enums\Status;
use App\Models\User;
use Carbon\Carbon;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\RedeemOffshoreLeave>
 */
class RedeemOffshoreLeaveFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition()
    {
        $start_date = fake()->date();
        $end_date = Carbon::parse($start_date)->copy()->endOfMonth()->toDateString();

        return [
            'user_id' => User::factory()->create()->id,
            'start_date' => $start_date,
            'end_date' => $end_date,
            'remark' => fake()->bs(),
            'overall_status' => Status::pending->value,
        ];
    }
}
