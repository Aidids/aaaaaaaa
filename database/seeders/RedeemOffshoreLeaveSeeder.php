<?php

namespace Database\Seeders;

use App\Enums\Status;
use App\Models\RedeemOffshoreLeave;
use App\Models\User;
use Carbon\Carbon;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class RedeemOffshoreLeaveSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $notification = RedeemOffshoreLeave::getEventDispatcher();
        RedeemOffshoreLeave::unsetEventDispatcher();

        $approvers_id = [180, 183];
        $status = ['approved', 'rejected'];

        $start_date = fake()->dateTimeBetween('-11 month', '+1 month');
        $end_date = Carbon::parse($start_date)->copy()->endOfMonth()->toDateString();

        foreach (User::whereNotNull('joining_date')->get() as $user) {
            $selected_status = $status[rand(0,1)];

            RedeemOffshoreLeave::factory()
                ->create([
                    'user_id' => $user->id,
                    'start_date' => $start_date,
                    'end_date' => $end_date,
                    'balance_received' => fake()->randomDigitNot(0),

                    'first_approver_id' => $approvers_id[rand(0,1)],
                    'first_approver_status' => $selected_status,
                    'first_approver_remark' => fake()->bs(),
                    'first_approver_date' => fake()->date(),

                    'overall_status' => $selected_status,
                ]);
        }

        RedeemOffshoreLeave::setEventDispatcher($notification);
    }
}
