<?php

namespace App\Console\Commands;

use App\Services\RedeemReplacementLeaveConsoleService;
use Illuminate\Console\Command;

class ExpiredReplacementLeave extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'replacement:expired';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'use for deducting expired user redeemed replacement leave';

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
        $this->replacementLeaveConsoleService->replacementLeaveExpired();
    }
}
