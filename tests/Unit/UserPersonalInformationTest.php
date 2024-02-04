<?php

namespace Tests\Unit;

use App\Models\User;
use App\Services\UserInformationService;
use Tests\TestCase;

class UserPersonalInformationTest extends TestCase
{
    public function test_update_personal_information()
    {
        $user = User::factory()->create();
        $data = [
            'ic_no' => '000123-45-6789',
            'passport_no' => fake()->bs(),
            'date_of_birth' => '2023-09-01',
            'place_of_birth' => fake()->bs(),
            'race' => fake()->bs(),
            'religion' => fake()->bs(),
            'nationality' => fake()->bs(),
        ];
        $service = (new UserInformationService())->storeInformation(
            array_merge( ['user_id' => $user->id], $data )
        );
        $this->assertIsString('Personal information update Successful', $service['message']);
    }

    public function test_update_family_detail()
    {
        $user = User::factory()->create();
        $data = [
            'marital_status' => 1,
            'spouse_name' => fake()->bs(),
            'spouse_ic_no' => '000123-45-6789',
            'spouse_work' => rand(0,1),
        ];
        $service = (new UserInformationService())->storeFamilyDetail(
            array_merge( ['user_id' => $user->id], $data )
        );
        $this->assertIsString('Family Detail update Successful', $service['message']);
    }
}
