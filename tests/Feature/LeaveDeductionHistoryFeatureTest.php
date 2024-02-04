<?php

namespace Tests\Feature;

use App\Models\LeaveDeductionHistory;
use App\Models\LeaveRequest;
use App\Models\User;
use Illuminate\Foundation\Testing\DatabaseTransactions;
use Tests\TestCase;

use Tests\Feature\AssignUserLeaveBalanceFeatureTest;
use Tests\Feature\LeaveDeductionFeatureTest;

class LeaveDeductionHistoryFeatureTest extends TestCase
{
    use DatabaseTransactions;

    private static $userBalance, $leaveDeduction;

    public static function setUpBeforeClass(): void
    {
        self::$userBalance = new AssignUserLeaveBalanceFeatureTest();
        self::$leaveDeduction = new LeaveDeductionFeatureTest();
    }

    public function setUp(): void
    {
        parent::setUp();
        LeaveDeductionHistory::unsetEventDispatcher();

        self::$userBalance->setUser();
        $this->admin = self::$userBalance->admin;
    }

    public function test_user_unauthorized_leave_deduction_index_page()
    {
        $response = $this->actingAs(self::$userBalance->userMale4)->get('administration/deduct-leave');
        $response->assertUnauthorized();
    }

    public function test_leave_deduction_index_page_able_to_get_all_history()
    {
        $leaveDeductionHistory = LeaveDeductionHistory::factory()->count(5)->create();

        $response = $this->actingAs($this->admin)->getJson('/api/administration/deduct-leave/');
        $response->assertJsonFragment([
            'id' => $leaveDeductionHistory[0]->id,
            'remark' => $leaveDeductionHistory[0]->remark,
            'duration' => $leaveDeductionHistory[0]->duration,
            'hr_ic_date' => $leaveDeductionHistory[0]->hr_ic_date,
            ]);
    }

    public function test_admin_deduct_1_user_only()
    {
        $deductData = [
            'duration' => 1,
            'remark' => 'hari raya haji',
            'hr_startDate' => '2023-09-01',
            'hr_endDate' => '2023-09-01',
            'user_id' => self::$userBalance->userMale4->id,
        ];

        $response = $this->actingAs($this->admin)
            ->postJson('/api/administration/deduct-leave/' . $this->admin->id, $deductData);
        $response->assertJsonFragment(['message' => 'Leave Deduction History Success.']);

        // Check if data exist on leave_request table
        $this->assertDatabaseHas('leave_requests', [
            'user_id' => self::$userBalance->userMale4->id,
            'reason' => 'Applied by HR: hari raya haji',
        ]);
        // Check if data exist on leave_deduction_histories table
        $this->assertDatabaseHas('leave_deduction_histories', [
            'user_id' => self::$userBalance->userMale4->id,
            'remark' => 'hari raya haji',
        ]);

        $this->checkBalanceForceLeave(
            user: self::$userBalance->userMale4,
            result: 'annual',
            expectedBalance: [
                'offshoreBalance' => 0, 'offshoreTaken' => 0, 'offshoreTotal' => 0,
                'annualBalance' => 13, 'annualTaken' => 1, 'annualTotal' => 14,
                'unpaidBalance' => 365, 'unpaidTaken' => 0, 'unpaidTotal' => 365,
            ],
        );
    }

    public function test_admin_deduct_all_user()
    {
        $deductData = [
            'duration' => 2,
            'remark' => 'hari raya haji',
            'hr_startDate' => '2023-09-01',
            'hr_endDate' => '2023-09-02',
            'deduct_all' => true,
        ];

        $response = $this->actingAs($this->admin)
            ->postJson('/api/administration/deduct-leave/' . $this->admin->id, $deductData);
        $response->assertJsonFragment(['message' => 'Leave Deduction History Success.']);

        if ( count(LeaveDeductionHistory::all()) === count(User::all()) ) {
            // Check if data exist on leave_request table
            $this->assertDatabaseHas('leave_requests', [
                'user_id' => self::$userBalance->userMale1->id,
                'reason' => 'Applied by HR: hari raya haji',
            ]);
            // Check if data exist on leave_deduction_histories table
            $this->assertDatabaseHas('leave_deduction_histories', [
                'user_id' => self::$userBalance->userMale1->id,
                'remark' => 'hari raya haji',
            ]);

            // Check if data exist on leave_request table
            $this->assertDatabaseHas('leave_requests', [
                'user_id' => self::$userBalance->userMale2->id,
                'reason' => 'Applied by HR: hari raya haji',
            ]);
            // Check if data exist on leave_deduction_histories table
            $this->assertDatabaseHas('leave_deduction_histories', [
                'user_id' => self::$userBalance->userMale2->id,
                'remark' => 'hari raya haji',
            ]);

            $this->checkBalanceForceLeave(
                user: self::$userBalance->userMale4,
                result: 'annual',
                expectedBalance: [
                    'offshoreBalance' => 0, 'offshoreTaken' => 0, 'offshoreTotal' => 0,
                    'annualBalance' => 12, 'annualTaken' => 2, 'annualTotal' => 14,
                    'unpaidBalance' => 365, 'unpaidTaken' => 0, 'unpaidTotal' => 365,
                ],
            );

            $this->checkBalanceForceLeave(
                user: self::$userBalance->userNoJoinDate,
                result: 'annual',
                expectedBalance: [
                    'offshoreBalance' => 0, 'offshoreTaken' => 0, 'offshoreTotal' => 0,
                    'annualBalance' => 0, 'annualTaken' => 0, 'annualTotal' => 0,
                    'unpaidBalance' => 363, 'unpaidTaken' => 2, 'unpaidTotal' => 365,
                ],
            );
        }
    }

    private function checkBalanceForceLeave(User $user, String $result, Array $expectedBalance)
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

    private function checkUserBalance(User $user, Array $data): void
    {
        $response = $this->actingAs($user)->getJson('/api/leave-balance/'.$user->id);

        $response->assertJsonFragment([
            'leave_type_id' => $data[0], // expected leave_type_id
            'balance' => $data[1], // expected balance
            'taken'=> $data[2], // expected taken
            'total' => $data[3], // expected total
        ]);
    }
}
