<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\TravelAuthorization>
 */
class TravelAuthorizationFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition()
    {
        return [
            'department_id' => null,
            'travel_purpose' => fake()->randomElement(array(0,1)),
            'main_office' => fake()->randomElement(array(0,1)),
            'reimbursement' => fake()->randomElement(array(0,1)),
            'project_name' => fake()->word(),
            'project_location' => fake()->word,
            'location' => '[{"to": "Miri", "from": "Kuala Lumpur", "end_date": "2023-10-07", "start_date": "2023-10-01", "flight_type": 1, "accomodation": 1}, {"to": "Kemaman", "from": "Miri", "end_date": "2023-10-20", "start_date": "2023-10-07", "flight_type": 2, "accomodation": 1}, {"to": "Kuala Lumpur", "from": "Miri", "end_date": "2023-10-31", "start_date": "2023-10-20", "flight_type": 1, "accomodation": 1}]',
            'purpose' => fake()->bs(),
            'created_at' => fake()->dateTimeThisYear()
        ];
    }
}
