<?php

namespace Database\Seeders;

use App\Models\Allowance;
use App\Models\Expense;
use App\Models\Transport;
use App\Models\TravelClaim;
use App\Models\User;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class TravelClaimSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $notification = TravelClaim::getEventDispatcher();
        TravelClaim::unsetEventDispatcher();

        foreach (User::all() as $i)
        {
            $user = User::find($i->id);

            $claim = TravelClaim::factory()
                ->create([
                    'user_id' => $user->id,
                    'department_id' => $user->department_id
                ]);

            $travelFolder = storage_path('app/travel-claim-attachment/' . $claim->id);
            if(! file_exists($travelFolder)) {
                mkdir($travelFolder, 0755, true);
                mkdir($travelFolder . '/allowance', 0755, true);
                mkdir($travelFolder . '/transport', 0755, true);
                mkdir($travelFolder . '/expense', 0755, true);
            }

            Allowance::factory()->count(10)
                ->create([
                    'travel_id' => $claim->id,
                    'path' => fake()->file(
                        sourceDirectory: storage_path('fakeDocuments/'),
                        targetDirectory:
                        storage_path('app/travel-claim-attachment/'.$claim->id.'/allowance/'),
                        fullPath: false,
                    ),
                ]);

            Transport::factory()->count(10)
                ->create([
                    'travel_id' => $claim->id,
                    'path' => fake()->file(
                        sourceDirectory: storage_path('fakeDocuments/'),
                        targetDirectory:
                        storage_path('app/travel-claim-attachment/'.$claim->id.'/transport/'),
                        fullPath: false,
                    ),
                ]);

            Expense::factory()->count(10)
                ->create([
                    'travel_id' => $claim->id,
                    'path' => fake()->file(
                        sourceDirectory: storage_path('fakeDocuments/'),
                        targetDirectory:
                        storage_path('app/travel-claim-attachment/'.$claim->id.'/expense/'),
                        fullPath: false,
                    ),
                ]);

            $this->calculateTotal($claim);
        }

        TravelClaim::setEventDispatcher($notification);
    }

    private function calculateTotal($claim)
    {
        $allowance_total = DB::table('allowances')
            ->select('travel_id', DB::raw('SUM(amount) as total'))
            ->where('travel_id', $claim->id)
            ->groupBy('travel_id')
            ->get();

        $transport_total = DB::table('transports')
            ->select('travel_id', DB::raw('SUM(amount) as total'))
            ->where('travel_id', $claim->id)
            ->groupBy('travel_id')
            ->get();

        $expense_total = DB::table('expenses')
            ->select('travel_id', DB::raw('SUM(amount) as total'))
            ->where('travel_id', $claim->id)
            ->groupBy('travel_id')
            ->get();

        $claim->update([
            'total_allowance' => $allowance_total[0]->total,
            'total_transport' => $transport_total[0]->total,
            'total_expense' => $expense_total[0]->total,
            'isDraft' => 0,
        ]);
    }
}
