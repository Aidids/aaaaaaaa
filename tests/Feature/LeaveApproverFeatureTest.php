<?php

namespace Tests\Feature;

use App\Models\Approver;
use App\Models\LeaveRequest;
use App\Models\User;
use Carbon\Carbon;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Tests\TestCase;

class LeaveApproverFeatureTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        LeaveRequest::unsetEventDispatcher();
        parent::setUp();
        $this->user = $this->createUser();
        $this->admin = $this->createUser(is_admin: true);
    }

    public function test_approver_get_leave_request()
    {
        LeaveRequest::factory()->create([
            'first_approver_id' => $this->admin->id
        ]);

        $response = $this->actingAs($this->admin)->getJson('/api/leave-request/'.$this->admin->id.'/approve');
        $response->assertOk();
    }

    public function test_user_get_all_leave_request()
    {
        $leaveRequest = LeaveRequest::factory(50)->create([
            'user_id' => $this->admin->id,
            'first_approver_id' => User::factory()->create()->id,
        ]);

        $response = $this->actingAs($this->admin)->getJson('/api/leave-request/'.$this->admin->id.'/all');
        $response->assertOk();
        $response->assertJson([
            'data' => array([
                'start_date' => $leaveRequest[0]->start_date,
                'end_date' => $leaveRequest[0]->end_date,
                'duration' => $leaveRequest[0]->duration,
                'reason' => $leaveRequest[0]->reason,
            ])
        ]);
    }

    public function test_approver_only_get_leave_request_assigned_to_them()
    {
        $approver = User::factory()->create([
            'approver_id' => Approver::factory()->create()->id,
            'joining_date' => '2020-01-01',
        ]);

        $leaveRequest = LeaveRequest::factory(50)->create([
            'first_approver_id' => $approver->id
        ]);

        $response = $this->actingAs($approver)->getJson('/api/leave-request/'.$approver->id.'/approve');
        $response->assertOk();
        $response->assertJson([
            'data' => array([
                'start_date' => $leaveRequest[0]->start_date,
                'end_date' => $leaveRequest[0]->end_date,
                'duration' => $leaveRequest[0]->duration,
                'overall_status' => $leaveRequest[0]->overall_status
            ])
        ]);
    }

    public function test_approver_reject_leave()
    {
        $this->testApproverStatus(approverType: 'first_approver',
            status: 'rejected',
            message: 'You have rejected this leave request',
            overallStatus: 'rejected'
        );
        $this->testApproverStatus(approverType: 'second_approver',
            status: 'rejected',
            message: 'You have rejected this leave request',
            overallStatus: 'rejected'
        );
    }

    public function test_only_first_approver_assign_and_approve_leave()
    {
        $this->testApproverStatus(approverType: 'first_approver',
            status: 'approved',
            message: 'Leave request status has been successfully approved',
            overallStatus: 'approved'
        );
    }

    public function test_only_second_approver_assign_and_approve_leave()
    {
        $this->testApproverStatus(approverType: 'second_approver',
            status: 'approved',
            message: 'Leave request status has been successfully approved',
            overallStatus: 'approved'
        );
    }

    public function test_approve_leave_from_first_to_second_approver()
    {
        {
            $approver1 = User::factory()->create([
                'approver_id' => Approver::factory()->create()->id,
                'joining_date' => '2020-01-01',
            ]);

            $approver2 = User::factory()->create([
                'approver_id' => Approver::factory()->create()->id,
                'joining_date' => '2020-01-01',
            ]);

            $leaveRequest = LeaveRequest::factory()->create([
                'first_approver_id' => $approver1->id,
                'first_approver_status' => 'pending',
                'second_approver_id' => $approver2->id,
                'second_approver_status' => 'pending',
            ]);

            $data1 = ['id' => $leaveRequest->id,
                'first_approver_id' => $approver1->id,
                'first_approver_date' => '2020-01-01',
                'first_approver_status' => 'approved',
                'first_approver_remark' => null
            ];

            $data2 = ['id' => $leaveRequest->id,
                'second_approver_id' => $approver2->id,
                'second_approver_date' => '2020-01-01',
                'second_approver_status' => 'approved',
                'second_approver_remark' => null
            ];

            $response = $this->actingAs($approver1)->postJson('/api/leave-request/'.$approver1->id.'/approve', $data1);
            $response->assertJson(['message'=> 'You have approve this leave request']);

            $response = $this->actingAs($approver1)->getJson('/api/leave-request/'.$approver1->id.'/approve');
            $response->assertOk();
            $response->assertJson([
                'data' => array([
                    'overall_status' => 'pending'
                ])
            ]);

            $response = $this->actingAs($approver2)->postJson('/api/leave-request/'.$approver2->id.'/approve', $data2);
            $response->assertJson(['message'=> 'Leave request status has been successfully approved']);
        }
    }

    public function test_approve_leave_from_second_to_first_approver()
    {
        {
            $approver1 = User::factory()->create([
                'approver_id' => Approver::factory()->create()->id,
                'joining_date' => '2020-01-01',
            ]);

            $approver2 = User::factory()->create([
                'approver_id' => Approver::factory()->create()->id,
                'joining_date' => '2020-01-01',
            ]);

            $leaveRequest = LeaveRequest::factory()->create([
                'first_approver_id' => $approver1->id,
                'first_approver_status' => 'pending',
                'second_approver_id' => $approver2->id,
                'second_approver_status' => 'pending',
            ]);

            $data1 = ['id' => $leaveRequest->id,
                'first_approver_id' => $approver1->id,
                'first_approver_date' => '2020-01-01',
                'first_approver_status' => 'approved',
                'first_approver_remark' => null
            ];

            $data2 = ['id' => $leaveRequest->id,
                'second_approver_id' => $approver2->id,
                'second_approver_date' => '2020-01-01',
                'second_approver_status' => 'approved',
                'second_approver_remark' => null
            ];

            $response = $this->actingAs($approver2)->postJson('/api/leave-request/'.$approver2->id.'/approve', $data2);
            $response->assertJson(['message'=> 'You have approve this leave request']);

            $response = $this->actingAs($approver2)->getJson('/api/leave-request/'.$approver2->id.'/approve');
            $response->assertOk();
            $response->assertJson([
                'data' => array([
                    'overall_status' => 'pending'
                ])
            ]);

            $response = $this->actingAs($approver1)->postJson('/api/leave-request/'.$approver1->id.'/approve', $data1);
            $response->assertJson(['message'=> 'Leave request status has been successfully approved']);
        }
    }

    public function test_first_approver_reject_and_second_approver_try_to_approve_and_fail()
    {
        $approver1 = User::factory()->create([
            'approver_id' => Approver::factory()->create()->id,
            'joining_date' => '2020-01-01',
        ]);

        $approver2 = User::factory()->create([
            'approver_id' => Approver::factory()->create()->id,
            'joining_date' => '2020-01-01',
        ]);

        $leaveRequest = LeaveRequest::factory(1)->create([
            'first_approver_id' => $approver1->id,
            'second_approver_id' => $approver2->id
        ]);

        $data1 = ['id' => $leaveRequest[0]->id,
            'status' => 'rejected',
            'first_approver_id' => $approver1->id,
            'first_approver_date' => '2020-01-01',
            'first_approver_status' => 'rejected',
            'first_approver_remark' => null
        ];

        $data2 = ['id' => $leaveRequest[0]->id,
            'status' => 'approved',
            'second_approver_id' => $approver2->id,
            'second_approver_date' => '2020-01-01',
            'second_approver_status' => 'approved',
            'second_approver_remark' => null
        ];

        $response = $this->actingAs($approver1)->postJson('/api/leave-request/'.$approver1->id.'/approve', $data1);
        $response->assertJson(['message'=> 'You have rejected this leave request']);

        $response = $this->actingAs($approver1)->getJson('/api/leave-request/'.$approver1->id.'/approve');
        $response->assertOk();

        $response = $this->actingAs($approver2)->postJson('/api/leave-request/'.$approver2->id.'/approve', $data2);
        $response->assertJson(['message'=> 'This leave has been rejected by another approver']);

    }

    public function test_second_approver_reject_and_first_approver_try_to_approve_and_fail()
    {
        $approver1 = User::factory()->create([
            'approver_id' => Approver::factory()->create()->id,
            'joining_date' => '2020-01-01',
        ]);

        $approver2 = User::factory()->create([
            'approver_id' => Approver::factory()->create()->id,
            'joining_date' => '2020-01-01',
        ]);

        $leaveRequest = LeaveRequest::factory(1)->create([
            'first_approver_id' => $approver1->id,
            'second_approver_id' => $approver2->id
        ]);

        $data1 = ['id' => $leaveRequest[0]->id,
            'status' => 'approved',
            'first_approver_id' => $approver1->id,
            'first_approver_date' => '2020-01-01',
            'first_approver_status' => 'approved',
            'first_approver_remark' => null
        ];

        $data2 = ['id' => $leaveRequest[0]->id,
            'status' => 'rejected',
            'second_approver_id' => $approver2->id,
            'second_approver_date' => '2020-01-01',
            'second_approver_status' => 'rejected',
            'second_approver_remark' => null
        ];

        $response = $this->actingAs($approver2)->postJson('/api/leave-request/'.$approver2->id.'/approve', $data2);
        $response->assertJson(['message'=> 'You have rejected this leave request']);

        $response = $this->actingAs($approver2)->getJson('/api/leave-request/'.$approver2->id.'/approve');
        $response->assertOk();

        $response = $this->actingAs($approver1)->postJson('/api/leave-request/'.$approver1->id.'/approve', $data1);
        $response->assertJson(['message'=> 'This leave has been rejected by another approver']);

    }

    private function testApproverStatus(String $approverType, String $status, String $message, String $overallStatus): void
    {
        $approver = User::factory()->create([
            'approver_id' => Approver::factory()->create()->id,
            'joining_date' => '2020-01-01',
        ]);

        $leaveRequest = LeaveRequest::factory(1)->create([
            $approverType.'_id' => $approver->id
        ]);

        $data = ['id' => $leaveRequest[0]->id,
            $approverType.'_status' => $status,
            $approverType.'_id' => $approver->id,
            $approverType.'_date' => '2020-01-01',
            $approverType.'_remark' => null
        ];

        $response = $this->actingAs($approver)->postJson('/api/leave-request/'.$approver->id.'/approve', $data);
        $response->assertJson(['message'=> $message]);
    }

    private function createUser(bool $is_admin = false): User
    {
        return User::factory()->create([
            'is_admin' => $is_admin,
            'joining_date' => '2020-01-01',
        ]);
    }
}
