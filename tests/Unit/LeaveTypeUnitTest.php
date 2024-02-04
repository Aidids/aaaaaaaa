<?php

namespace Tests\Unit;

use App\Models\User;
use App\Services\LeaveTypeService;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class LeaveTypeUnitTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();

        $this->user = $this->createUser();
        $this->admin = $this->createUser(is_admin: true);
        $this->leaveTypeService = new LeaveTypeService();
    }

    public function test_store_leave_type_with_multiple_entitlement_data()
    {
        $this->leaveTypeService->store(array_merge($this->storeLeaveTypeData(), $this->storeEntitlementData()));

        $this->assertDatabaseHas('leave_entitlements', [
            'start_period' => 1,
            'end_period' => 3,
            'amount' => 69
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
                    'start_period' => 1,
                    'end_period' => 3,
                    'amount' => 69
                ],
                [
                    'start_period' => 4,
                    'end_period' => 0,
                    'amount' => 12
                ]
            ],
        ];
    }
}
