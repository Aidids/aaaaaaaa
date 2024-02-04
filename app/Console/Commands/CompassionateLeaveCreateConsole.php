<?php

namespace App\Console\Commands;

use App\Services\CompassionateLeaveConsoleService;
use Illuminate\Console\Command;

class CompassionateLeaveCreateConsole extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'compassionate:create';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'console command to create compassionate leave type (only run once)';

    /**
     * Execute the console command.
     *
     * @return int
     */

    public function __construct(CompassionateLeaveConsoleService $compassionateLeaveConsoleService)
    {
        parent::__construct();
        $this->compassionateLeaveConsoleService = $compassionateLeaveConsoleService;
    }

    public function handle()
    {
        $status = $this->compassionateLeaveConsoleService->addCompassionateLeaveType();
        $status2 = $this->compassionateLeaveConsoleService->createCompassionateLeaveBalance();

        if ($status || ($status2 > 0) ){
            $this->info('Compassionate leave type created');
            $this->info($status2.' Compassionate leave balance record created');
        }
    }


}
