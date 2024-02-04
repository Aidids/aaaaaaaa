<?php

namespace App\Console\Commands;

use App\Services\OffshoreCreationService;
use Illuminate\Console\Command;

class OffshoreCreateConsole extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'offshore:create';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Offshore leave type creation and assignation (only run for first time)';

    /**
     * Execute the console command.
     *
     * @return int
     */

    public function __construct(OffshoreCreationService $offshoreCreationService)
    {
        parent::__construct();
        $this->OffshoreCreationService = $offshoreCreationService;
    }

    public function handle()
    {
        $status = $this->OffshoreCreationService->addOffshoreLeaveType();
        $status2 = $this->OffshoreCreationService->createUserOffshoreLeaveBalance();

        if ($status || ($status2 > 0) )
        {
            $this->info('Offshore leave type created');
            $this->info($status2.' Offshore leave balance record created');
        }
    }
}
