<?php

namespace Tests\Feature;

use App\Enums\Status;
use App\Models\LeaveType;
use App\Models\RedeemOffshoreLeave;
use App\Models\User;
use Carbon\Carbon;
use Illuminate\Foundation\Testing\DatabaseTransactions;
use Illuminate\Support\Facades\Artisan;
use Psy\Util\Str;
use Tests\TestCase;

class RedeemOffshoreFeatureTest extends TestCase
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
        RedeemOffshoreLeave::unsetEventDispatcher();

        self::$userBalance->setUser();
        $this->user = self::$userBalance->userMale4;
        $this->admin = self::$userBalance->admin;
        $this->approver1 = self::$userBalance->approver1;
        $this->approver2 = self::$userBalance->approver2;

        $this->now = Carbon::now()->timezone('Asia/Kuala_Lumpur');
    }

    public function test_redeem_offshore_index_page_get_theirs_own_only()
    {
        $redeemOffshore = RedeemOffshoreLeave::factory()->create([
            'user_id' => $this->user->id,
        ]);

        $response = $this->actingAs($this->user)
            ->get('/api/redeem-offshore-leave/');

        $response->assertJson([
            'data' => array([
                'id' => $redeemOffshore->id,
                'user_id' => $redeemOffshore->user_id,
            ])
        ]);
    }

    public function test_apply_redeem_offshore()
    {
        $response = $this->actingAs($this->user)
            ->postJson('/api/redeem-offshore-leave/apply', $this->storeRedeemOffshoreData());

        $response->assertCreated();
    }

    public function test_edit_redeem_offshore()
    {
        $this->test_apply_redeem_offshore();
        $redeemOffshore = RedeemOffshoreLeave::where('user_id', $this->user->id)->first();

        $response = $this->actingAs($this->user)
            ->postJson('/api/redeem-offshore-leave/edit', $this->editData($redeemOffshore->id));

        $response->assertStatus(202);
    }

    public function test_approver_only_get_pending_redeem_offshore_assigned_to_them()
    {
        $this->test_apply_redeem_offshore();

        $response = $this->actingAs($this->approver1)
            ->getJson('/api/redeem-offshore-leave/approve');
        $response->assertOk();
    }

    public function test_redeem_offshore_approved_by_first_approver()
    {
        $this->test_apply_redeem_offshore();
        $redeemOffshore = RedeemOffshoreLeave::where('user_id', $this->user->id)->first();

        $this->addSecondApproverToRedeemOffshoreRecord($redeemOffshore);
        $this->getPendingForApprover($this->approver1, 'first');

        $response = $this->approver_approved_or_reject_redeem_offshore(
            approver: $this->approver1,
            level: 'first',
            status: 'approved',
            redeemOffshoreLeave: $redeemOffshore,
        );

        $response->assertJson([
            'message' => 'You have approved this request'
        ]);
    }

    public function test_redeem_offshore_approved_by_second_approver()
    {
        $this->test_apply_redeem_offshore();
        $this->test_redeem_offshore_approved_by_first_approver();
        $redeemOffshore = RedeemOffshoreLeave::where('user_id', $this->user->id)->first();

        $this->getPendingForApprover($this->approver2, 'second');

        $response = $this->approver_approved_or_reject_redeem_offshore(
            approver: $this->approver2,
            level: 'second',
            status: 'approved',
            redeemOffshoreLeave: $redeemOffshore,
        );

        $response->assertJson([
            'message' => 'You have fully approved this request'
        ]);

    }

    public function test_first_approve_and_second_reject()
    {
        $this->test_apply_redeem_offshore();
        $this->test_redeem_offshore_approved_by_first_approver();
        $redeemOffshore = RedeemOffshoreLeave::where('user_id', $this->user->id)->first();

        $this->getPendingForApprover($this->approver2, 'second');

        $response = $this->approver_approved_or_reject_redeem_offshore(
            approver: $this->approver2,
            level: 'second',
            status: 'rejected',
            redeemOffshoreLeave: $redeemOffshore,
        );

        $response->assertJson([
            'message' => 'You have rejected this request'
        ]);
    }

    public function test_hr_approve_redeem_offshore_and_replacement_balance_added()
    {
        $this->test_redeem_offshore_approved_by_second_approver();
        $redeemOffshore = RedeemOffshoreLeave::where('user_id', $this->user->id)->first();

        $response = $this->hr_approve_or_reject_redeem_offshore(
            redeemOffshoreLeave: $redeemOffshore,
            status: Status::approved->value,
            add_qty: 3,
        );
        $response->assertAccepted();

        //check user balance for offshore leave after hr approved
        $this->checkUserLeaveBalance(balance: 3);
    }

    public function test_hr_reject_and_balance_not_added()
    {
        $this->test_redeem_offshore_approved_by_second_approver();
        $redeemOffshore = RedeemOffshoreLeave::where('user_id', $this->user->id)->first();

        $response = $this->hr_approve_or_reject_redeem_offshore(
            redeemOffshoreLeave: $redeemOffshore,
            status: Status::rejected->value,
        );
        $response->assertAccepted();

        //check user balance does not add after hr rejected
        $this->checkUserLeaveBalance(balance: 0);
    }

    public function test_check_user_offshore_balance_when_reset()
    {
        Artisan::call('offshore:reset');

        //check user balance for offshore is 0, after reset every year
        $this->checkUserLeaveBalance();
    }

///////////////////////////////////////////////////////////////////////////////////////////////////

    private function storeRedeemOffshoreData()
    {
        return [
            'user_id' => $this->user->id,
            'start_date' => '2023-11-11',
            'end_date' => '2023-12-12',
            'first_approver_id' => $this->approver1->id,
            'first_approver_status' => Status::pending->value,
        ];
    }

    private function editData(int $redeemOffshoreID)
    {
        return [
            'id' => $redeemOffshoreID,
            'start_date' => '2023-12-01',
            'end_date' => '2023-12-31',
        ];
    }

    private function addSecondApproverToRedeemOffshoreRecord(RedeemOffshoreLeave $redeemOffshoreLeave)
    {
        $redeemOffshoreLeave->update([
            'second_approver_id' => $this->approver2->id,
            'second_approver_status' => Status::pending->value,
        ]);

        //Retrieved data after update (necessary, because update only returns boolean)
        return RedeemOffshoreLeave::find($redeemOffshoreLeave->id);
    }

    private function getPendingForApprover(User $approver, String $approverType)
    {
        $response = $this->actingAs($approver)
            ->getJson('/api/redeem-offshore-leave/approve');;

        $response->assertOk();
    }

    private function approver_approved_or_reject_redeem_offshore(User $approver, String $level, String $status, RedeemOffshoreLeave $redeemOffshoreLeave)
    {
        $data = [
            'id' => $redeemOffshoreLeave->id,
            $level.'_approver_status' => $status,
            $level.'_approver_remark' => fake()->bs(),
            $level.'_approver_date' => fake()->date(),
        ];

        return $this->actingAs($approver)
            ->postJson('/api/redeem-offshore-leave/approve', $data);
    }

    private function hr_approve_or_reject_redeem_offshore(RedeemOffshoreLeave $redeemOffshoreLeave,  String $status, int $add_qty = 0)
    {
        $hrApproveData = [
            'id' => $redeemOffshoreLeave->id,
            'hr_status' => $status,
            'hr_remark' => fake()->bs(),
            'hr_date' => fake()->date(),
            'added_qty' => $add_qty,
        ];

        return $this->actingAs($this->admin)
            ->postJson('/api/redeem-offshore-leave/summary', $hrApproveData);
    }

    private function checkUserLeaveBalance($balance = 0): void
    {
        $response = $this->actingAs($this->user)->getJson('/api/leave-balance/'.$this->user->id);
        $response->assertJsonFragment([
            'leave_type_id' => LeaveType::getOffshoreLeaveType()->id,
            'balance' => $balance,
        ]);
    }
}
