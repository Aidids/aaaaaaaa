<?php

namespace App\Console\Commands;

use App\Services\OutOfOfficeConsoleService;
use Illuminate\Console\Command;

class OutOfOfficeLeaveCreate extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'create:out-of-office';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Create Leave Type (Out Of Office)';

    /**
     * Execute the console command.
     *
     * @return int
     */

    public function __construct(OutOfOfficeConsoleService $outOfOfficeConsoleService)
    {
        parent::__construct();
        $this->outOfficeConsoleService = $outOfOfficeConsoleService;
    }

    public function handle()
    {
        // create out of office leave type
        $this->outOfficeConsoleService->createOutOfOfficeLeaveType();

        // create new record of leave balance for out of office leave type
        $status = $this->outOfficeConsoleService->createOutOfOfficeLeaveBalance();

        if ($status > 0) {
            $this->info('Out Of Office leave type created/existed');
            $this->info($status.' Out Of Office leave balance record created');
        }
    }
}
