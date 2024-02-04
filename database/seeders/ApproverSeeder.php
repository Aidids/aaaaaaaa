<?php

namespace Database\Seeders;

use App\Models\Approver;
use App\Models\User;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class ApproverSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        Approver::create(['name' => 'Approver Level One']);

        Approver::create(['name' => 'Approver Level Two']);
    }
}
