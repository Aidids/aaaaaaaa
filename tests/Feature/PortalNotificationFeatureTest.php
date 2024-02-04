<?php

namespace Tests\Feature;

use App\Enums\Status;
use App\Models\EForm;
use App\Models\LeaveRequest;
use App\Models\RedeemOffshoreLeave;
use App\Models\RedeemReplacementLeave;
use App\Models\TravelAuthorization;
use App\Models\User;
use Illuminate\Foundation\Testing\DatabaseTransactions;
use Illuminate\Support\Facades\DB;
use Tests\TestCase;

class PortalNotificationFeatureTest extends TestCase
{
    use DatabaseTransactions;

    protected function setUp(): void
    {
        parent::setUp();
    }

    ############################################
    // LEAVE REQUEST
    ############################################
    public function test_leave_request_create_notification_first_approver_only()
    {
        $lr = $this->create_leave_request(
            first_approver: true
        );

        $this->assertDatabaseHas('portal_notifications', [
            'user_id' => $lr->first_approver_id,
            'model_name' => get_class($lr),
            'model_id' => $lr->id,
            'status' => 'pending',
        ]);
    }

    public function test_leave_request_create_notification_second_approver_only()
    {
        $lr = $this->create_leave_request(
            second_approver: true,
        );

        $this->assertDatabaseHas('portal_notifications', [
            'user_id' => $lr->second_approver_id,
            'model_name' => get_class($lr),
            'model_id' => $lr->id,
            'status' => 'pending',
        ]);
    }

    public function test_leave_request_create_notification()
    {
        $lr = $this->create_leave_request(
            first_approver: true,
            second_approver: true,
        );

        $this->assertDatabaseHas('portal_notifications', [
            'user_id' => $lr->first_approver_id,
            'model_name' => get_class($lr),
            'model_id' => $lr->id,
            'status' => 'pending',
        ]);

        $this->assertDatabaseHas('portal_notifications', [
            'user_id' => $lr->second_approver_id,
            'model_name' => get_class($lr),
            'model_id' => $lr->id,
            'status' => 'pending',
        ]);
    }

    public function test_approve_leave_request_update_notification()
    {
        $lr = $this->create_leave_request(
            first_approver: true
        );

        $data = [
            'id' => $lr->id,
            'first_approver_id' => $lr->first_approver_id,
            'first_approver_status' => 'approved',
            'first_approver_remark' => 'Portal Notification Feature Test',
            'first_approver_date' => '2023-10-01',
        ];

        $response = $this->actingAs($lr->firstApprover)->postJson('/api/leave-request/'.$lr->first_approver_id.'/approve', $data);
        $response->assertJson([
            'message'=> 'Leave request status has been successfully approved'
        ]);

        $this->assertDatabaseHas('portal_notifications', [
            'user_id' => $lr->user_id,
            'model_name' => get_class($lr),
            'model_id' => $lr->id,
            'status' => 'approved',
        ]);
    }

    public function test_reject_leave_request_update_notification()
    {
        $lr = $this->create_leave_request(
            first_approver: true,
            second_approver: true,
        );

        $data = [
            'id' => $lr->id,
            'first_approver_id' => $lr->first_approver_id,
            'first_approver_status' => 'rejected',
            'first_approver_remark' => 'Portal Notification Feature Test',
            'first_approver_date' => '2023-10-01',
        ];

        $response = $this->actingAs($lr->firstApprover)->postJson('/api/leave-request/'.$lr->first_approver_id.'/approve', $data);
        $response->assertJson([
            'message'=> 'You have rejected this leave request'
        ]);

        $this->assertCount(3, DB::table('portal_notifications')
            ->whereIn('user_id',
                [$lr->user_id, $lr->first_approver_id, $lr->second_approver_id])
            ->where([
                'model_name' => get_class($lr),
                'model_id' => $lr->id,
                'status' => 'rejected',
            ])
            ->get()
        );
    }

    private function create_leave_request(bool $first_approver = false, bool $second_approver = false): LeaveRequest
    {
        $first_approver_id = ($first_approver) ?  User::factory()->create()->id : NULL;
        $second_approver_id = ($second_approver) ?  User::factory()->create()->id : NULL;

        return LeaveRequest::factory()->create([
            'first_approver_id' => $first_approver_id,
            'second_approver_id' => $second_approver_id,
        ]);
    }


    ############################################
    // Redeem Leave Feature Test
    ############################################

    // REDEEM REPLACEMENT LEAVE
    public function test_redeem_replacement_create_notification_first_approver_only()
    {
        $user = $this->setUserLeave();

        $redeemReplacement = $this->create_redeem_replacement_leave(
            user: $user,
            first_approver: true,
        );

        $this->assertDatabaseHas('portal_notifications', [
            'user_id' => $redeemReplacement->first_approver_id,
            'model_name' => get_class($redeemReplacement),
            'model_id' => $redeemReplacement->id,
            'status' => 'pending',
        ]);
    }

    public function test_redeem_replacement_create_notification_second_approver_only()
    {
        $user = $this->setUserLeave();

        $redeemReplacement = $this->create_redeem_replacement_leave(
            user: $user,
            second_approver: true,
        );

        $this->assertDatabaseHas('portal_notifications', [
            'user_id' => $redeemReplacement->second_approver_id,
            'model_name' => get_class($redeemReplacement),
            'model_id' => $redeemReplacement->id,
            'status' => 'pending',
        ]);
    }

    public function test_approve_redeem_replacement_update_notification()
    {
        $user = $this->setUserLeave();

        //Redeem Replacement Leave Observer hardcode hr_id
        User::factory()->create(['id' => 171]);
        User::factory()->create(['id' => 172]);
        User::factory()->create(['id' => 24]);

        $redeemReplacement = $this->create_redeem_replacement_leave(
            user: $user,
            first_approver: true,
        );

        $response = $this->approver_approved_or_reject_redeem_replacement(
            redeemReplacementID: $redeemReplacement->id,
            approver: $redeemReplacement->firstApprover,
            approverLevel: 'first',
            status: 'approved',
        );

        $response->assertJson([
            'message' => 'You have fully approved this request',
        ]);

        $this->assertDatabaseHas('portal_notifications', [
            'user_id' => $redeemReplacement->first_approver_id,
            'model_name' => get_class($redeemReplacement),
            'model_id' => $redeemReplacement->id,
            'status' => Status::approved->value,
        ]);
    }

    public function test_reject_redeem_replacement_update_notification()
    {
        $user = $this->setUserLeave();

        //Redeem Replacement Leave Observer hardcode hr_id
        User::factory()->create(['id' => 171]);
        User::factory()->create(['id' => 172]);
        User::factory()->create(['id' => 24]);

        $redeemReplacement = $this->create_redeem_replacement_leave(
            user: $user,
            first_approver: true,
        );

        $response = $this->approver_approved_or_reject_redeem_replacement(
            redeemReplacementID: $redeemReplacement->id,
            approver: $redeemReplacement->firstApprover,
            approverLevel: 'first',
            status: 'rejected',
        );

        $response->assertJson([
            'message' => 'You have rejected this request',
        ]);

        $this->assertDatabaseHas('portal_notifications', [
            'user_id' => $redeemReplacement->first_approver_id,
            'model_name' => get_class($redeemReplacement),
            'model_id' => $redeemReplacement->id,
            'status' => Status::rejected->value,
        ]);
    }

    public function test_hr_approve_redeem_replacement_update_notification()
    {
        $user = $this->setUserLeave();

        //Redeem Replacement Leave Observer hardcode hr_id
        User::factory()->create(['id' => 171]);
        User::factory()->create(['id' => 172]);
        $admin = User::factory()->create(['id' => 24]);

        $redeemReplacement = $this->create_redeem_replacement_leave(
            user: $user,
            first_approver: true,
        );

        $response = $this->approver_approved_or_reject_redeem_replacement(
            redeemReplacementID: $redeemReplacement->id,
            approver: $redeemReplacement->firstApprover,
            approverLevel: 'first',
            status: 'approved',
        );

        $response->assertJson([
            'message' => 'You have fully approved this request',
        ]);

        //check approver notification
        $this->assertDatabaseHas('portal_notifications', [
            'user_id' => $redeemReplacement->first_approver_id,
            'model_name' => get_class($redeemReplacement),
            'model_id' => $redeemReplacement->id,
            'status' => Status::approved->value,
        ]);

        //check user notification
        $this->assertDatabaseHas('portal_notifications', [
            'user_id' => $redeemReplacement->user_id,
            'model_name' => get_class($redeemReplacement),
            'model_id' => $redeemReplacement->id,
            'status' => Status::hr_pending->value,
        ]);

        $hrApproveData = [
            'id' => $redeemReplacement->id,
            'hr_id' => $admin->id,
            'hr_status' => 'approved',
            'hr_remark' => fake()->bs(),
            'hr_date' => fake()->date('Y-m-d'),
            'added_qty' => 1,
        ];

        $response = $this->actingAs($admin)
            ->postJson('/api/redeem-replacement-leave/summary', $hrApproveData);

        $response->assertJson([
            'message' => 'You have fully approve this request',
        ]);

        //check HR notification
        $this->assertDatabaseHas('portal_notifications', [
            'user_id' => $admin->id,
            'model_name' => get_class($redeemReplacement),
            'model_id' => $redeemReplacement->id,
            'status' => Status::hr_approved->value,
        ]);

        //check user notification
        $this->assertDatabaseHas('portal_notifications', [
            'user_id' => $redeemReplacement->user_id,
            'model_name' => get_class($redeemReplacement),
            'model_id' => $redeemReplacement->id,
            'status' => Status::hr_approved->value,
        ]);
    }


    // REDEEM OFFSHORE LEAVE
    public function test_redeem_offshore_create_notification_first_approver_only()
    {
        $user = $this->setUserLeave();

        $redeemOffshore = $this->create_redeem_offshore_leave(
          user: $user,
          first_approver: true,
        );

        $this->assertDatabaseHas('portal_notifications', [
            'user_id' => $redeemOffshore->first_approver_id,
            'model_name' => get_class($redeemOffshore),
            'model_id' => $redeemOffshore->id,
            'status' => Status::pending->value,
        ]);
    }

    public function test_redeem_offshore_create_notification_second_approver_only()
    {
        $user = $this->setUserLeave();

        $redeemOffshore = $this->create_redeem_offshore_leave(
            user: $user,
            second_approver: true,
        );

        $this->assertDatabaseHas('portal_notifications', [
            'user_id' => $redeemOffshore->second_approver_id,
            'model_name' => get_class($redeemOffshore),
            'model_id' => $redeemOffshore->id,
            'status' => Status::pending->value,
        ]);
    }

    public function test_approve_redeem_offshore_update_notification()
    {
        $user = $this->setUserLeave();

        //Redeem Replacement Leave Observer hardcode hr_id
        User::factory()->create(['id' => 172]);
        User::factory()->create(['id' => 24]);

        $redeemOffshore = $this->create_redeem_offshore_leave(
            user: $user, first_approver: true,
        );

        $response = $this->approver_approved_or_reject_redeem_offshore(
            redeemOffshoreID: $redeemOffshore->id,
            approver: $redeemOffshore->firstApprover,
            approverLevel: 'first',
            status: 'approved',
        );

        $response->assertJson([
            'message' => 'You have fully approved this request',
        ], 202);

        $this->assertDatabaseHas('portal_notifications', [
            'user_id' => $redeemOffshore->first_approver_id,
            'model_name' => get_class($redeemOffshore),
            'model_id' => $redeemOffshore->id,
            'status' => Status::approved->value,
        ]);
    }

    public function test_reject_redeem_offshore_update_notification()
    {
        $user = $this->setUserLeave();

        //Redeem Replacement Leave Observer hardcode hr_id
        User::factory()->create(['id' => 172]);
        User::factory()->create(['id' => 24]);

        $redeemOffshore = $this->create_redeem_offshore_leave(
            user: $user, first_approver: true,
        );

        $response = $this->approver_approved_or_reject_redeem_offshore(
            redeemOffshoreID: $redeemOffshore->id,
            approver: $redeemOffshore->firstApprover,
            approverLevel: 'first',
            status: 'rejected',
        );

        $response->assertJson([
            'message' => 'You have rejected this request',
        ], 202);

        $this->assertDatabaseHas('portal_notifications', [
            'user_id' => $redeemOffshore->first_approver_id,
            'model_name' => get_class($redeemOffshore),
            'model_id' => $redeemOffshore->id,
            'status' => Status::rejected->value,
        ]);
    }

    private function setUserLeave()
    {
        $user = User::factory()->create();

        $this->artisan('replacement:create');

        return $user;
    }

    private function create_redeem_replacement_leave(User $user, bool $first_approver = false, bool $second_approver = false): RedeemReplacementLeave
    {
        $first_approver_id = ($first_approver) ?  User::factory()->create()->id : NULL;
        $second_approver_id = ($second_approver) ?  User::factory()->create()->id : NULL;

        $data = [
            'user_id' => $user->id,
            'first_approver_id' => $first_approver_id,
            'second_approver_id' => $second_approver_id,
            'first_approver_status' => $first_approver ? 'pending' : NULL,
            'second_approver_status' => $second_approver ? 'pending' : NULL,
        ];

        return RedeemReplacementLeave::factory()->create($data);
    }

    private function create_redeem_offshore_leave(User $user, $first_approver = false, $second_approver = false)
    {
        $first_approver_id = ($first_approver) ?  User::factory()->create()->id : NULL;
        $second_approver_id = ($second_approver) ?  User::factory()->create()->id : NULL;

        $data = [
            'user_id' => $user->id,
            'first_approver_id' => $first_approver_id,
            'second_approver_id' => $second_approver_id,
            'first_approver_status' => $first_approver ? 'pending' : NULL,
            'second_approver_status' => $second_approver ? 'pending' : NULL,
        ];

        return RedeemOffshoreLeave::factory()->create($data);
    }

    private function approver_approved_or_reject_redeem_replacement(int $redeemReplacementID, User $approver, String $approverLevel, String $status)
    {
        $data = [
            'id' => $redeemReplacementID,
            $approverLevel.'_approver_status' => $status,
            $approverLevel.'_approver_remark' => fake()->bs(),
            $approverLevel.'_approver_date' =>  fake()->date('Y-m-d'),
        ];

        return $this->actingAs($approver)
            ->postJson('/api/redeem-replacement-leave/approve', $data);
    }

    private function approver_approved_or_reject_redeem_offshore(int $redeemOffshoreID, User $approver, String $approverLevel, String $status)
    {
        $data = [
            'id' => $redeemOffshoreID,
            $approverLevel.'_approver_status' => $status,
            $approverLevel.'_approver_remark' => fake()->bs(),
            $approverLevel.'_approver_date' =>  fake()->date('Y-m-d'),
        ];

        return $this->actingAs($approver)
            ->postJson('/api/redeem-offshore-leave/approve', $data);
    }


    ############################################
    // E-FORM (TRAVEL AUTHORIZATION)
    ############################################

    public function test_eform_create_notification_first_approver_only()
    {
        $eform = $this->create_eform(
            first_approver: true,
        );

        $this->assertDatabaseHas('portal_notifications', [
            'user_id' => $eform->first_approver_id,
            'model_name' => $eform->eformable_type,
            'model_id' => $eform->eformable_id,
            'status' => 'pending',
        ]);
    }

    public function test_eform_create_notification_second_approver_only()
    {
        $eform = $this->create_eform(
            second_approver: true,
        );

        $this->assertDatabaseHas('portal_notifications', [
            'user_id' => $eform->second_approver_id,
            'model_name' => $eform->eformable_type,
            'model_id' => $eform->eformable_id,
            'status' => 'pending',
        ]);
    }

    public function test_cancel_eform_update_notification()
    {
        $eform = $this->create_eform(
            first_approver: true,
        );
        $travel = TravelAuthorization::where('id', $eform->eformable_id)->first();

        $response = $this->actingAs($eform->user)
            ->postJson('/api/travel-authorization/' . $travel->id . '/cancel');
        $response->assertJson(['message' => 'Cancel request successful']);

        $this->assertDatabaseHas('portal_notifications', [
            'user_id' => $eform->first_approver_id,
            'model_name' => $eform->eformable_type,
            'model_id' => $eform->eformable_id,
            'status' => 'canceled',
        ]);
    }

    public function test_approve_eform_update_notification()
    {
        $eform = $this->create_eform(
            first_approver: true,
            second_approver: true,
        );

        // after user apply, only first_approver get notification
        $this->assertDatabaseHas('portal_notifications', [
            'user_id' => $eform->first_approver_id,
            'model_name' => $eform->eformable_type,
            'model_id' => $eform->eformable_id,
            'status' => 'pending',
        ]);

        // after first approver has approved, 2nd approver will get notification
        $data = [
            'first_approver_status' => Status::approved->value,
            'first_approver_remark' => fake()->bs(),
            'first_approver_date' => fake()->date('Y-m-d'),
        ];
        $travel = TravelAuthorization::where('id', $eform->eformable_id)->first();

        $travel->update(['travel_purpose' => 0]);

        $response = $this->actingAs($eform->firstApprover)
            ->postJson('/api/travel-authorization/' . $travel->id . '/review', $data);
        $response->assertJson(['message' => 'You have approved this request']);

        // 2nd approver get notification
        $this->assertDatabaseHas('portal_notifications', [
            'user_id' => $eform->second_approver_id,
            'model_name' => $eform->eformable_type,
            'model_id' => $eform->eformable_id,
            'status' => 'pending',
        ]);

        // created admin id here, because user factory id increment skip after new id created
        $admin = User::factory()->create(['id' => (int)env('TA_PIC_ID')]); //->EFORM OBSERVER HR ID HARD CODE

        //->2nd approver, approve travel
        $data = [
            'second_approver_status' => Status::approved->value,
            'second_approver_remark' => fake()->bs(),
            'second_approver_date' => fake()->date('Y-m-d'),
        ];
        $travel = TravelAuthorization::where('id', $eform->eformable_id)->first();

        $response = $this->actingAs($eform->secondApprover)
            ->postJson('/api/travel-authorization/' . $travel->id . '/review', $data);
        $response->assertJson(['message' => 'You have fully approved this request']);

        // requester and HR get notification
        $this->assertDatabaseHas('portal_notifications', [
            'user_id' => $eform->user_id,
            'model_name' => $eform->eformable_type,
            'model_id' => $eform->eformable_id,
            'status' => 'hr_pending',
        ]);
        $this->assertDatabaseHas('portal_notifications', [
            'user_id' => $admin->id,
            'model_name' => $eform->eformable_type,
            'model_id' => $eform->eformable_id,
            'status' => 'hr_processing',
        ]);
    }

    private function create_eform(bool $first_approver = false, bool $second_approver = false): EForm
    {
        $first_approver_id = ($first_approver) ?  User::factory()->create()->id : NULL;
        $second_approver_id = ($second_approver) ?  User::factory()->create()->id : NULL;

        $travel = TravelAuthorization::factory()->create();

        $data = [
            'user_id' => User::factory()->create()->id,
            'first_approver_id' => $first_approver_id,
            'first_approver_status' => ($first_approver_id) ? 'pending' : NULL,
            'second_approver_id' => $second_approver_id,
            'second_approver_status' => ($second_approver_id) ? 'pending' : NULL,
            'overall_status' => 'pending',
        ];

        return EForm::createEForm($data, $travel);
    }
}
