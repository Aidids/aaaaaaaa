<?php

namespace Tests\Unit;

use App\Models\LeaveEntitlement;
use App\Models\LeaveType;
use App\Models\User;
use App\Services\LeaveBalanceService;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class LeaveBalanceUnitTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();
        $this->leaveBalanceService = new LeaveBalanceService();
    }

    public function test_calculate_employee_months_of_service()
    {
        $user1 = User::factory()->create([
            'joining_date' => null
        ]);

        $user2 = User::factory()->create([
            'joining_date' => '2023-01-14'
        ]);

        $user3 = User::factory()->create([
            'joining_date' => '2023-01-18'
        ]);

        $user4 = User::factory()->create([
            'joining_date' => '2023-02-07'
        ]);

        $user5 = User::factory()->create([
            'joining_date' => '2023-02-15'
        ]);

        $user6 = User::factory()->create([
            'joining_date' => '2023-12-01'
        ]);

        $user7 = User::factory()->create([
            'joining_date' => '2023-12-16'
        ]);

        $result = $this->leaveBalanceService->calcMonthsOfService($user1);
        $this->assertEquals(0, $result);

        $result = $this->leaveBalanceService->calcMonthsOfService($user2);
        $this->assertEquals(12, $result);

        $result = $this->leaveBalanceService->calcMonthsOfService($user3);
        $this->assertEquals(12, $result);

        $result = $this->leaveBalanceService->calcMonthsOfService($user4);
        $this->assertEquals(12, $result);

        $result = $this->leaveBalanceService->calcMonthsOfService($user5);
        $this->assertEquals(12, $result);

        $result = $this->leaveBalanceService->calcMonthsOfService($user6);
        $this->assertEquals(12, $result);

        $result = $this->leaveBalanceService->calcMonthsOfService($user7);
        $this->assertEquals(12, $result);
    }

    public function test_calculate_users_entitlement()
    {
        $leaveType = LeaveType::factory()->create([]);

        LeaveEntitlement::factory()->create([
            'leave_type_id' => $leaveType->id,
            'start_period' => 1,
            'end_period' => 2,
            'amount' => 69,
        ]);

        LeaveEntitlement::factory()->create([
            'leave_type_id' => $leaveType->id,
            'start_period' => 3,
            'end_period' => 4,
            'amount' => 420,
        ]);

        LeaveEntitlement::factory()->create([
            'leave_type_id' => $leaveType->id,
            'start_period' => 6,
            'end_period' => 100,
            'amount' => 1000,
        ]);

        $user = User::factory()->create([ 'joining_date' => '2016-01-01']);
        $result = $this->leaveBalanceService->calcEntitlement($leaveType, $user);
        $this->assertEquals(1000, $result);

        $user = User::factory()->create([ 'joining_date' => '2020-01-01']);
        $result = $this->leaveBalanceService->calcEntitlement($leaveType, $user);
        $this->assertEquals(420, $result);

        $user = User::factory()->create([ 'joining_date' => '2022-01-01']);
        $result = $this->leaveBalanceService->calcEntitlement($leaveType, $user);
        $this->assertEquals(69, $result);

        $user = User::factory()->create([ 'joining_date' => '2024-01-01']);
        $result = $this->leaveBalanceService->calcEntitlement($leaveType, $user);
        $this->assertEquals(0, $result);

        $user = User::factory()->create([ 'joining_date' => null]);
        $result = $this->leaveBalanceService->calcEntitlement($leaveType, $user);
        $this->assertEquals(0, $result);
    }
}
