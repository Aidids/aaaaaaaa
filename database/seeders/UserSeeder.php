<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class UserSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        // User without joining date
        User::factory()->create([
            'gender' => 'm'
        ]);

        // User with joining date
        User::factory()->create([
            'joining_date' => '2010-01-01',
            'gender' => 'm'
        ]);

        User::factory()->create([
            'joining_date' => '2015-01-01',
            'gender' => 'm'
        ]);

        User::factory()->create([
            'joining_date' => '2020-01-01',
            'gender' => 'm'
        ]);

        User::factory()->create([
            'joining_date' => '2023-01-01',
            'gender' => 'm'
        ]);

        User::factory()->create([
            'joining_date' => '2023-01-01',
            'gender' => 'f'
        ]);

        // Approver
        User::factory()->create([
            'approver_id' => 1,
            'gender' => 'm'
        ]);

        User::factory()->create([
            'approver_id' => 2,
            'gender' => 'm'
        ]);

        User::factory()->create([
            'is_admin' => true,
            'gender' => 'm'
        ]);
    }
}
