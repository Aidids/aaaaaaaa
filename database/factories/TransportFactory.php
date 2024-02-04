<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Transport>
 */
class TransportFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition()
    {
        $to = [
            0 => [
                'value' => 'CITY BANK (TALISMAN)',
                'label' => 'CITY BANK (TALISMAN)',
                'distance' => 15
            ],
            1 => [
                'value' => 'SAPURA',
                'label' => 'SAPURA',
                'distance' => 18
            ],
            2 => [
                'value' => 'KLCC-PETRONAS',
                'label' => 'KLCC-PETRONAS',
                'distance' => 15
            ],
            3 => [
                'value' => 'CHUKAI KEMAMAN',
                'label' => 'CHUKAI KEMAMAN',
                'distance' => 310
            ],
            4 => [
                'value' => 'KSB KEMAMAN/TELUK KALONG',
                'label' => 'KSB KEMAMAN/TELUK KALONG',
                'distance' => 320
            ],
            5 => [
                'value' => 'KOP KERTEH',
                'label' => 'KOP KERTEH',
                'distance' => 350
            ],
            6 => [
                'value' => 'TCOT KERTEH',
                'label' => 'TCOT KERTEH',
                'distance' => 350
            ],
            7 => [
                'value' => 'NIOSH BANGI',
                'label' => 'NIOSH BANGI',
                'distance' => 35
            ],
            8 => [
                'value' => 'SEQU KAYU ARA',
                'label' => 'SEQU KAYU ARA',
                'distance' => 16
            ],
            9 => [
                'value' => 'PUTRAJAYA DOSH',
                'label' => 'PUTRAJAYA DOSH',
                'distance' => 35
            ],
            10 => [
                'value' => 'AIRPORT KLIA',
                'label' => 'AIRPORT KLIA',
                'distance' => 62
            ],
            11 => [
                'value' => 'AIRPORT KLIA2',
                'label' => 'AIRPORT KLIA2',
                'distance' => 62
            ],
            12 => [
                'value' => 'AIRPORT SUBANG',
                'label' => 'AIRPORT SUBANG',
                'distance' => 20
            ],
            13 => [
                'value' => 'AIRPORT KERTEH',
                'label' => 'AIRPORT KERTEH',
                'distance' => 360
            ],
            14 => [
                'value' => 'PCSB PMO',
                'label' => 'PCSB PMO',
                'distance' => 335
            ],
            15 => [
                'value' => 'DUNGUN',
                'label' => 'DUNGUN',
                'distance' => 70
            ],
            16 => [
                'value' => 'TOWER 3',
                'label' => 'TOWER 3',
                'distance' => 16
            ],
            17 => [
                'value' => 'KL SENTRAL',
                'label' => 'KL SENTRAL',
                'distance' => 13
            ]
        ];

//        rate =  distance * 0.6
        $startDate = fake()->dateTimeBetween('-1 week', 'now');
        $randomize = fake()->randomElement($to);

        $transport_type = [
            'mileage' => [
                'transport_type' => 'Mileage',
                'date' => $startDate,
                'start_location' => 'KL OFFICE(VSQ)',
                'end_location' => $randomize['value'],
                'total_distance' => $randomize['distance'],
                'amount' => $randomize['distance'] * 0.6,
                'remark' => 'mileage: ' . fake()->bs(),
            ],

            'parking' => [
                'transport_type' => 'Parking',
                'amount' => fake()->randomFloat(2, 50, 1000),
                'remark' => 'parking: ' . fake()->bs(),
            ],

            'toll' => [
                'transport_type' => 'Toll',
                'amount' => fake()->randomFloat(2, 50, 1000),
                'remark' => 'toll: ' . fake()->bs(),
            ],

            'publicTransport' => [
                'transport_type' => 'Public Transport',
                'amount' => fake()->randomFloat(2, 50, 1000),
                'remark' => 'public transport: ' . fake()->bs(),
            ],

            'fuel' => [
                'transport_type' => 'Fuel',
                'amount' => fake()->randomFloat(2, 50, 1000),
                'remark' => 'fuel: ' . fake()->bs(),
            ]
        ];

        return fake()->randomElement($transport_type);
    }
}
