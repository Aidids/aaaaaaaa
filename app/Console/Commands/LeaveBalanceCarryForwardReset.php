<?php

namespace App\Console\Commands;

use App\Models\LeaveBalance;
use App\Models\User;
use App\Services\LeaveBalanceService;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Log;

class LeaveBalanceCarryForwardReset extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'leave-balance:new-year-reset';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Reset all user leave balance, and calculate carry forward (Every new year)';

    /**
     * Execute the console command.
     *
     * @return int
     */
    public function handle(): void
    {
        $this->call('offshore:reset');

        $leaveBalanceService = new LeaveBalanceService();

        // Logs Start Timer
        $start_timer = microtime(true);
        $this->info('Leave Balance New Year Reset Start');
        Log::channel('lb-reset-log')->info('Leave Balance New Year Reset Start');

        $users = User::all();
        foreach ($users as $user) {
            $leaveBalanceService->resetLeaveBalance($user);
        }

        // Logs End Timer
        $elapsed_time = number_format( (microtime(true) - $start_timer), 2);
        $this->info('Leave Balance New Year Reset End: ' . $elapsed_time);
        Log::channel('lb-reset-log')->info('Leave Balance New Year Reset Ends: ' . $elapsed_time . ' Seconds');

        // Logs Affected User
        $this->info('Users Affected by Leave Balance New Year Reset: ' . $users->count());
        Log::channel('lb-reset-log')
            ->info('Users Affected by Leave Balance New Year Reset: ' . $users->count());
    }
}
