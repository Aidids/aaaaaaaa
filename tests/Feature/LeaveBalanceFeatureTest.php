<?php

namespace Tests\Feature;

use App\Models\LeaveType;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Tests\TestCase;

class LeaveBalanceFeatureTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();

        $this->user = $this->createUser();
        $this->admin = $this->createUser(is_admin: true);
    }

    public function test_get_leave_balance_from_user()
    {
        $response = $this->actingAs($this->admin)->getJson('/api/leave-balance/'.$this->admin->id);
        $response->assertStatus(200);
    }

    public function test_update_existing_leave_type_will_update_all_users_balance()
    {
        $leaveType = LeaveType::factory()->create();

        $response = $this->actingAs($this->admin)->postJson('/api/leave-type/'.$leaveType->id,
            array_merge(
                $this->storeLeaveTypeData(),
                $this->storeEntitlementData(),
            )
        );

        $response->assertOk();
        $response->assertJsonFragment(
            array_merge(
                $this->storeLeaveTypeData(),
                $this->storeEntitlementData(),
            )
        );

        $response = $this->actingAs($this->admin)->getJson('/api/leave-balance/'.$this->admin->id);

        /**
         * Expected outcome
         * Admin joining date = 2020-01-01
         * Default Amount = 14
         * Entitlement = 1 to 3 years : 0
         * Expected balance & total = 14
         */
        $response->assertJsonFragment([
            'balance' => 14,
            'total' => 14
        ]);
    }

    public function test_admin_can_update_leave_balance()
    {
        /**
         only admin can access page
         */
        $response = $this->actingAs($this->user)->get('/api/administration/update-annual-leave');
        $response->assertForbidden();
        $response = $this->actingAs($this->admin)->get('/api/administration/update-annual-leave');
        $response->assertOk();

        /**
         * populate data: create new user and assign leave balances
         * 'name' => 'Annual Leave',
         * 'balance' => 14,
         * 'total' => 14
         */
        $newUser = $this->createNewLeaveAndCheckBalance();

        $response = $this->actingAs($this->admin)->postJson('/api/administration/update-annual-leave',
        [
            'user_id' => $newUser->id,
            'leave_type_id' => 1,
            'pic_id' => $this->admin->id,
            'added_qty' => 20,
            'remark' => 'test',
        ]);

        $response->assertOk();
    }

    public function test_user_cannot_update_leave_balance()
    {
        $newUser = $this->createNewLeaveAndCheckBalance();

        $response = $this->actingAs($newUser)->postJson('/api/administration/update-annual-leave',
            [
                'user_id' => $newUser->id,
                'pic_id' => $this->admin->id,
                'added_qty' => 20,
                'remark' => 'test',
            ]);

        $response->assertForbidden();
    }

    private function createUser(bool $is_admin = false)
    {
        return User::factory()->create([
            'is_admin' => $is_admin,
            'joining_date' => '2020-01-01'
        ]);
    }

    private function storeLeaveTypeData(): array
    {
        return [
            'name' => 'Annual Leave',
            'description' => 'For Internal Annual Leave',
            'amount' => 14,
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
                    'end_period' => 4,
                    'amount' => 0
                ],
                [
                    'id' => 2,
                    'start_period' => 5,
                    'end_period' => 7,
                    'amount' => 2
                ],
                [
                    'id' => 3,
                    'start_period' => 8,
                    'end_period' => 9,
                    'amount' => 3
                ],
                [
                    'id' => 4,
                    'start_period' => 10,
                    'end_period' => 100,
                    'amount' => 6
                ]
            ],
        ];
    }

    private function adminUpdateLeaveData(): array
    {
        return [
            'user_id' => 1,
        ];
    }

    private function createNewLeaveAndCheckBalance()
    {
        /**
         * Admin Create New Leave Type
         */
        $response = $this->actingAs($this->admin)->postJson('/api/leave-type',
            array_merge(
                $this->storeLeaveTypeData(),
                $this->storeEntitlementData(),
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
            'balance' => 14,
            'total' => 14
        ]);

        return $newUser; // You can return any data you might need for further tests.
    }
}
