<?php

namespace App\Console\Commands;

use App\Services\RedeemReplacementLeaveConsoleService;
use Illuminate\Console\Command;

class ReplacementLeaveCreateConsole extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'replacement:create';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'console command to create replacement leave type (only run once)';

    /**
     * Execute the console command.
     *
     * @return int
     */

    public function __construct(RedeemReplacementLeaveConsoleService $replacementLeaveConsoleService)
    {
        parent::__construct();
        $this->replacementLeaveConsoleService = $replacementLeaveConsoleService;
    }

    public function handle()
    {
        $status = $this->replacementLeaveConsoleService->addReplacementLeaveType();
        $status2 = $this->replacementLeaveConsoleService->createReplacementLeaveBalance();

        if ($status || ($status2 > 0) ){
            $this->info('Replacement leave type created');
            $this->info($status2.' Replacement leave balance record created');
        }
    }
}
