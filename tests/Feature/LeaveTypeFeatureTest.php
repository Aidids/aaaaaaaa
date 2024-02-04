<?php

namespace Tests\Feature;

use App\Models\LeaveEntitlement;
use App\Models\LeaveType;
use App\Models\User;
use App\Services\LeaveBalanceService;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Tests\TestCase;

class LeaveTypeFeatureTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();

        $this->user = $this->createUser();
        $this->admin = $this->createUser(is_admin: true);
        $this->leaveBalanceService = new LeaveBalanceService();
    }

    public function test_get_all_leave_type()
    {
        $response = $this->actingAs($this->admin)->getJson('/api/leave-type');
        $response->assertStatus(200);
    }

    public function test_create_new_leave_type()
    {
        $response = $this->actingAs($this->admin)->postJson('/api/leave-type', $this->storeLeaveTypeData());
        $response->assertCreated();
        $response->assertJsonFragment($this->storeLeaveTypeData());
    }

    public function test_update_existing_leave_type()
    {
        $leaveType = LeaveType::factory()->create();

        $response = $this->actingAs($this->admin)->postJson('/api/leave-type/'.$leaveType->id,
            $this->storeLeaveTypeData());

        $response->assertOk();
        $response->assertJsonFragment($this->storeLeaveTypeData());
    }

    public function test_create_new_leave_type_with_entitlement()
    {
        $response = $this->actingAs($this->admin)->postJson('/api/leave-type',
            array_merge($this->storeLeaveTypeData(), $this->storeEntitlementData())
        );
        $response->assertCreated();
        $response->assertJsonFragment(array_merge($this->storeLeaveTypeData(), $this->storeEntitlementData()));
    }

    public function test_update_existing_leave_type_with_entitlement()
    {
        $leaveType = LeaveType::factory()->create();

        $response = $this->actingAs($this->admin)->postJson('/api/leave-type/'.$leaveType->id,
            array_merge($this->storeLeaveTypeData(), $this->storeEntitlementData()));
        $response->assertOk();
        $response->assertJsonFragment($this->storeEntitlementData());
    }

    public function test_create_leave_type_with_carry_forward()
    {
        $response = $this->actingAs($this->admin)->postJson('/api/leave-type',
            array_merge($this->storeLeaveTypeData(), $this->storeCarryForwardData())
        );
        $response->assertCreated();
        $response->assertJsonFragment(array_merge($this->storeLeaveTypeData(), $this->storeCarryForwardData()));
    }

    public function test_update_existing_leave_type_with_carry_forward()
    {
        $leaveType = LeaveType::factory()->create();

        $response = $this->actingAs($this->admin)->postJson('/api/leave-type/'.$leaveType->id,
            array_merge($this->storeLeaveTypeData(), $this->storeCarryForwardData()));
        $response->assertOk();
        $response->assertJsonFragment($this->storeCarryForwardData());
    }

    public function test_create_leave_type_with_all_attributes()
    {
        $response = $this->actingAs($this->admin)->postJson('/api/leave-type',
            array_merge(
                $this->storeLeaveTypeData(),
                $this->storeEntitlementData(),
                $this->storeCarryForwardData()
            )
        );

        $response->assertCreated();

        $response->assertJsonFragment(
            array_merge(
                $this->storeLeaveTypeData(),
                $this->storeEntitlementData(),
                $this->storeCarryForwardData()
            )
        );
    }

    public function test_update_leave_type_without_duplication()
    {
        $leaveType = LeaveType::factory()->create();

        $response = $this->actingAs($this->admin)->postJson('/api/leave-type/'.$leaveType->id,
            array_merge(
                $this->storeLeaveTypeData(),
                $this->storeEntitlementData(),
                $this->storeCarryForwardData()
            )
        );
        $response->assertOk();
        $response->assertJsonFragment(
            array_merge(
                $this->storeLeaveTypeData(),
                $this->storeEntitlementData(),
                $this->storeCarryForwardData()
            )
        );

        $response = $this->actingAs($this->admin)->postJson('/api/leave-type/'.$leaveType->id,
            array_merge(
                $this->storeLeaveTypeData(),
                $this->duplicatedEntitlementData(),
                $this->duplicatedCarryForwardData(),
            ));
        $response->assertOk();
        $response->assertJsonFragment(
            array_merge(
                $this->duplicatedEntitlementData(),
                $this->duplicatedCarryForwardData(),
            )
        );
    }

    public function test_delete_leave_type()
    {
        $leaveType = LeaveType::factory()->create();
        $response = $this->actingAs($this->admin)->deleteJson('/api/leave-type/'.$leaveType->id);
        $response->assertStatus(200);
    }

    public function test_all_users_got_leave_balance_after_creating_leave_type()
    {
        $user = User::factory()->create([
            'is_admin' => true,
            'joining_date' => '2000-01-01',
        ]);

        $response = $this->actingAs($user)->postJson('/api/leave-type',
            array_merge(
                $this->storeLeaveTypeData(),
                $this->storeEntitlementData(),
                $this->storeCarryForwardData(),
            )
        );
        $response->assertCreated();
        $response->assertJsonFragment($this->storeLeaveTypeData());

        $this->artisan('leave:onboarding');

        $response = $this->actingAs($user)->getJson('/api/leave-balance/'.$user->id);
        $response->assertStatus(200);

        $response->assertJsonFragment([
            // expected outcome = storeleaveTypeData + storeEntitlementData
            // balance = 69 + 600
            'balance' => 669,
            'total' => 669
        ]);
    }

    private function createUser(bool $is_admin = false)
    {
        return User::factory()->create([
            'is_admin' => $is_admin,
        ]);
    }

    private function storeLeaveTypeData(): array
    {
        return [
            'name' => 'Annual Leave',
            'description' => 'For Internal Annual Leave',
            'amount' => 69,
            'pro_rated' => false
        ];
    }

    private function storeEntitlementData(): array
    {
        return [
            'entitlement' => [
                [
                    'id' => 1,
                    'start_period' => 1,
                    'end_period' => 3,
                    'amount' => 69
                ],
                [
                    'id' => 2,
                    'start_period' => 4,
                    'end_period' => 100,
                    'amount' => 600
                ]
            ],
        ];
    }

    private function storeCarryForwardData(): array
    {
        return [
            'carry_forward' => [
                [
                    'id' => 1,
                    'start_period' => 3,
                    'end_period' => 5,
                    'amount' => 12
                ],
                [
                    'id' => 2,
                    'start_period' => 6,
                    'end_period' => 0,
                    'amount' => 15
                ]
            ],
        ];
    }

    private function duplicatedEntitlementData(): array
    {
        return [
            'entitlement' => [
                [
                    'id' => 1,
                    'start_period' => 2,
                    'end_period' => 3,
                    'amount' => 100
                ],
                [
                    'id' => 2,
                    'start_period' => 4,
                    'end_period' => 5,
                    'amount' => 200
                ],
                [
                    'id' => 3,
                    'start_period' => 6,
                    'end_period' => 7,
                    'amount' => 300
                ]
            ]
        ];
    }

    private function duplicatedCarryForwardData() :array
    {
        return [
            'carry_forward' => [
                [
                    'id' => 1,
                    'start_period' => 8,
                    'end_period' => 9,
                    'amount' => 101
                ],
                [
                    'id' => 2,
                    'start_period' => 10,
                    'end_period' => 11,
                    'amount' => 201
                ],
                [
                    'id' => 3,
                    'start_period' => 12,
                    'end_period' => 13,
                    'amount' => 301
                ]
            ]
        ];
    }
}
