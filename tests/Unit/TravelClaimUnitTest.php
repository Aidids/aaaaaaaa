<?php

namespace Tests\Unit;

use App\Enums\Status;
use App\Models\TravelClaim;
use App\Models\User;
use App\Services\TravelClaimService;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Auth;
use Tests\TestCase;

class TravelClaimUnitTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();
        $this->user = User::factory()->create();
        $this->approver1 = User::factory()->create(['is_admin' => 1]);
        $this->approver2 = User::factory()->create(['is_admin' => 1]);
    }

    public function test_approve_travel_claim()
    {
        $travel = TravelClaim::create([
            'user_id' => $this->user->id,
            'approvers_id' => json_encode([$this->approver1->id, $this->approver2->id]),
            'approvers_remark' => json_encode(array_fill(
                start_index: 0,
                count: 2,
                value: NULL,
            )),
            'current_approver' => $this->approver1->id,
            'status' => Status::pending->value,
            'isDraft' => 0,
            'month_submission' => '2023-10-01',
        ]);

        $data = [
            'id' => $travel->id,
            'status' => Status::approved->value,
            'remark' => fake()->bs(),
        ];
        $this->be($this->approver1); //mocking Auth::id() in test case

        $service = (new TravelClaimService())->approveTravelClaim($data);
        $this->assertModelExists($service);
    }
}
