<?php

namespace Database\Seeders;

use App\Models\LeaveCarryForward;
use App\Models\LeaveEntitlement;
use App\Models\LeaveType;
use Database\Factories\LeaveTypeFactory;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class LeaveTypeSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {

        /**
         * Annual Leave
         */
        $annualLeave =  LeaveType::factory()->create([
            'name' => 'Annual Leave',
            'amount' => 14,
            'description' => 'Dayang\'s Kuala Lumpur headquarters annual leave type',
            'pro_rated' => true,
        ]);

        LeaveEntitlement::factory()->create([
            'leave_type_id' => $annualLeave->id,
            'start_period' => 0,
            'end_period' => 4,
            'amount' => 0,
        ]);

        LeaveEntitlement::factory()->create([
            'leave_type_id' => $annualLeave->id,
            'start_period' => 5,
            'end_period' => 7,
            'amount' => 2,
        ]);

        LeaveEntitlement::factory()->create([
            'leave_type_id' => $annualLeave->id,
            'start_period' => 8,
            'end_period' => 9,
            'amount' => 3,
        ]);

        LeaveEntitlement::factory()->create([
            'leave_type_id' => $annualLeave->id,
            'start_period' => 10,
            'end_period' => 100,
            'amount' => 6,
        ]);

        LeaveCarryForward::factory()->create([
            'leave_type_id' => $annualLeave->id,
            'start_period' => 1,
            'end_period' => 4,
            'amount' => 5,
        ]);

        LeaveCarryForward::factory()->create([
            'leave_type_id' => $annualLeave->id,
            'start_period' => 5,
            'end_period' => 7,
            'amount' => 5,
        ]);

        LeaveCarryForward::factory()->create([
            'leave_type_id' => $annualLeave->id,
            'start_period' => 8,
            'end_period' => 9,
            'amount' => 6,
        ]);

        LeaveCarryForward::factory()->create([
            'leave_type_id' => $annualLeave->id,
            'start_period' => 10,
            'end_period' => 100,
            'amount' => 7,
        ]);

        /**
         * Annual Leave
        */

        $medicalLeave = LeaveType::factory()->create([
            'name' => 'Medical Leave',
            'amount' => 0,
            'description' => 'Dayang\'s Kuala Lumpur headquarters unpaid leave type'
        ]);

        LeaveEntitlement::factory()->create([
            'leave_type_id' => $medicalLeave->id,
            'start_period' => 0,
            'end_period' => 2,
            'amount' => 14,
        ]);

        LeaveEntitlement::factory()->create([
            'leave_type_id' => $medicalLeave->id,
            'start_period' => 2,
            'end_period' => 5,
            'amount' => 18,
        ]);

        LeaveEntitlement::factory()->create([
            'leave_type_id' => $medicalLeave->id,
            'start_period' => 5,
            'end_period' => 100,
            'amount' => 22,
        ]);

        LeaveType::factory()->create([
            'name' => 'Hospitalisation Leave',
            'amount' => 60,
            'description' => 'Dayang\'s Kuala Lumpur headquarters hospitalisation leave type'
        ]);

        LeaveType::factory()->create([
            'name' => 'Unpaid Leave',
            'amount' => 365,
            'description' => 'Dayang\'s Kuala Lumpur headquarters emergency leave type'
        ]);

        LeaveType::factory()->create([
            'name' => 'Emergency Leave',
            'amount' => 365,
            'description' => 'Dayang\'s Kuala Lumpur headquarters emergency leave type'
        ]);

        LeaveType::factory()->create([
            'name' => 'Paternity Leave',
            'gender' => 'm',
            'amount' => 7,
            'description' => 'Dayang\'s Kuala Lumpur headquarters medical leave type'
        ]);

        LeaveType::factory()->create([
            'name' => 'Maternity Leave',
            'gender' => 'f',
            'amount' => 98,
            'description' => 'Dayang\'s Kuala Lumpur headquarters medical leave type'
        ]);

//        LeaveType::factory()->create([
//            'name' => 'Quarantine Leave',
//            'amount' => 365,
//            'description' => 'Dayang\'s Kuala Lumpur headquarters quarantine leave type (Ex: covid, influenza)'
//        ]);
    }
}
