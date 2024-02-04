<?php

namespace Tests\Unit;

use App\Services\ProfileServices;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithoutMiddleware;
use Tests\TestCase;
use App\Models\User;

class ProfileTest extends TestCase
{
    use RefreshDatabase;

    public function test_get_profile()
    {
        $user = User::factory()->create();
        $service = (new ProfileServices())->getProfile($user->id);
        $this->assertModelExists($service);
    }

    public function test_update_profile()
    {
        $user = User::factory()->create();
        $data = [
          'gender' => 'm',
          'contact_no' => '017-8056069',
          'date_of_birth' => '1993-12-26',
          'joining_date' => '2020-01-01'
        ];
        $service = (new ProfileServices())->updateProfile($user->id, $data);
        $this->assertModelExists($service);
    }

    public function test_get_empty_permanent_address()
    {
        $user = User::factory()->create();
        $service = (new ProfileServices())->getPermanentAddress($user->id);
        $this->assertNull($service);
    }

    public function test_update_permanent_address()
    {
        $user = User::factory()->create();
        $data = [
            'details' => 'No 102A jalan datuk sulaiman',
            'city' => 'taman tun dr ismail',
            'state' => 'kuala lumpur',
            'zip' => '934000',
            'country' => 'Malaysia',
            'phone' => '0178056069',
        ];
        $service = (new ProfileServices())->updatePermanentAddress($user->id, $data);
        $this->assertModelExists($service);
    }

}
