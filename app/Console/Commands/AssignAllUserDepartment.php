<?php

namespace App\Console\Commands;

use App\Models\Department;
use App\Models\User;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Log;
use LdapRecord\Models\ActiveDirectory\User as AD;

class AssignAllUserDepartment extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'user:department-reset';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'RE-ASSIGN ALL USER DEPARTMENT (FETCH DEPT FROM AD)';

    /**
     * Execute the console command.
     *
     * @return int
     */
    public function handle()
    {
        $users = User::all();

        foreach ($users as $user) {
            $ldap = AD::find($user->dn);

            if ($ldap)
            {
                $dept = Department::updateOrCreate(
                    ['name' => $ldap->getFirstAttribute('department')],
                );

                $user->update(
                    [
                        'department_id' => $dept->id,
                    ]
                );
            }
            else {
                $dept = Department::updateOrCreate(['name' => 'NO DEPARTMENT']);
                $user->update(['department_id' => $dept->id]);
            }
        }

        Log::info('Users Effected for department reset: ' . $users->count());
    }
}
