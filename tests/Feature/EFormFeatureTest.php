<?php

namespace Tests\Feature;

use App\Enums\LeaveRequestStatus;
use App\Http\Resources\UserResource;
use Carbon\Carbon;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\ArrangeData;
use Tests\TestCase;

class EFormFeatureTest extends TestCase
{
    use ArrangeData, RefreshDatabase;

    public function test_new_user_browsing_index_screen()
    {
        foreach ($this->EFORM as $eform) {
            $eform_name = $this->convertName($eform);

            $response = $this->actingAs($this->user)->getJson('api/' . $eform_name);
            $response->assertStatus(200)->assertJson([
                'data' => []
            ]);
        }
    }

    public function test_user_cannot_see_others_eform_history()
    {
        foreach ($this->EFORM as $eform) {
            $this->createEform(
                user: $this->randomUser,
                model: $eform,
            );

            $this->test_new_user_browsing_index_screen();
        }
    }

    public function test_user_apply_eform()
    {
        foreach ($this->EFORM as $eform) {
            $eform_name = $this->convertName($eform);

            $response = $this->actingAs($this->user)->postJson('api/'. $eform_name .'/apply', $this->data($eform));

            $response->assertStatus(200)->assertJsonFragment($response->json('data'));
        }
    }

    public function test_user_edit_eform()
    {
        foreach ($this->EFORM as $eform) {
            $eform_name = $this->convertName($eform);

            $response = $this->actingAs($this->user)->postJson('api/'. $eform_name .'/apply', $this->data($eform));
            $response->assertStatus(200)->assertJsonFragment($response->json('data'));


            $response = $this->actingAs($this->user)
                ->postJson('api/'. $eform_name . '/' . $response->json('data')['id'] .'/edit', $this->updateData($eform));
            $response->assertStatus(201)->assertJsonFragment(['message' => 'Travel form edit success']);
        }
    }

    public function test_user_cancel_eform()
    {
        foreach ($this->EFORM as $eform) {
            $eform_name = $this->convertName($eform);

            $applyEform = $this->applyEformApi($eform_name, $this->data($eform));

            $response = $this->actingAs($this->user)
                ->postJson('api/'. $eform_name . '/' . $applyEform->json('data')['id'] .'/cancel');
            $response->assertStatus(200)->assertJsonFragment(['message' => 'Cancel request successful']);
            //approver1 will see empty page in index screen
            $approver1 = $this->actingAs($this->approver1)->getJson('api/'. $eform_name .'/approver');
            $approver1->assertStatus(200)->assertJson([
                'data' => []
            ]);
            //approver2 will see empty page in index screen
            $approver2 = $this->actingAs($this->approver2)->getJson('api/'. $eform_name .'/approver');
            $approver2->assertStatus(200)->assertJson([
                'data' => []
            ]);
        }
    }

    public function test_eform_approved_assigned_with_two_approver()
    {
        foreach ($this->EFORM as $eform) {
            $eform_name = $this->convertName($eform);
            //apply eform and approver check if receive data
            $applyEform = $this->applyEformApi($eform_name, $this->data($eform));
            //approver1 approve eform
            $approver1 = $this->actingAs($this->approver1)
                ->postJson('api/'. $eform_name .'/' . $applyEform->json('data')['id'] . '/review',
                [
                    'first_approver_status' => LeaveRequestStatus::approved->value,
                    'first_approver_remark' => 'Remark from feature test',
                    'first_approver_date' => Carbon::now()->toDateString(),
                ]);
            $approver1->assertStatus(200)->assertJsonFragment(['message' => 'You have approved this request']);
            //approver1 will see empty page in index screen
            $approver1 = $this->actingAs($this->approver1)->getJson('api/'. $eform_name .'/approver');
            $approver1->assertStatus(200)->assertJson([
                'data' => []
            ]);
            //user check eform and has been approved by first approver
            $eformFirstApproved = $this->actingAs($this->user)
                ->getJson('api/'. $eform_name .'/' . $applyEform->json('data')['id']);
            $approver2 = $this->actingAs($this->approver2)->getJson('api/'. $eform_name .'/approver');
            $approver2->assertStatus(200)->assertJsonFragment([
                $eformFirstApproved->json('data')
            ]);
            //approver2 approve eform
            $approver2 = $this->actingAs($this->approver2)
                ->postJson('api/'. $eform_name .'/' . $eformFirstApproved->json('data')['id'] . '/review',
                    [
                        'second_approver_status' => LeaveRequestStatus::approved->value,
                        'second_approver_remark' => 'Remark from feature test',
                        'second_approver_date' => Carbon::now()->toDateString(),
                    ]);
            $approver2->assertStatus(200)->assertJsonFragment(['message' => 'You have fully approved this request']);
            //approver2 will see empty page in index screen
            $approver2 = $this->actingAs($this->approver2)->getJson('api/'. $eform_name .'/approver');
            $approver2->assertStatus(200)->assertJson([
                'data' => []
            ]);
            //user check eform and has been approved by both approver
            $eformApproved = $this->actingAs($this->user)
                ->getJson('api/'. $eform_name .'/' . $applyEform->json('data')['id']);
            //admin see approved e-form
            $admin = $this->actingAs($this->admin)->getJson('api/'. $eform_name .'/summary');
            $admin->assertStatus(200)->assertJsonFragment([
                $eformApproved->json('data')
            ]);
        }
    }

    public function test_eform_approved_assigned_only_one_approver()
    {
        foreach ($this->EFORM as $eform) {
            $eform_name = $this->convertName($eform);

            //assign hod
            $userApplyForm = $this->actingAs($this->user)->postJson('api/'. $eform_name .'/apply', $this->data($eform, 2));
            $approver2Response = $this->actingAs($this->approver2)->getJson('api/'. $eform_name .'/approver');
            $approver2Response->assertStatus(200)->assertJsonFragment([
                $userApplyForm->json('data')
            ]);

            //approver2 approve eform
            $approver2 = $this->actingAs($this->approver2)
                ->postJson('api/'. $eform_name .'/' . $userApplyForm->json('data')['id'] . '/review',
                    [
                        'second_approver_status' => LeaveRequestStatus::approved->value,
                        'second_approver_remark' => 'Remark from feature test',
                        'second_approver_date' => Carbon::now()->toDateString(),
                    ]);
            $approver2->assertStatus(200)->assertJsonFragment(['message' => 'You have fully approved this request']);

            //user check eform and has been approved by both approver
            $eformApproved = $this->actingAs($this->user)
                ->getJson('api/'. $eform_name .'/' . $userApplyForm->json('data')['id']);

            //admin see approved e-form
            $admin = $this->actingAs($this->admin)->getJson('api/'. $eform_name .'/summary');
            $admin->assertStatus(200)->assertJsonFragment([
                $eformApproved->json('data')
            ]);

            //assign supervisor
            $userApplyForm = $this->actingAs($this->user)->postJson('api/'. $eform_name .'/apply', $this->data($eform, 1));
            $approver1Response = $this->actingAs($this->approver1)->getJson('api/'. $eform_name .'/approver');
            $approver1Response->assertStatus(200)->assertJsonFragment([
                $userApplyForm->json('data')
            ]);

            //approver1 approve eform
            $approver1 = $this->actingAs($this->approver1)
                ->postJson('api/'. $eform_name .'/' . $userApplyForm->json('data')['id'] . '/review',
                    [
                        'first_approver_status' => LeaveRequestStatus::approved->value,
                        'first_approver_remark' => 'Remark from feature test',
                        'first_approver_date' => Carbon::now()->toDateString(),
                    ]);
            $approver1->assertStatus(200)->assertJsonFragment(['message' => 'You have fully approved this request']);

            //user check eform and has been approved by both approver
            $eformApproved = $this->actingAs($this->user)
                ->getJson('api/'. $eform_name .'/' . $userApplyForm->json('data')['id']);

            //admin see approved e-form
            $admin = $this->actingAs($this->admin)->getJson('api/'. $eform_name .'/summary');
            $admin->assertStatus(200)->assertJsonFragment([
                $eformApproved->json('data')
            ]);
        }
    }

    public function test_rejecting_eform()
    {
        foreach ($this->EFORM as $eform) {
            $eform_name = $this->convertName($eform);

            /**
             * Test assign HOD only
             */
            $userApplyForm = $this->actingAs($this->user)->postJson('api/'. $eform_name .'/apply', $this->data($eform, 2));
            $approver2Response = $this->actingAs($this->approver2)->getJson('api/'. $eform_name .'/approver');
            $approver2Response->assertStatus(200)->assertJsonFragment([
                $userApplyForm->json('data')
            ]);

            //approver2 reject eform
            $approver2 = $this->actingAs($this->approver2)
                ->postJson('api/'. $eform_name .'/' . $userApplyForm->json('data')['id'] . '/review',
                    [
                        'second_approver_status' => LeaveRequestStatus::rejected->value,
                        'second_approver_remark' => 'Remark from feature test',
                        'second_approver_date' => Carbon::now()->toDateString(),
                    ]);
            $approver2->assertStatus(200)->assertJsonFragment(['message' => 'You have rejected this request']);

            //user check eform and has been approved by both approver
            $eformApproved = $this->actingAs($this->user)
                ->getJson('api/'. $eform_name .'/' . $userApplyForm->json('data')['id']);

            //admin see approved e-form
            $admin = $this->actingAs($this->admin)->getJson('api/'. $eform_name .'/summary');
            $admin->assertStatus(200)->assertJsonMissing([
                $eformApproved->json('data')
            ]);

            /**
             * Test assign supervisor only
             */
            $userApplyForm = $this->actingAs($this->user)->postJson('api/'. $eform_name .'/apply', $this->data($eform, 1));
            $approver1Response = $this->actingAs($this->approver1)->getJson('api/'. $eform_name .'/approver');
            $approver1Response->assertStatus(200)->assertJsonFragment([
                $userApplyForm->json('data')
            ]);

            //approver1 reject eform
            $approver1 = $this->actingAs($this->approver1)
                ->postJson('api/'. $eform_name .'/' . $userApplyForm->json('data')['id'] . '/review',
                    [
                        'first_approver_status' => LeaveRequestStatus::rejected->value,
                        'first_approver_remark' => 'Remark from feature test',
                        'first_approver_date' => Carbon::now()->toDateString(),
                    ]);
            $approver1->assertStatus(200)->assertJsonFragment(['message' => 'You have rejected this request']);

            //user check eform and has been approved by both approver
            $eformApproved = $this->actingAs($this->user)
                ->getJson('api/'. $eform_name .'/' . $userApplyForm->json('data')['id']);

            //admin see approved e-form
            $admin = $this->actingAs($this->admin)->getJson('api/'. $eform_name .'/summary');
            $admin->assertStatus(200)->assertJsonMissing([
                $eformApproved->json('data')
            ]);

            /**
             * Test assign two approvers
             */
            $userApplyForm = $this->applyEformApi($eform_name, $this->data($eform));

            $approver1Response = $this->actingAs($this->approver1)->getJson('api/'. $eform_name .'/approver');
            $approver1Response->assertStatus(200)->assertJsonFragment([
                $userApplyForm->json('data')
            ]);

            //approver1 approve eform
            $approver1 = $this->actingAs($this->approver1)
                ->postJson('api/'. $eform_name .'/' . $userApplyForm->json('data')['id'] . '/review',
                    [
                        'first_approver_status' => LeaveRequestStatus::approved->value,
                        'first_approver_remark' => 'Remark from feature test',
                        'first_approver_date' => Carbon::now()->toDateString(),
                    ]);
            $approver1->assertStatus(200)->assertJsonFragment(['message' => 'You have approved this request']);

            //user check eform and has been approved by both approver
            $eformApproved = $this->actingAs($this->user)
                ->getJson('api/'. $eform_name .'/' . $userApplyForm->json('data')['id']);

            $approver2Response = $this->actingAs($this->approver2)->getJson('api/'. $eform_name .'/approver');
            $approver2Response->assertStatus(200)->assertJsonFragment([
                $eformApproved->json('data')
            ]);

            //approver2 reject eform
            $approver2 = $this->actingAs($this->approver2)
                ->postJson('api/'. $eform_name .'/' . $eformApproved->json('data')['id'] . '/review',
                    [
                        'second_approver_status' => LeaveRequestStatus::rejected->value,
                        'second_approver_remark' => 'Remark from feature test',
                        'second_approver_date' => Carbon::now()->toDateString(),
                    ]);
            $approver2->assertStatus(200)->assertJsonFragment(['message' => 'You have rejected this request']);

            //user check eform and has been approved by both approver
            $eformApproved = $this->actingAs($this->user)
                ->getJson('api/'. $eform_name .'/' . $userApplyForm->json('data')['id']);

            //admin see approved e-form
            $admin = $this->actingAs($this->admin)->getJson('api/'. $eform_name .'/summary');
            $admin->assertStatus(200)->assertJsonMissing([
                $eformApproved->json('data')
            ]);
        }
    }
}
