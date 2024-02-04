<?php

namespace Tests\Feature;

use App\Models\Approver;
use App\Models\User;
use App\Services\LeaveBalanceService;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Tests\TestCase;

class UserFeatureTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();
        $this->user = $this->createUser();
        $this->admin = $this->createUser(is_admin: true);
        $this->leaveBalanceService = new LeaveBalanceService();
    }

    public function test_get_leave_type_when_admin_change_profile_joining_date()
    {
        /**
         * Admin Create New Leave Type
         */
        $response = $this->actingAs($this->admin)->postJson('/api/leave-type',
            array_merge(
                $this->storeLeaveTypeData(),
                $this->storeEntitlementData(),
                $this->storeCarryForwardData()
            )
        );

        $response->assertCreated();

        /**
         * New user added to AD
         * Expected leave balance should be empty
         */
        $newUser = User::factory()->create();
        $response = $this->actingAs($newUser)->getJson('/api/leave-balance/'.$newUser->id);
        $response->assertJsonFragment([ /*assert data is empty*/ ]);

        /**
         * Admin update new profile-settings contact no
         * Leave balance should be empty
         */
        $this->actingAs($this->admin)->postJson('/api/profile-settings/'.$newUser->id,
            [
                'contact_no' => '017-8056063'
            ]);
        $response = $this->actingAs($newUser)->getJson('/api/leave-balance/'.$newUser->id);
        $response->assertJsonFragment([/*assert data is empty*/]);

        /**
         * When admin update profile-settings join date,
         * User should get appropriate balance
         */
        $this->actingAs($this->admin)->postJson('/api/profile-settings/'.$newUser->id,
            [
                'joining_date' => '2022-01-01'
            ]);
        $response = $this->actingAs($newUser)->getJson('/api/leave-balance/'.$newUser->id);

        $response->assertJsonFragment([
            'balance' => 138,
            'total' => 138
        ]);

    }

    public function test_user_get_leave_balance_after_logging_in()
    {
        $response = $this->actingAs($this->admin)->postJson('/api/leave-type', $this->storeLeaveTypeData());
        $response->assertCreated();
        $response->assertJsonFragment([
            'name' => 'Annual Leave',
            'description' => 'For Internal Annual Leave',
            'amount' => 69,
            'pro_rated' => true
        ]);

        $newUser = User::factory()->create();
        $response = $this->actingAs($newUser)->getJson('/api/leave-balance/'.$newUser->id);
        $response->assertJsonFragment([ /*assert data is empty*/ ]);

        $this->leaveBalanceService->assignLeaveBalance($newUser);
        $response = $this->actingAs($newUser)->getJson('/api/leave-balance/'.$newUser->id);
        $response->assertJsonFragment([
            'balance' => 0,
            'total' => 0
        ]);

    }

    private function storeLeaveTypeData(): array
    {
        return [
            'name' => 'Annual Leave',
            'description' => 'For Internal Annual Leave',
            'amount' => 69,
            'pro_rated' => true,
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

    public function createUser(bool $is_admin = false)
    {
        return User::factory()->create([
            'is_admin' => $is_admin,
            'joining_date' => '2023-01-01',
        ]);
    }

    public function create_approver(): User
    {
        return User::factory()->create([
            'approver_id' => Approver::factory()->create()->id,
            'joining_date' => fake()->date('Y-m-d'),
        ]);
    }
}
