<?php

namespace Tests\Feature;

use App\Enums\LeaveRequestStatus;
use App\Models\LeaveBalance;
use App\Models\LeaveRequest;
use App\Models\LeaveType;
use App\Models\User;
use Illuminate\Foundation\Testing\DatabaseTransactions;
use Illuminate\Support\Facades\Artisan;
use Tests\TestCase;

use Tests\Feature\AssignUserLeaveBalanceFeatureTest;

class LeaveDeductionFeatureTest extends TestCase
{
    use DatabaseTransactions;

    private static $userBalance;

    public static function setUpBeforeClass(): void
    {
        self::$userBalance = new AssignUserLeaveBalanceFeatureTest();
    }

    public function setUp(): void
    {
        parent::setUp();
        LeaveRequest::unsetEventDispatcher(); // Disable Custom Portal Notifications Module

        self::$userBalance->setUser();
        $this->user = self::$userBalance->userMale4;
    }

    public function test_user_apply_annual_leave_deduct_annual_only()
    {
        $this->user_apply_leave(
            user: $this->user, //user join 2023-01-01
            leaveTypeID: 1,
            duration: 1,
        );

        // deduct user balance through leave deduction console flow
        $this->artisan('leave:deduction');

        // check user balance after deduction
        $this->checkUserBalance(
            user: $this->user,  //user join 2023-01-01
            data: [1, 13, 1, 14] // leave_type_id, balance, taken, total
        );
    }

    public function test_user_apply_annual_leave_but_empty_balance()
    {
        // SIMULATE USER HAS USED ALL BALANCE
        $this->updateUserBalance(
            userID: $this->user->id,
            leaveTypeID: 1,
            reduceAmount: 14
        );

        // CHECK IF USER BALANCE IS EMPTY
        $this->checkUserBalance(
            user: $this->user,
            data: [1, 0, 14, 14] // leave_type_id, balance, taken, total
        );

        // USER APPLYING FOR LEAVE (AL)
        $response = $this->user_apply_leave(
            user: $this->user, //user join 2023-01-01
            leaveTypeID: 1,
            duration: 1,
        );
        $response->assertJsonFragment(['Balance insufficient, Your balance is : 0 Day\'s']);
    }

    public function test_user_apply_annual_leave_but_no_join_date()
    {
        // SIMULATE USER WITH NO JOINING DATE
        $this->checkUserBalance(
            user: self::$userBalance->userNoJoinDate,
            data: [1, 0, 0, 0] // leave_type_id, balance, taken, total
        );

        // USER ATTEMPT TO APPLY FOR LEAVE (AL)
        $response = $this->user_apply_leave(
            user: self::$userBalance->userNoJoinDate, //user join 2023-01-01
            leaveTypeID: 1,
            duration: 1,
        );
        $response->assertJsonFragment(['Balance insufficient, Your balance is : 0 Day\'s']);
    }

    public function test_user_apply_medical_leave()
    {
        $this->user_apply_leave(
            user: $this->user, //user join 2023-01-01
            leaveTypeID: 2,
            duration: 1,
        );

        // deduct user balance through leave deduction console flow
        $this->artisan('leave:deduction');

        // check user balance after deduction
        $this->checkUserBalance(
            user: $this->user,  //user join 2023-01-01
            data: [2, 13, 1, 14] // leave_type_id, balance, taken, total
        );
    }

    public function test_user_apply_medical_but_empty_balance()
    {
        // SIMULATE USER HAS USED ALL BALANCE
        $this->updateUserBalance(
            userID: $this->user->id,
            leaveTypeID: 2,
            reduceAmount: 14
        );

        // CHECK IF USER BALANCE IS EMPTY
        $this->checkUserBalance(
            user: $this->user,
            data: [2, 0, 14, 14] // leave_type_id, balance, taken, total
        );

        // USER ATTEMPT TO APPLY FOR LEAVE (MEDICAL)
        $response = $this->user_apply_leave(
            user: $this->user, //user join 2023-01-01
            leaveTypeID: 2,
            duration: 1,
        );
        $response->assertJsonFragment(['Balance insufficient, Your balance is : 0 Day\'s']);
    }

    public function test_user_apply_hospitalization_leave()
    {
        $this->user_apply_leave(
            user: $this->user, //user join 2023-01-01
            leaveTypeID: 3,
            duration: 1,
        );

        // deduct user balance through leave deduction console flow
        $this->artisan('leave:deduction');

        // check user balance after deduction
        $this->checkUserBalance(
            user: $this->user,  //user join 2023-01-01
            data: [3, 59, 1, 60] // leave_type_id, balance, taken, total
        );
    }

    public function test_user_apply_hospitalization_but_empty_balance()
    {
        // SIMULATE USER HAS USED ALL BALANCE
        $this->updateUserBalance(
            userID: $this->user->id,
            leaveTypeID: 3,
            reduceAmount: 60
        );

        // CHECK IF USER BALANCE IS EMPTY
        $this->checkUserBalance(
            user: $this->user,
            data: [3, 0, 60, 60] // leave_type_id, balance, taken, total
        );

        // USER ATTEMPT TO APPLY FOR LEAVE (MEDICAL)
        $response = $this->user_apply_leave(
            user: $this->user, //user join 2023-01-01
            leaveTypeID: 3,
            duration: 1,
        );
        $response->assertJsonFragment(['Balance insufficient, Your balance is : 0 Day\'s']);
    }

    public function test_user_apply_unpaid_leave_deduct_offshore_balance()
    {
        // UPDATE USER OFFSHORE FOR SIMULATION PURPOSE
        $this->updateUserOffshoreBalance(user: $this->user, newBalance: 3);

        // user apply UNPAID leave
        $this->user_apply_leave(
            user: $this->user,
            leaveTypeID: 4, duration: 3,
        );

        // deduct user balance through leave deduction console flow
        $this->artisan('leave:deduction');

        // check user balance after deduction
        $this->checkLeaveDeductionBalanceFlow(
            user: $this->user,
            result: 'offshore',
            expectedBalance: [
                'offshoreBalance' => 0, 'offshoreTaken' => 3, 'offshoreTotal' => 3,
                'annualBalance' => 14, 'annualTaken' => 0, 'annualTotal' => 14,
                'unpaidBalance' => 365, 'unpaidTaken' => 0, 'unpaidTotal' => 365,
            ],
        );
    }

    public function test_user_apply_unpaid_leave_deduct_offshore_and_annual_balance()
    {
        // UPDATE USER OFFSHORE FOR SIMULATION PURPOSE
        $this->updateUserOffshoreBalance(user: $this->user, newBalance: 2);

        // user apply UNPAID leave
        $this->user_apply_leave(
            user: $this->user,
            leaveTypeID: 4, duration: 6,
        );

        // deduct user balance through leave deduction console flow
        $this->artisan('leave:deduction');

        // check user balance after deduction
        $this->checkLeaveDeductionBalanceFlow(
            user: $this->user,
            result: 'offshore & annual',
            expectedBalance: [
                'offshoreBalance' => 0, 'offshoreTaken' => 2, 'offshoreTotal' => 2,
                'annualBalance' => 10, 'annualTaken' => 4, 'annualTotal' => 14,
                'unpaidBalance' => 365, 'unpaidTaken' => 0, 'unpaidTotal' => 365,
            ],
        );
    }

    public function test_user_apply_unpaid_leave_deduct_offshore_annual_and_unpaid_balance()
    {
        // UPDATE USER OFFSHORE FOR SIMULATION PURPOSE
        $this->updateUserOffshoreBalance(user: $this->user, newBalance: 2);

        // SIMULATE USER HAS 2 AL BALANCE LEFT
        $this->updateUserBalance(
            userID: $this->user->id,
            leaveTypeID: 1,
            reduceAmount: 12
        );

        // user apply UNPAID leave
        $this->user_apply_leave(
            user: $this->user,
            leaveTypeID: 4, duration: 10,
        );

        // deduct user balance through leave deduction console flow
        $this->artisan('leave:deduction');

        // check user balance after deduction
        $this->checkLeaveDeductionBalanceFlow(
            user: $this->user,
            result: 'offshore, annual, & unpaid',
            expectedBalance: [
                'offshoreBalance' => 0, 'offshoreTaken' => 2, 'offshoreTotal' => 2,
                'annualBalance' => 0, 'annualTaken' => 14, 'annualTotal' => 14,
                'unpaidBalance' => 359, 'unpaidTaken' => 6, 'unpaidTotal' => 365,
            ],
        );
    }

    public function test_user_apply_emergency_leave_deduct_offshore()
    {
        // UPDATE USER OFFSHORE FOR SIMULATION PURPOSE
        $this->updateUserOffshoreBalance(user: $this->user, newBalance: 1);

        // user apply EMERGENCY leave
        $this->user_apply_leave(
            user: $this->user,
            leaveTypeID: 5, duration: 1,
        );

        // deduct user balance through leave deduction console flow
        $this->artisan('leave:deduction');

        // check user balance after deduction
        $this->checkLeaveDeductionBalanceFlow(
            user: $this->user,
            result: 'offshore',
            expectedBalance: [
                'offshoreBalance' => 0, 'offshoreTaken' => 1, 'offshoreTotal' => 1,
                'annualBalance' => 14, 'annualTaken' => 0, 'annualTotal' => 14,
                'unpaidBalance' => 365, 'unpaidTaken' => 0, 'unpaidTotal' => 365,
            ],
        );

        // check user EMERGENCY BALANCE after deduction
        $this->checkUserBalance(
            user: $this->user,
            data: [5, 364, 1, 365] // leave_type_id, balance, taken, total
        );
    }

    public function test_user_apply_emergency_leave_deduct_offshore_and_unpaid()
    {
        // UPDATE USER OFFSHORE FOR SIMULATION PURPOSE
        $this->updateUserOffshoreBalance(user: $this->user, newBalance: 1);

        // SIMULATE USER HAS 0 AL BALANCE LEFT
        $this->updateUserBalance(
            userID: $this->user->id,
            leaveTypeID: 1,
            reduceAmount: 14
        );

        // user apply EMERGENCY leave
        $this->user_apply_leave(
            user: $this->user,
            leaveTypeID: 5, duration: 2,
        );

        // deduct user balance through leave deduction console flow
        $this->artisan('leave:deduction');

        // check user balance after deduction
        $this->checkLeaveDeductionBalanceFlow(
            user: $this->user,
            result: 'offshore & unpaid',
            expectedBalance: [
                'offshoreBalance' => 0, 'offshoreTaken' => 1, 'offshoreTotal' => 1,
                'annualBalance' => 0, 'annualTaken' => 0, 'annualTotal' => 14,
                'unpaidBalance' => 364, 'unpaidTaken' => 1, 'unpaidTotal' => 365,
            ],
        );

        // check user EMERGENCY BALANCE after deduction
        $this->checkUserBalance( // Offshore Type
            user: $this->user,
            data: [5, 364, 1, 365] // leave_type_id, balance, taken, total
        );
        $this->checkUserBalance( // Unpaid Type
            user: $this->user,
            data: [4, 364, 1, 365] // leave_type_id, balance, taken, total
        );
    }

    public function test_user_apply_emergency_leave_deduct_annual_and_unpaid()
    {
        // SIMULATE USER HAS 1 AL BALANCE LEFT
        $this->updateUserBalance(
            userID: $this->user->id,
            leaveTypeID: 1,
            reduceAmount: 13
        );

        // user apply EMERGENCY leave
        $this->user_apply_leave(
            user: $this->user,
            leaveTypeID: 5, duration: 2,
        );

        // deduct user balance through leave deduction console flow
        $this->artisan('leave:deduction');

        // check user balance after deduction
        $this->checkLeaveDeductionBalanceFlow(
            user: $this->user,
            result: 'annual & unpaid',
            expectedBalance: [
                'offshoreBalance' => 0, 'offshoreTaken' => 0, 'offshoreTotal' => 0,
                'annualBalance' => 0, 'annualTaken' => 14, 'annualTotal' => 14,
                'unpaidBalance' => 364, 'unpaidTaken' => 1, 'unpaidTotal' => 365,
            ],
        );

        // check user EMERGENCY BALANCE after deduction
        $this->checkUserBalance( // Offshore Type
            user: $this->user,
            data: [1, 0, 14, 14] // leave_type_id, balance, taken, total
        );
        $this->checkUserBalance( // Unpaid Type
            user: $this->user,
            data: [4, 364, 1, 365] // leave_type_id, balance, taken, total
        );
    }

    public function test_user_apply_emergency_leave_deduct_offshore_and_annual()
    {
        // UPDATE USER OFFSHORE FOR SIMULATION PURPOSE
        $this->updateUserOffshoreBalance(user: $this->user, newBalance: 3);

        // SIMULATE USER HAS 1 AL BALANCE LEFT
        $this->updateUserBalance(
            userID: $this->user->id,
            leaveTypeID: 1,
            reduceAmount: 13
        );

        // user apply EMERGENCY leave
        $this->user_apply_leave(
            user: $this->user,
            leaveTypeID: 5, duration: 4,
        );

        // deduct user balance through leave deduction console flow
        $this->artisan('leave:deduction');

        // check user balance after deduction
        $this->checkLeaveDeductionBalanceFlow(
            user: $this->user,
            result: 'offshore & annual',
            expectedBalance: [
                'offshoreBalance' => 0, 'offshoreTaken' => 3, 'offshoreTotal' => 3,
                'annualBalance' => 0, 'annualTaken' => 14, 'annualTotal' => 14,
                'unpaidBalance' => 365, 'unpaidTaken' => 0, 'unpaidTotal' => 365,
            ],
        );

        // check user EMERGENCY BALANCE after deduction
        $this->checkUserBalance(
            user: $this->user,
            data: [5, 361, 4, 365] // leave_type_id, balance, taken, total
        );
    }

    public function test_user_apply_emergency_leave_deduct_offshore_annual_and_unpaid()
    {
        // UPDATE USER OFFSHORE FOR SIMULATION PURPOSE
        $this->updateUserOffshoreBalance(user: $this->user, newBalance: 2);

        // SIMULATE USER HAS 3 AL BALANCE LEFT
        $this->updateUserBalance(
        userID: $this->user->id,
            leaveTypeID: 1,
            reduceAmount: 11
        );

        // user apply EMERGENCY leave
        $this->user_apply_leave(
            user: $this->user,
            leaveTypeID: 5, duration: 8,
        );

        // deduct user balance through leave deduction console flow
        $this->artisan('leave:deduction');

        // check user balance after deduction
        $this->checkLeaveDeductionBalanceFlow(
            user: $this->user,
        result: 'offshore, annual, & unpaid',
        expectedBalance:[
            'offshoreBalance' => 0, 'offshoreTaken' => 2, 'offshoreTotal' => 2,
            'annualBalance' => 0, 'annualTaken' => 14, 'annualTotal' => 14,
                'unpaidBalance' => 362, 'unpaidTaken' => 3, 'unpaidTotal' => 365,
            ],
            );

        // check user EMERGENCY BALANCE after deduction
        $this->checkUserBalance(
            user: $this->user,
            data: [5, 357, 8, 365] // leave_type_id, balance, taken, total
        );
    }

    public function test_user_apply_paternity_leave()
    {
        $this->user_apply_leave(
            user: $this->user,
        leaveTypeID: 6,
            duration: 5,
        );

        // deduct user balance through leave deduction console flow

        $this->artisan('leave:deduction');

        // check user balance after deduction
        $this->checkUserBalance(
            user: $this->user,
        data: [6, 2, 5, 7] // leave_type_id, balance, taken, total
        );
    }

    public function test_female_user_apply_maternity_leave()
    {
        $this->user_apply_leave(
        user: self::$userBalance->userFemale,
            leaveTypeID: 7,
        duration: 72,
        );

        // deduct user balance through leave deduction console flow

        $this->artisan('leave:deduction');

        // check user balance after deduction
        $this->checkUserBalance(
            user: self::$userBalance->userFemale,
            data: [7, 26, 72, 98] // leave_type_id, balance, taken, total
        );
        }

    public function test_user_apply_out_of_office_leave()
    {
        $this->user_apply_leave(
            user: $this->user,
            leaveTypeID: 11,
            duration: 1,
        );


        // deduct user balance through leave deduction console flow
        $this->artisan('leave:deduction');

        // check user balance after deduction
        $this->checkUserBalance(
            user: $this->user,
            data: [11, 364, 1, 365] // leave_type_id, balance, taken, total
        );
    }

    public function test_user_apply_quarantine_leave()
    {
        $quarantineLeave = LeaveType::create([
            'name' => 'Quarantine Leave',
            'amount' => 365,
            'description' => 'Dayang\'s Kuala Lumpur headquarters quarantine leave type (Ex: covid, influenza)'
        ]);

        LeaveBalance::create([
            'user_id' => $this->user->id,
            'leave_type_id' => $quarantineLeave->id,
            'proRated' => 0, 'entitlement' => 0, 'carry_forward' => 0,
            'balance' => 365, 'total' => 365, 'taken' => 0,
        ]);

        $this->user_apply_leave(
            user: $this->user,
            leaveTypeID: $quarantineLeave->id,
            duration: 6,
        );

        // deduct user balance through leave deduction console flow
        $this->artisan('leave:deduction');

        // check user balance after deduction
        $this->checkUserBalance(
            user: $this->user,
            data: [$quarantineLeave->id, 359, 6, 365] // leave_type_id, balance, taken, total
        );
    }

    public function test_user_apply_offshore_leave()
    {
        // UPDATE USER OFFSHORE FOR SIMULATION PURPOSE
        $this->updateUserOffshoreBalance(user: $this->user, newBalance: 5);

        $this->user_apply_leave(
            user: $this->user,
            leaveTypeID: 8,
        duration: 3,
        );

        // deduct user balance through leave deduction console flow
        $this->artisan('leave:deduction');

        // check user balance after deduction
        $this->checkUserBalance(
            user: $this->user,
            data: [8, 2, 3, 5] // leave_type_id, balance, taken, total
        );
    }

    public function test_user_apply_offshore_but_empty_balance()
    {
        $response = $this->user_apply_leave(
            user: $this->user,
            leaveTypeID: 8,
            duration: 2,
        );

        $response->assertJsonFragment(['Balance insufficient, Your balance is : 0 Day\'s']);
    }

    public function test_force_leave_deduct_offshore_only()
    {
        // UPDATE USER OFFSHORE FOR SIMULATION PURPOSE
        $this->updateUserOffshoreBalance(user: $this->user, newBalance: 1);
        $response = $this->simulateForceLeave(user: $this->user, duration: 1);
        $response->assertOk();

        // check user balance after deduction
        $this->checkLeaveDeductionBalanceFlow(
            user: $this->user,
            result: 'offshore',
            expectedBalance: [
                'offshoreBalance' => 0, 'offshoreTaken' => 1, 'offshoreTotal' => 1,
                'annualBalance' => 14, 'annualTaken' => 0, 'annualTotal' => 14,
                'unpaidBalance' => 365, 'unpaidTaken' => 0, 'unpaidTotal' => 365,
            ],
        );
    }

    public function test_force_leave_deduct_offshore_and_annual()
    {
        // UPDATE USER OFFSHORE FOR SIMULATION PURPOSE
        $this->updateUserOffshoreBalance(user: $this->user, newBalance: 1);

        // SIMULATE USER HAS 1 AL BALANCE LEFT
        $this->updateUserBalance(
            userID: $this->user->id,
            leaveTypeID: 1,
            reduceAmount: 12
        );

        $response = $this->simulateForceLeave(user: $this->user, duration: 2);
        $response->assertOk();

        // check user balance after deduction
        $this->checkLeaveDeductionBalanceFlow(
            user: $this->user,
            result: 'offshore & annual',
            expectedBalance: [
                'offshoreBalance' => 0, 'offshoreTaken' => 1, 'offshoreTotal' => 1,
                'annualBalance' => 1, 'annualTaken' => 13, 'annualTotal' => 14,
                'unpaidBalance' => 365, 'unpaidTaken' => 0, 'unpaidTotal' => 365,
            ],
        );
    }

    public function test_force_leave_deduct_offshore_annual_and_unpaid()
    {
        // UPDATE USER OFFSHORE FOR SIMULATION PURPOSE
        $this->updateUserOffshoreBalance(user: $this->user, newBalance: 2);

        // SIMULATE USER HAS 1 AL BALANCE LEFT
        $this->updateUserBalance(
            userID: $this->user->id,
            leaveTypeID: 1,
            reduceAmount: 12
        );

        $response = $this->simulateForceLeave(user: $this->user, duration: 6);
        $response->assertOk();

        // check user balance after deduction
        $this->checkLeaveDeductionBalanceFlow(
            user: $this->user,
            result: 'offshore, annual, & unpaid',
            expectedBalance: [
                'offshoreBalance' => 0, 'offshoreTaken' => 2, 'offshoreTotal' => 2,
                'annualBalance' => 0, 'annualTaken' => 14, 'annualTotal' => 14,
                'unpaidBalance' => 363, 'unpaidTaken' => 2, 'unpaidTotal' => 365,
            ],
        );
    }


    ###################################################################################
    // PRIVATE FUNCTION FOR THIS TEST FILE USAGE ONLY
    ###################################################################################
    private function user_apply_leave(User $user, int $leaveTypeID, int $duration)
    {
        $leaveBalance = LeaveBalance::where([
            'user_id' => $user->id,
            'leave_type_id' => $leaveTypeID,
        ])->first();

        $leaveRequestData = [
            'leave_balance_id' => $leaveBalance->id,
            'start_date' => '2023-09-01', 'end_date' => '2023-09-01',
            'start_date_type' => 'full day', 'end_date_type' => 'full day',
            'duration' => $duration,
            'first_approver_id' => self::$userBalance->approver1->id,
            'overall_status' => LeaveRequestStatus::approved->value,
        ];

        return $this->actingAs($user)->postJson('/api/leave-request/' . $user->id, $leaveRequestData);
    }

    private function checkUserBalance(User $user, Array $data): void
    {
        $response = $this->actingAs($user)->getJson('/api/leave-balance/'.$user->id);
        $response->assertJsonFragment([
            'leave_type_id' => $data[0], // expected leave_type_id
            'balance' => $data[1], // expected balance
            'taken' => $data[2], // expected taken
            'total' => $data[3], // expected total
        ]);
    }

    private function updateUserBalance(int $userID, int $leaveTypeID, int $reduceAmount)
    {
        $leaveBalance = LeaveBalance::whereHas('leave', function ($query) use ($leaveTypeID) {
            $query->where('id', $leaveTypeID);
        })
            ->where('user_id', $userID)
            ->first();

        $leaveBalance->leaveDeduction($reduceAmount);
    }

    private function updateUserOffshoreBalance(User $user, int $newBalance)
    {
        $leaveBalance = LeaveBalance::where([
            'user_id' => $user->id,
            'leave_type_id' => 8,
        ])->first();
        $leaveBalance->addBalance($newBalance);

        $this->checkUserBalance(
            user: $user,
            data: [8, $newBalance, 0, $newBalance] // leave_type_id, balance, taken, total
        );
    }

    private function checkLeaveDeductionBalanceFlow(User $user, String $result, Array $expectedBalance)
    {
        // check OFFSHORE balance
        $this->checkUserBalance(
            user: $user,  //user join 2023-01-01
            data: [
                8, // Leave_type_id
                $expectedBalance['offshoreBalance'], // Balance
                $expectedBalance['offshoreTaken'], // Taken
                $expectedBalance['offshoreTotal'], // Total
            ]
        );

        // check ANNUAL balance
        $this->checkUserBalance(
            user: $user,  //user join 2023-01-01
            data: [
                1, // Leave_type_id
                $expectedBalance['annualBalance'], // Balance
                $expectedBalance['annualTaken'], // Taken
                $expectedBalance['annualTotal'], // Total
            ]
        );

        // check UNPAID balance
        $this->checkUserBalance(
            user: $user,  //user join 2023-01-01
            data: [
                4, // Leave_type_id
                $expectedBalance['unpaidBalance'], // Balance
                $expectedBalance['unpaidTaken'], // Taken
                $expectedBalance['unpaidTotal'], // Total
            ]
        );

        $this->assertDatabaseHas('leave_requests', ['deduct_type' => $result]);
    }

    private function simulateForceLeave(User $user, int $duration)
    {
        $deductionHistoryData = [
            'user_id' => $user->id,
            'duration' => $duration,
            'remark' => 'hari raya haji',
            'hr_startDate' => '2023-09-01',
            'hr_endDate' => '2023-09-01',
        ];

        return $this->actingAs(self::$userBalance->admin)->postJson('/api/administration/deduct-leave/' . self::$userBalance->admin->id, $deductionHistoryData);
    }
}
