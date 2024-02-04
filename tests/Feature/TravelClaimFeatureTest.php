<?php

namespace Tests\Feature;

use App\Models\FixedApprover;
use App\Models\TravelClaim;
use Carbon\Carbon;
use Illuminate\Foundation\Testing\DatabaseTransactions;
use Tests\TestCase;


//BEFORE RUNNING THIS TEST, MAKE SURE RUN AssignUserLeaveBalanceFeatureTest FIRST
class TravelClaimFeatureTest extends TestCase
{
    use DatabaseTransactions;

    private static $userBalance;

    public static function setUpBeforeClass(): void
    {
        self::$userBalance = new AssignUserLeaveBalanceFeatureTest();
    }

    protected function setUp(): void
    {
        parent::setUp();
        TravelClaim::unsetEventDispatcher();
        self::$userBalance->setUser();

        $this->user = self::$userBalance->userMale4;
        $this->approver1 = self::$userBalance->approver1;
        $this->approver2 = self::$userBalance->approver2;

        $approversID = [$this->approver1->id, $this->approver2->id];
        FixedApprover::updateOrCreate([
            'user_id' => $this->user->id,
        ],[
            'approvers_id' => json_encode($approversID),
        ]);

        $this->now = Carbon::now()->timezone('Asia/Kuala_Lumpur');
    }

    public function test_index_travel_claim()
    {
        $travel = $this->actingAs($this->user)->get('/api/travel-claim/');
        $travel->assertCreated();
    }

    public function test_store_travel_claim()
    {
        $travel = $this->createTravel();

        $response = $this->actingAs($this->user)
            ->postJson('/api/travel-claim/', $this->data($travel->id));

        $response->assertJsonFragment([
            'id' => $travel->id,
            'submission_month' => '11 November 2023',
        ]);
    }

    public function test_create_and_store_allowances($travel = false)
    {
        (!$travel) && $travel = $this->createTravel();

        $response = $this->actingAs($this->user)
            ->postJson('/api/travel-claim/' .$travel->id. '/allowance-add');
        $response->assertCreated();

        $allowance = [
            'travel_id' => $travel->id,
            'start_date' => '2023-11-11',
            'end_date' => '2023-11-11',
            'allowance_type' => 'Outstation',
            'allowance_rate' => 13.3,
            'amount' => 13.3,
            'remark' => 'test',
        ];

        $response = $this->actingAs($this->user)
            ->postJson('/api/travel-claim/' .$travel->id. '/allowance', $allowance);
        $response->assertStatus(201);
    }

    public function test_create_and_store_transports($travel = false)
    {
        (!$travel) && $travel = $this->createTravel();

        $response = $this->actingAs($this->user)
            ->postJson('/api/travel-claim/' .$travel->id. '/transport-add');
        $response->assertCreated();

        $transport = [
            'travel_id' => $travel->id,
            'date' => '2023-11-11',
            'start_location' => 'KL OFFICE(VSQ)',
            'end_location' => 'TCOT KERTEH',
            'total_distance' => '10',
            'rate' => 13.3,
            'remark' => 'test',
        ];

        $response = $this->actingAs($this->user)
            ->postJson('/api/travel-claim/' .$travel->id. '/transport', $transport);
        $response->assertStatus(201);
    }

    public function test_create_and_store_expenses($travel = false)
    {
        (!$travel) && $travel = $this->createTravel();

        $response = $this->actingAs($this->user)
            ->postJson('/api/travel-claim/' .$travel->id. '/expense-add');
        $response->assertCreated();

        $expense = [
            'travel_id' => $travel->id,
            'description' => 'test expense description',
            'account_code' => 'test account code',
            'total_hours' => 12,
            'amount' => 120,
            'remark' => 'test expense remark',
        ];

        $response = $this->actingAs($this->user)
            ->postJson('/api/travel-claim/' .$travel->id. '/expense', $expense);
        $response->assertStatus(201);
    }

    public function test_cancel_travel_claim()
    {
        $travel = $this->createTravel();

        $response = $this->actingAs($this->user)
            ->postJson('/api/travel-claim/', $this->data($travel->id));
        $response->assertJsonFragment([
            'id' => $travel->id,
            'submission_month' => '11 November 2023',
            'isDraft' => false,
        ]);

        $data = ['travel_id' => $travel->id];
        $response = $this->actingAs($this->user)
            ->postJson('/api/travel-claim/cancel', $data);
        $response->assertAccepted();
    }

    public function test_reset_travel_claim_delete_all_child()
    {
        $travel = $this->createTravel();
        $response = $this->actingAs($this->user)
            ->postJson('/api/travel-claim/', $this->data($travel->id));
        $response->assertAccepted();

        $this->test_create_and_store_expenses($travel);
        $this->test_create_and_store_transports($travel);
        $this->test_create_and_store_expenses($travel);

        $response = $this->actingAs($this->user)
            ->deleteJson('/api/travel-claim/reset', $this->data($travel->id));
        $response->assertJsonFragment([
            'message' => "Travel Claim data delete successful.",
        ]);
    }

    private function createTravel()
    {
        return TravelClaim::factory()->create([
            'user_id' => $this->user->id,
            'isDraft' => false,
        ]);
    }

    private function data(int $travelID)
    {
        return [
            'id' => $travelID,
            'submission_month' => '2023-11-11',
            'isDraft' => false,
        ];
    }
}
