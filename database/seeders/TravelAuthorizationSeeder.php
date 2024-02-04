<?php

namespace Database\Seeders;

use App\Enums\LeaveRequestStatus;
use App\Models\EForm;
use App\Models\TravelAuthorization;
use App\Models\User;
use Illuminate\Database\Seeder;

class TravelAuthorizationSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $approvers = [180,183];

        $users = User::whereNotIn('id', $approvers)
            ->get();

        foreach ($users as $user)
        {
            EForm::factory()
                ->for(TravelAuthorization::factory()->create([
                    'department_id' => $user->department_id,
                ]), 'eformable')
                ->create([
                'user_id' => $user->id,
                'eformable_type' => TravelAuthorization::class,
                'first_approver_status' => LeaveRequestStatus::approved->value,
                'second_approver_status' => LeaveRequestStatus::approved->value,
                'overall_status' => LeaveRequestStatus::approved->value,
            ]);

            EForm::factory()
                ->for(TravelAuthorization::factory()->create([
                    'department_id' => $user->department_id,
                ]), 'eformable')
                ->create([
                    'user_id' => $user->id,
                    'eformable_type' => TravelAuthorization::class,
                ]);
        }
    }
}
