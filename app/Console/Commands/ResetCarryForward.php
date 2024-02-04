<?php

namespace App\Console\Commands;

use App\Models\LeaveBalance;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Log;

class ResetCarryForward extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'carry-forward:reset';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Reset all user carry forward to 0 (this is only for cleaning data purpose)';

    public function handle(): void
    {
        $balances = LeaveBalance::where('carry_forward', '>' ,0)->get();

        Log::channel('command-log')->info('######################################');
        Log::channel('command-log')->info('Carry Forward Reset Start.');
        Log::channel('command-log')->info('Affected User: ' . count($balances));

        foreach ($balances as $userAnnual) {
            Log::channel('command-log')->info('Leave Balance ID: ' . $userAnnual->id);
            Log::channel('command-log')
                ->info('Before deduct' .
                    ', Carry Forward: ' . $userAnnual->carry_forward .
                    ', Balance: ' . $userAnnual->balance);

            if ($userAnnual->balance <= $userAnnual->carry_forward) {
                // if balance less than carry forward, reset to 0
                $afterBalance = 0;
            }
            else {
                // if balance more than carry forward, only deduct carry forward value from balance.
                $afterBalance = ($userAnnual->balance - $userAnnual->carry_forward);
            }

            $userAnnual->update([
                'carry_forward' => 0,
                'balance' => $afterBalance,
            ]);

            Log::channel('command-log')
                ->info('After deduct, ' .
                    'Carry Forward: ' . 0 .
                    ' Balance: ' . $afterBalance);
            Log::channel('command-log')->info('');
        }

        Log::channel('command-log')->info('Carry Forward Reset End.');
        Log::channel('command-log')->info('######################################');

        $this->info('Carry forward reset flow done, Please check log for more details.');
    }
}
