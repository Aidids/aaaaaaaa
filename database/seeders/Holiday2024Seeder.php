<?php

namespace Database\Seeders;

use App\Models\Holiday;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class Holiday2024Seeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $holidays = [
            [
                'label' => 'Birthday of the Sultan of Selangor (Selangor)',
                'date' => '2023-12-11',
            ],
            [
                'label' => 'Christmas Day',
                'date' => '2023-12-25',
            ],
            [
                'label' => 'New Year\'s Day',
                'date' => '2024-01-01',
            ],
            [
                'label' => 'Thaipusam',
                'date' => '2024-01-25',
            ],
            [
                'label' => 'Federal Territory Day',
                'date' => '2024-02-01',
            ],
            [
                'label' => 'Chinese New Year',
                'date' => '2024-02-10',
            ],
            [
                'label' => 'Chinese New Year',
                'date' => '2024-02-11',
            ],
            [
                'label' => 'Chinese New Year',
                'date' => '2024-02-12',
            ],
            [
                'label' => 'Nuzul Al-Quran',
                'date' => '2024-03-28',
            ],
            [
                'label' => 'Hari Raya Aidilfitri',
                'date' => '2024-04-10',
            ],
            [
                'label' => 'Hari Raya Aidilfitri',
                'date' => '2024-04-11',
            ],
            [
                'label' => 'Labour Day',
                'date' => '2024-05-01',
            ],
            [
                'label' => 'Wesak Day',
                'date' => '2024-05-22',
            ],
            [
                'label' => 'Agong\'s Birthday',
                'date' => '2024-06-03',
            ],
            [
                'label' => 'Hari Raya Haji',
                'date' => '2024-06-17',
            ],
            [
                'label' => 'Awal Muharram',
                'date' => '2024-07-07',
            ],
            [
                'label' => 'Awal Muharram Holiday',
                'date' => '2024-07-08',
            ],
            [
                'label' => 'Merdeka Day',
                'date' => '2024-08-31',
            ],
            [
                'label' => 'Prophet Muhammad\'s Birthday | Malaysia Day',
                'date' => '2024-09-16',
            ],
            [
                'label' => 'Malaysia Day Holiday',
                'date' => '2024-09-17',
            ],
            [
                'label' => 'Deepavali',
                'date' => '2024-10-31',
            ],
            [
                'label' => 'Christmas Day',
                'date' => '2024-12-25',
            ],
        ];

        foreach ($holidays as $holiday)
        {
            Holiday::factory()->create($holiday);
        }
    }
}
