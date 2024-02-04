<?php

namespace Tests;

use App\Enums\LeaveRequestStatus;
use App\Models\Approver;
use App\Models\EForm;
use App\Models\TravelAuthorization;
use App\Models\User;
use Illuminate\Database\Eloquent\Model;

trait ArrangeData
{
    protected function setUp(): void
    {
        parent::setUp();
        EForm::unsetEventDispatcher();
        $this->user = $this->createUser();
        $this->randomUser = $this->createUser();
        $this->approver1 = $this->createUser(['approver_id' => Approver::factory()->create()->id]);
        $this->approver2 = $this->createUser(['approver_id' => Approver::factory()->create()->id]);
        $this->admin = $this->createUser(['is_admin' => true]);

        $this->EFORM = [new TravelAuthorization()];
    }

    public function createUser(Array $data = []): User
    {
        $joinDate = ['joining_date' => '2020-01-01'];
        return User::factory()->create(array_merge($data,$joinDate));
    }

    public function createEform(User $user, Model $model)
    {
        if ($model == 'App\Models\TravelAuthorization') {
            return EForm::factory()
                ->for($model::factory()->create([
                    'department_id' => $user->department_id,
                ]), 'eformable')
                ->create([
                    'user_id' => $user->id,
                    'eformable_type' => get_class($model),
                    'first_approver_status' => LeaveRequestStatus::approved->value,
                    'second_approver_status' => LeaveRequestStatus::approved->value,
                    'overall_status' => LeaveRequestStatus::approved->value,
                ]);
        }

        return null;
    }

    private function convertName(Model $Model): String
    {
        $text = explode('\\', get_class($Model))[2];

        return strtolower(preg_replace('/(?<=\w)([A-Z])/', '-$1', $text));
    }

    private function data(Model $model, int $approvers = 0)
    {
        $approver_data = match ($approvers) {
            1 => [
                'first_approver_id' => $this->approver1->id,
                'first_approver_status' => LeaveRequestStatus::pending->value,
            ],
            2 => [
                'second_approver_id' => $this->approver2->id,
                'second_approver_status' => LeaveRequestStatus::pending->value,
            ],
            default => [
                'first_approver_id' => $this->approver1->id,
                'first_approver_status' => LeaveRequestStatus::pending->value,

                'second_approver_id' => $this->approver2->id,
                'second_approver_status' => LeaveRequestStatus::pending->value,
            ],
        };

        return match (get_class($model)) {
            'App\Models\TravelAuthorization' => array_merge(
                [
                    'user_id' => $this->user->id,
                    'department_id' => $this->user->department_id,
                    'travel_purpose' => fake()->randomElement(array(0, 1)),
                    'main_office' => fake()->randomElement(array(0, 1)),
                    'reimbursement' => fake()->randomElement(array(0, 1)),
                    'project_name' => fake()->word(),
                    'project_location' => fake()->word,
                    'location' => '[{"to": "Miri", "from": "Kuala Lumpur", "end_date": "2023-10-07", "start_date": "2023-10-01", "flight_type": 1, "accomodation": 1},
                    {"to": "Kemaman", "from": "Miri", "end_date": "2023-10-20", "start_date": "2023-10-07", "flight_type": 2, "accomodation": 1},
                    {"to": "Kuala Lumpur", "from": "Miri", "end_date": "2023-10-31", "start_date": "2023-10-20", "flight_type": 1, "accomodation": 1}]',
                    'purpose' => fake()->bs()
                ],
                $approver_data
            ),
            default => [],
        };
    }

    private function updateData(Model $model)
    {
        return match (get_class($model)) {
            'App\Models\TravelAuthorization' => [
                'travel_purpose' => fake()->randomElement(array(0, 1)),
                'main_office' => fake()->randomElement(array(0, 1)),
                'reimbursement' => fake()->randomElement(array(0, 1)),
                'project_name' => fake()->word(),
                'project_location' => fake()->word,
                'location' => '[{"to": "Miri", "from": "Kuala Lumpur", "end_date": "2023-10-07", "start_date": "2023-10-01", "flight_type": 1, "accomodation": 1},
                    {"to": "Kemaman", "from": "Miri", "end_date": "2023-10-20", "start_date": "2023-10-07", "flight_type": 2, "accomodation": 1},
                    {"to": "Kuala Lumpur", "from": "Miri", "end_date": "2023-10-31", "start_date": "2023-10-20", "flight_type": 1, "accomodation": 1}]',
                'purpose' => fake()->bs()
            ],
            default => [],
        };
    }

    private function applyEformApi(String $api, Array $data)
    {
        $userApplyForm = $this->actingAs($this->user)->postJson('api/'. $api .'/apply', $data);

        $approver1Response = $this->actingAs($this->approver1)->getJson('api/'. $api .'/approver');
        $approver1Response->assertStatus(200)
            ->assertJsonFragment([
                $userApplyForm->json('data')
            ]);

        $approver2Response = $this->actingAs($this->approver2)->getJson('api/'. $api .'/approver');
        $approver2Response->assertStatus(200)
            ->assertJsonMissing([
                $userApplyForm->json('data')
            ]);

        return $userApplyForm;
    }
}
