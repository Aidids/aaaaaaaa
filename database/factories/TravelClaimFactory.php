<?php

namespace Database\Factories;

use App\Enums\Status;
use App\Models\Allowance;
use App\Models\TravelClaim;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\TravelClaim>
 */
class TravelClaimFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition()
    {
        $status = [
            'hr' => [
                'id' => 0,
                'status' => Status::processing->value
            ],
            'alias' => [
                'id' => 145,
                'status' => Status::processing->value
            ],
            'tengku' => [
                'id' => 179,
                'status' => fake()->randomElement([Status::processing->value,Status::approved->value]),
            ],
        ];

        $shuffle = fake()->randomElement($status);

        return [
            'approvers_id' => '[180, 183, 0, 145, 179]',
            'current_approver' => $shuffle['id'],
            'approvers_remark' => '[null,null,null,null,null]',
            'status' => $shuffle['status'],
            'submission_month' => fake()->dateTimeThisYear(),
            'isDraft' => false,
        ];
    }
}
