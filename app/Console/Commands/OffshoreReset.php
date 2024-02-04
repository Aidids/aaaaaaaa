<?php

namespace App\Console\Commands;

use App\Models\LeaveBalance;
use App\Models\LeaveType;
use App\Services\OffshoreConsoleService;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Log;

class OffshoreReset extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'offshore:reset';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Run at the end of each year (reset all user offshore balance)';

    /**
     * Execute the console command.
     *
     * @return int
     */

    public function __construct(OffshoreConsoleService $offshoreConsoleService)
    {
        parent::__construct();
        $this->offshoreConsoleService = $offshoreConsoleService;
    }

    public function handle()
    {
        $count = $this->offshoreConsoleService->resetOffshoreBalance();

        if ($count) {
            $this->info($count.' rows of offshore leave balance has been reset');
            Log::channel('lb-reset-log')->info($count.' rows of offshore leave balance has been reset');
        }
    }

}
