<?php

namespace Tests\Feature;

use App\Enums\LeaveRequestStatus;
use App\Models\LeaveBalance;
use App\Models\LeaveType;
use App\Models\RedeemReplacementLeave;
use App\Models\User;
use Carbon\Carbon;
use Illuminate\Foundation\Testing\DatabaseTransactions;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

use Tests\Feature\AssignUserLeaveBalanceFeatureTest;

class RedeemReplacementFeatureTest extends TestCase
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
        RedeemReplacementLeave::unsetEventDispatcher();

        self::$userBalance->setUser();
        $this->user = self::$userBalance->userMale4;
        $this->admin = self::$userBalance->admin;
        $this->approver1 = self::$userBalance->approver1;
        $this->approver2 = self::$userBalance->approver2;

        $this->now = Carbon::now()->timezone('Asia/Kuala_Lumpur');
    }

    public function test_redeem_replacement_index_page_get_redeem_replacement_only()
    {
        $redeemReplacement = RedeemReplacementLeave::factory()->create([
            'user_id' => $this->user->id,
        ]);

        $response = $this->actingAs($this->user)->get('/api/redeem-replacement-leave/');
        $response->assertJson([
            'data' => array([
                'id' => $redeemReplacement->id,
                'user_id' => $redeemReplacement->user_id,
                'start_date' => Carbon::parse($redeemReplacement->start_date)->format('d M Y'),
                'end_date' => Carbon::parse($redeemReplacement->end_date)->format('d M Y'),
                'overall_status' => $redeemReplacement->overall_status
            ])
        ]);
    }

    public function test_apply_redeem_replacement_leave()
    {
        $response = $this->actingAs($this->user)->postJson(
            '/api/redeem-replacement-leave/apply', $this->storeRedeemReplacementLeaveData()
        );
        $response->assertOk();
    }

    public function test_edit_redeem_replacement_leave()
    {
        $redeemReplacement = RedeemReplacementLeave::factory()->create([
            'user_id' => $this->user->id,
        ]);

        $response = $this->actingAs($this->user)->postJson(
            '/api/redeem-replacement-leave/edit', $this->editRedeemReplacementData($redeemReplacement->id)
        );
        $response->assertStatus(200);
    }

    public function test_approver_only_get_pending_redeem_replacement_assigned_to_them()
    {
        $redeemReplacement = RedeemReplacementLeave::factory()->create([
            'user_id' => $this->user->id,
            'first_approver_id' => $this->approver1->id,
            'first_approver_status' => 'pending'
        ]);

        $response = $this->actingAs($this->approver1)
            ->getJson('/api/redeem-replacement-leave/approve');
        $response->assertOk();

        $response->assertJson([
            'data' => array([
                'start_date' => Carbon::parse($redeemReplacement->start_date)->format('d M Y'),
                'end_date' => Carbon::parse($redeemReplacement->end_date)->format('d M Y'),
                'first_approver_status' => 'pending',
                'overall_status' => 'pending',
            ])
        ]);
    }

    public function test_redeem_replacement_approved_by_first_approver()
    {
        $redeemReplacement = $this->create_redeem_replacement_record(
            user: $this->user,
            approver1: $this->approver1,
        );

        $this->get_pending_for_approver($this->approver1, 'first');

        $response = $this->approver_approved_or_reject_redeem_replacement(
            redeemReplacementID: $redeemReplacement->id,
            approver: $this->approver1,
            approverType: 'first',
            status: 'approved',
        );

        $response->assertJson([
            'message' => 'You have fully approved this request',
        ]);
    }

    public function test_redeem_replacement_approved_by_second_approver()
    {
        $redeemReplacement = $this->create_redeem_replacement_record(
            user: $this->user,
            approver2: $this->approver2,
        );

        $this->get_pending_for_approver($this->approver2, 'second');

        $response = $this->approver_approved_or_reject_redeem_replacement(
            redeemReplacementID: $redeemReplacement->id,
            approver: $this->approver2,
            approverType: 'second',
            status: 'approved',
        );

        $response->assertJson([
            'message' => 'You have fully approved this request',
        ]);
    }

    public function test_first_approve_and_second_reject()
    {
        $redeemReplacement = $this->create_redeem_replacement_record(
            user: $this->user,
            approver1: $this->approver1,
            approver2: $this->approver2,
        );

        // first approver get pending and approved redeem replacement
        $this->get_pending_for_approver($this->approver1, 'first');
        $response = $this->approver_approved_or_reject_redeem_replacement(
            redeemReplacementID: $redeemReplacement->id,
            approver: $this->approver1,
            approverType: 'first',
            status: 'approved',
        );
        $response->assertJson([
            'message' => 'You have approved this request',
        ]);

        // second approver rejected redeem replacement
        $response = $this->approver_approved_or_reject_redeem_replacement(
            redeemReplacementID: $redeemReplacement->id,
            approver: $this->approver2,
            approverType: 'second',
            status: 'rejected',
        );
        $response->assertJson([
            'message' => 'You have rejected this request',
        ]);
    }

    public function test_second_approver_reject_after_first_approve()
    {
        $redeemReplacement = $this->create_redeem_replacement_record(
            user: $this->user,
            approver1: $this->approver1,
            approver2: $this->approver2,
        );

        // first approver get pending and reject redeem replacement
        $this->get_pending_for_approver($this->approver1, 'first');
        $response = $this->approver_approved_or_reject_redeem_replacement(
            redeemReplacementID: $redeemReplacement->id,
            approver: $this->approver1,
            approverType: 'first',
            status: 'rejected',
        );
        $response->assertJson([
            'message' => 'You have rejected this request',
        ]);

        // second approver approved after first approver rejected redeem replacement
        $response = $this->approver_approved_or_reject_redeem_replacement(
            redeemReplacementID: $redeemReplacement->id,
            approver: $this->approver2,
            approverType: 'second',
            status: 'approved',
        );
        $response->assertJson([
            'message' => 'This request has been rejected by another approver.',
        ]);
    }

    public function test_hr_approve_redeem_replacement_and_replacement_balance_added()
    {
        $redeemReplacement = $this->create_redeem_replacement_record(
            user: $this->user,
            approver1: $this->approver1,
        );

        $this->approver_approved_or_reject_redeem_replacement(
            redeemReplacementID: $redeemReplacement->id,
            approver: $this->approver1,
            approverType: 'first',
            status: 'approved',
        );

        $response = $this->hr_approve_or_reject_redeem_replacement(
            redeemReplacementID: $redeemReplacement->id,
            status: 'approved',
        );

        $response->assertOk();

        //check user balance for replacement leave after hr approved
        $this->checkUserLeaveBalance(balance: 1);
    }

    public function test_hr_reject_and_balance_not_added()
    {
        $redeemReplacement = $this->create_redeem_replacement_record(
            user: $this->user,
            approver1: $this->approver1,
        );

        $this->approver_approved_or_reject_redeem_replacement(
            redeemReplacementID: $redeemReplacement->id,
            approver: $this->approver1,
            approverType: 'first',
            status: 'approved',
        );

        $response = $this->hr_approve_or_reject_redeem_replacement(
            redeemReplacementID: $redeemReplacement->id,
            status: 'rejected',
        );

        $response->assertOk();

        //check user balance is 0 after hr rejected for replacement leave
        $this->checkUserLeaveBalance();
    }

    public function test_user_balance_expired_and_deducted()
    {
        $this->test_hr_approve_redeem_replacement_and_replacement_balance_added();

        // retrieve user redeem replacement leave
        $redeemReplacement = RedeemReplacementLeave::where([
            'user_id' => $this->user->id
        ])->first();
        $redeemReplacement->update(['expired_date' => $this->now->toDateString()]);

        // simulate that user replacement leave has expired
        $this->artisan('replacement:expired');

        //Check if user expired balance has been deducted
        $leaveBalance = LeaveBalance::where('leave_type_id',
            LeaveType::where('name', 'Replacement Leave')->value('id')
        )->first();

        $this->checkUserLeaveBalance();
    }

///////////////////////////////////////////////////////////////////////////////////////////////////

    private function storeRedeemReplacementLeaveData()
    {
        return [
            'user_id' => $this->user->id,
            'start_date' => '2023-08-08',
            'end_date' => '2023-08-08',
            'first_approver_id' => $this->approver1->id,
            'first_approver_status' => LeaveRequestStatus::pending->value,
        ];
    }

    private function editRedeemReplacementData(int $redeemReplacementID)
    {
        return [
            'id' => $redeemReplacementID,
            'start_date' => '2023-08-10',
            'end_date' => '2023-08-10',
        ];
    }

    private function create_redeem_replacement_record(User $user, User $approver1 = null, User $approver2 = null)
    {
        $data = [
            'user_id' => $user->id,
            'first_approver_id' => $approver1 ? $approver1->id : null,
            'second_approver_id' => $approver2 ? $approver2->id : null,
            'first_approver_status' => $approver1 ? 'pending' : null,
            'second_approver_status' => $approver2 ? 'pending' : null,
        ];

        return RedeemReplacementLeave::factory()->create($data);
    }

    private function get_pending_for_approver(User $approver, String $approverType)
    {
        $response = $this->actingAs($approver)
            ->getJson('/api/redeem-replacement-leave/approve');
        $response->assertJson([
            'data' => array([
                $approverType . '_approver_id' => $approver->id,
                $approverType . '_approver_status' => LeaveRequestStatus::pending->value,
            ])
        ]);
    }

    private function approver_approved_or_reject_redeem_replacement(int $redeemReplacementID, User $approver, String $approverType, String $status)
    {
        $data = [
            'id' => $redeemReplacementID,
            $approverType.'_approver_status' => $status,
            $approverType.'_approver_remark' => fake()->bs(),
            $approverType.'_approver_date' =>  fake()->date('Y-m-d'),
        ];

        return $this->actingAs($approver)
            ->postJson('/api/redeem-replacement-leave/approve', $data);
    }

    private function hr_approve_or_reject_redeem_replacement(int $redeemReplacementID, String $status)
    {
        $hrApproveData = [
            'id' => $redeemReplacementID,
            'hr_id' => $this->admin->id,
            'hr_status' => $status,
            'hr_remark' => fake()->bs(),
            'hr_date' => fake()->date('Y-m-d'),
            'added_qty' => 1,
        ];

        return $this->actingAs($this->admin)
            ->postJson('/api/redeem-replacement-leave/summary', $hrApproveData);
    }

    private function checkUserLeaveBalance($balance = 0): void
    {
        $replacementLeaveType = LeaveType::where('name', 'Replacement Leave')->first();
        $response = $this->actingAs($this->user)->getJson('/api/leave-balance/'.$this->user->id);
        $response->assertJsonFragment([
            'leave_type_id' => $replacementLeaveType->id,
            'balance' => $balance,
        ]);
    }
}
