<?php

namespace Tests\Feature;

use App\Models\Approver;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Tests\TestCase;

class ApproverFeatureTest extends TestCase
{
    protected function setUp(): void
    {
        parent::setUp();
        $this->user = $this->createUser();
    }

    public function test_get_list_of_approvers()
    {
        $response = $this->actingAs($this->user)->getJson('/api/approver');
        $response->assertStatus(200);
    }

    public function test_assigning_approver_as_approver_level_one()
    {
        $user = $this->createUser();
        $approver = $this->createApprover();

        $request = [
            'user_id' => $user->id,
            'approver_id' => $approver->id,
        ];

        $response = $this->actingAs($this->user)
            ->postJson('/api/approver/assign',
                $request);

        $response->assertOk();
    }

    public function test_get_everyone_including_approver_id_null()
    {
        $approver = $this->createApprover();

        User::factory(50)->create([
            'approver_id' => $approver->id,
        ]);

        $response = $this->actingAs($this->user)->getJson('/api/approver/all');
        $response->assertOk();
    }


    private function createUser()
    {
        return User::factory()->create();
    }

    private function createApprover()
    {
        return Approver::factory()->create();
    }
}
