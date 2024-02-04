<?php

namespace App\Console\Commands;

use App\Models\LeaveType;
use App\Models\User;
use App\Services\LeaveBalanceService;
use Illuminate\Console\Command;

class AssignAllLeave extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'leave:onboarding';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'ONBOARDING Leave for deployment (only use for first time)';

    /**
     * Execute the console command.
     *
     * @return int
     */
    public function handle()
    {
        $leaveBalanceService = new LeaveBalanceService();

        $leaveType = LeaveType::all();
        $users = User::all();

        foreach($leaveType as $type) {
            $leaveBalanceService->assignToAllUsers($type);
        }

        foreach($users as $user) {
            $leaveBalanceService->assignLeaveBalance($user);
        }



        $this->info('Everyone\'s leave updated');
    }
}
