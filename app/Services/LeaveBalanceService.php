<?php

namespace App\Services;

use App\Models\LeaveBalance;
use App\Models\LeaveEntitlement;
use App\Models\LeaveType;
use App\Models\User;
use Carbon\Carbon;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\DB;


class LeaveBalanceService
{
    public function __construct()
    {
        $this->now = Carbon::now()->timezone('Asia/Kuala_Lumpur');
    }

    /**
     *  Core query for assigning leave balance for all staff
     *  Used in:
     *  LoginAction.php (always run this when user login)
     *  Why?
     *      #1 for new staff when joining dayang, it will create all leavetype when login
     *      #2 easier assignment if new leave type is implemented (no need console for new leave type)
     *  ProfileApi.php
     *  Console - leave:onboarding
     */
    public function assignLeaveBalance(User $user): void
    {
        $leaveType = LeaveType::where('gender', '=', $user->gender)
            ->orWhereNull('gender')
            ->get();

        foreach ($leaveType as $type)
        {
            LeaveBalance::firstOrCreate(
                [   'leave_type_id' => $type->id,
                    'user_id' => $user->id,
                ],
                [
                    'user_id' => $user->id,
                    'leave_type_id' => $type->id,
                    'proRated' => $this->calcProRatedLeave(leaveType: $type, user: $user),
                    'entitlement' => $this->calcEntitlement(leaveType: $type, user: $user),
                    'balance' => $this->calcProRatedLeave(leaveType: $type, user: $user)
                        + $this->calcEntitlement(leaveType: $type, user: $user),
                    'total' => $this->calcProRatedLeave(leaveType: $type, user: $user)
                        + $this->calcEntitlement(leaveType: $type, user: $user)
                ]
            );
        }
    }

    public function assignToAllUsers(LeaveType $leaveType): void
    {
        foreach (User::all() as $user)
        {
            if (is_null($leaveType->gender)) {
                LeaveBalance::create(
                    [
                        'user_id' => $user->id,
                        'leave_type_id' => $leaveType->id,
                        'proRated' => $this->calcProRatedLeave(leaveType: $leaveType, user: $user),
                        'entitlement' => $this->calcEntitlement(leaveType: $leaveType, user: $user),
                        'balance' => $this->calcProRatedLeave(leaveType: $leaveType, user: $user)
                            + $this->calcEntitlement(leaveType: $leaveType, user: $user),
                        'total' => $this->calcProRatedLeave(leaveType: $leaveType, user: $user)
                            + $this->calcEntitlement(leaveType: $leaveType, user: $user)
                    ]
                );
            }

        }
    }

    // Update user Paternity/Maternity Leave only
    public function updateLeaveBalance(LeaveType $leaveType): void
    {
        foreach (User::all() as $user)
        {
            if ($user->gender == $leaveType->gender) {
                LeaveBalance::updateOrCreate(
                    [   'leave_type_id' => $leaveType->id,
                        'user_id' => $user->id,
                    ],
                    [
                        'proRated' => $this->calcProRatedLeave(leaveType: $leaveType, user: $user),
                        'entitlement' => $this->calcEntitlement(leaveType: $leaveType, user: $user),
                        'carry_forward' => $this->calcCarryForward(leaveType: $leaveType, user: $user),
                        'balance' => $this->calcProRatedLeave(leaveType: $leaveType, user: $user)
                            + $this->calcEntitlement(leaveType: $leaveType, user: $user)
                            + $this->calcCarryForward(leaveType: $leaveType, user: $user)
                            - $user->taken,
                        'total' => $this->calcProRatedLeave(leaveType: $leaveType, user: $user)
                            + $this->calcEntitlement(leaveType: $leaveType, user: $user)
                            + $this->calcCarryForward(leaveType: $leaveType, user: $user),
                    ]
                );
            }
        }
    }

    /**
     * DAYANG PRO RATED FORMULA
     *
     *        Annual Leave
     *       -------------- x Employee's months of service
     *         12 months
     */
    private function calcProRatedLeave(LeaveType $leaveType, User $user)
    {
        if ($leaveType->pro_rated)
        {
            $result = ($leaveType->amount) / (12) * $this->calcMonthsOfService($user);
            return floor($result);
        }

        return $leaveType->amount;
    }

    /**
     * DAYANG EMPLOYEE MONTHS OF SERVICE CUT OFF DATE
     *
     * If employee join before 14th (any month), employee will count that month as part of its service
     * Eg:
     * Ayman join DAYANG at 15th January, his service will only be 11 months
     */
    public function calcMonthsOfService(User $user): int
    {
        if (is_null($user->joining_date)) {
            return 0;
        }

        $joinDate = Carbon::parse($user->joining_date)->timezone('Asia/Kuala_Lumpur');

        if ( $joinDate->year < $this->now->year) {
            return 12;
        }

        if ( $joinDate->day <= 14 ) {
            return 13 - $joinDate->month;
        }

        return 12 - $joinDate->month;
    }

    /**
     * DAYANG LEAVE ENTITLEMENT
     *
     * Leave Entitlement is hard coded (LeaveType Table) and changes to it is rare
     */
    public function calcEntitlement(LeaveType $leaveType, User $user): int
    {
        if (is_null($user->joining_date)) {
            /**
             * Default leave balance count to 0 if joining date is not set
             */
            return 0;
        }

        $joinDate = Carbon::parse($user->joining_date)->timezone('Asia/Kuala_Lumpur');
        $period = $joinDate->diffInYears($this->now);

        $entitlements = $leaveType->entitlement;

        foreach ($entitlements as $entitlement)
        {
            if ($this->checkDate(model: $entitlement,period: $period)) {
                return $entitlement->amount;
            }
        }

        return 0;
    }

    /**
     * DAYANG LEAVE CARRY FORWARD
     *
     * CARRY FORWARD is configured by HR and changes to it is rare
     */
    public function calcCarryForward(LeaveType $leaveType, User $user): int
    {
        if (is_null($user->joining_date)) {
            /**
             * Default leave balance count to 0 if joining date is not set
             */
            return 0;
        }

        $joinDate = Carbon::parse($user->joining_date)->timezone('Asia/Kuala_Lumpur');
        $period = $joinDate->diffInYears($this->now);

        foreach ($leaveType->carryForward as $carryForward)
        {
            if ($this->checkDate(model: $carryForward, period: $period)) {
                return $carryForward->amount;
            }
        }

        return 0;
    }

    /**
     * Refer: UserFeatureTest.php
     * in: test_get_leave_type_when_admin_change_profile_joining_date
     * line: 23
     */
    public function calcLeaveBalance(User $user): void
    {
        $leaveBalances = LeaveBalance::where('user_id', $user->id)
            ->get();

        foreach ($leaveBalances as $balance)
        {
            if(in_array($balance->leave_type_id, [8, 9, 10, 11])) {
                continue;
            }
            else if ($balance->taken == 0.0 && !is_null($user->joining_date))
            {
                LeaveBalance::find($balance->id)->update([
                    'proRated' => $this->calcProRatedLeave(leaveType: $balance->leave, user: $user),
                    'entitlement' => $this->calcEntitlement(leaveType: $balance->leave, user: $user),
                    'balance' => $this->calcProRatedLeave(leaveType: $balance->leave, user: $user)
                        + $this->calcEntitlement(leaveType: $balance->leave, user: $user),
                    'total' => $this->calcProRatedLeave(leaveType: $balance->leave, user: $user)
                        + $this->calcEntitlement(leaveType: $balance->leave, user: $user)
                ]);
            }
        }
    }

    public function resetLeaveBalance(User $user): void
    {
        DB::transaction(function() use ($user) {
            $leaveType = LeaveType::where('gender', '=', $user->gender)
                ->orWhereNull('gender')
                ->get();

            foreach ($leaveType as $type)
            {
                if($type->id === 1) {
                    $this->updateLeaveCarryForward(user: $user, leaveType: $type);
                }
                else {
                    LeaveBalance::updateOrCreate(
                        [
                            'leave_type_id' => $type->id,
                            'user_id' => $user->id,
                        ],
                        [
                            'user_id' => $user->id,
                            'leave_type_id' => $type->id,
                            'proRated' => $this->calcProRatedLeave(leaveType: $type, user: $user),
                            'entitlement' => $this->calcEntitlement(leaveType: $type, user: $user),
                            'balance' => $this->calcProRatedLeave(leaveType: $type, user: $user)
                                + $this->calcEntitlement(leaveType: $type, user: $user),
                            'total' => $this->calcProRatedLeave(leaveType: $type, user: $user)
                                + $this->calcEntitlement(leaveType: $type, user: $user),
                            'taken' => 0,
                        ]
                    );
                }
            }
        });
    }

    private function updateLeaveCarryForward(User $user, Leavetype $leaveType)
    {
        $leaveBalance = LeaveBalance::where([
            'user_id' => $user->id,
            'leave_type_id' => $leaveType->id,
        ])->first();

        $max_cf = $this->calcCarryForward($leaveType, $user);

        // If balance is equal or more than allowed CF value; take max value
        // else take balance left
        $carry_forward_balance = ($max_cf <= $leaveBalance->balance)
            ? $max_cf
            : $leaveBalance->balance;

        $proRated = $this->calcProRatedLeave($leaveType, $user);
        $entitlement = $this->calcEntitlement(leaveType: $leaveType, user: $user);
        $total = $balance = ($proRated + $entitlement + $carry_forward_balance);

        $leaveBalance->update([
            'proRated' => $proRated,
            'entitlement' => $entitlement,
            'carry_forward' => $carry_forward_balance,
            'balance' => $balance,
            'total' => $total,
            'taken' => 0,
        ]);
    }

    private function checkDate(Model $model, int $period): bool
    {
        if ($period >= $model->start_period)
        {
            if ($period <= $model->end_period)
            {
                return true;
            }
        }

        return false;
    }
}
