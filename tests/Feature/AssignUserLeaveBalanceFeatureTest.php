<?php

namespace Tests\Feature;

use App\Enums\LeaveRequestStatus;
use App\Models\LeaveBalance;
use App\Models\LeaveRequest;
use App\Models\User;
use Illuminate\Foundation\Testing\DatabaseTransactions;
use Illuminate\Support\Facades\Artisan;
use Tests\TestCase;

class AssignUserLeaveBalanceFeatureTest extends TestCase
{
    use DatabaseTransactions;

    protected static $db_migrated = false;

    // If database have not been migrated setUp will return error (need to re-factor all feature test file)
    // --> setUp need to be public to use MigrateFreshSeedOnce trait
    // If database have been migrated, setUp works fine and even faster 65%
    // Ex: Cut from 2.4s to 0.4sec

    protected static function dbMigrateOnce(): void
    {
        echo "\n---DB Migrated Once---\n";
        Artisan::call('migrate:fresh');
        Artisan::call(
            'db:seed', ['--class' => 'DatabaseSeeder']
        );
        Artisan::call('leave:onboarding');
        Artisan::call('offshore:create');
        Artisan::call('replacement:create');
        Artisan::call('compassionate:create');
        Artisan::call('create:out-of-office');
    }

    protected function setUp(): void
    {
        parent::setUp();

        if (!static::$db_migrated) {
            static::$db_migrated = true;
            static::dbMigrateOnce();
        }

        $this->setUser();
    }

    public function setUser()
    {
        $this->userNoJoinDate = User::where('joining_date', NULL)->first();
        $this->userMale1 = User::where('joining_date', '2010-01-01')->first();
        $this->userMale2 = User::where('joining_date', '2015-01-01')->first();
        $this->userMale3 = User::where('joining_date', '2020-01-01')->first();
        $this->userMale4 = User::where('joining_date', '2023-01-01')->first();

        // Used in LeaveDeductionFeatureTest
        $this->userFemale = User::where(['joining_date' => '2023-01-01','gender' => 'f'])->first();
        $this->approver1 = User::where('approver_id', 1)->first();
        $this->approver2 = User::where('approver_id', 2)->first();
        $this->admin = User::where('is_admin', true)->first();
    }

    public function test_check_user_annual_balance_based_on_joining_date()
    {
        // Simulate User No Joining Date
        $this->checkUserBalance(
            user: $this->userNoJoinDate,
            data: [1, 0, 0, 0] // leave_type_id, balance, taken, total
        );

        // Simulate User 1
        $this->checkUserBalance(
            user: $this->userMale1,
            data: [1, 20, 0, 20] // leave_type_id, balance, taken, total
        );

        // Simulate User 2
        $this->checkUserBalance(
            user: $this->userMale2,
            data: [1, 17, 0, 17] // leave_type_id, balance, taken, total
        );

        // Simulate User 3
        $this->checkUserBalance(
            user: $this->userMale3,
            data: [1, 14, 0, 14] // leave_type_id, balance, taken, total
        );

        // Simulate User 4
        $this->checkUserBalance(
            user: $this->userMale4,
            data: [1, 14, 0, 14] // leave_type_id, balance, taken, total
        );
    }

    public function test_check_user_medical_balance_based_on_joining_date()
    {
        // Simulate User No Joining Date
        $this->checkUserBalance(
            user: $this->userNoJoinDate,
            data: [2, 0, 0, 0] // leave_type_id, balance, taken, total
        );

        // Simulate User 1
        $this->checkUserBalance(
            user: $this->userMale1,
            data: [2, 22, 0, 22] // leave_type_id, balance, taken, total
        );

        // Simulate User 2
        $this->checkUserBalance(
            user: $this->userMale2,
            data: [2, 22, 0, 22] // leave_type_id, balance, taken, total
        );

        // Simulate User 3
        $this->checkUserBalance(
            user: $this->userMale3,
            data: [2, 18, 0, 18] // leave_type_id, balance, taken, total
        );

        // Simulate User 4
        $this->checkUserBalance(
            user: $this->userMale4,
            data: [2, 14, 0, 14] // leave_type_id, balance, taken, total
        );
    }

    public function test_check_user_other_leave_balance()
    {
        // Each of the leave type below has a fixed amount, because carry forward and entitlement is not enabled
        $leave_type_ids = [3, 4, 5, 8, 9, 10];
        // 3->HOSPITALIZATION || 4->UNPAID || 5->EMERGENCY || 8->OFFSHORE
        $leave_balances = [60, 365, 365, 0, 0, 0];
        // 60->HOSPITALIZATION || 365->UNPAID || 365->EMERGENCY || 0->OFFSHORE

        $users = [
            $this->userNoJoinDate,
            $this->userMale1, $this->userMale2,
            $this->userMale3, $this->userMale4,
        ];

        // loop through each user leave_balances of different leave_type
        foreach ($users as $user) {
            $index=0;
            foreach ($leave_type_ids as $leave_type_id) {
                $this->checkUserBalance(
                    user: $user,
                    data: [$leave_type_id, $leave_balances[$index], 0, $leave_balances[$index]]
                );
                $index++;
            }
        }
    }

    // for unknown reasons (this function cannot be used by another test file)
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
