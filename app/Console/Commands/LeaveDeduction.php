<?php

namespace App\Console\Commands;

use App\Models\LeaveBalance;
use App\Models\LeaveRequest;
use App\Services\LeaveDeductionService;
use Illuminate\Console\Command;

class LeaveDeduction extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'leave:deduction';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'CRON job for deducting approved leave request';

    /**
     * Execute the console command.
     *
     * @return int
     */

    public function __construct(LeaveDeductionService $leaveDeductionService)
    {
        parent::__construct();
        $this->LeaveDeductionService = $leaveDeductionService;
    }

    public function handle()
    {
        $this->LeaveDeductionService->annualLeaveDailyCalculateV2();

        $this->call('replacement:expired');
    }
}
