<?php

namespace App\Services;

use App\Enums\Status;
use App\Http\Controllers\Controller;
use App\Http\Traits\ApproverTrait;
use App\Models\TravelClaim;
use App\Models\User;
use Carbon\Carbon;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class TravelClaimService extends Controller
{
    use ApproverTrait;

    public function approveTravelClaim(Array $data)
    {
        $data['current_approver'] = Auth::id();
        $travel = TravelClaim::find($data['id']);

        if (Auth::user()->is_admin && $travel->current_approver === 0) {
            $approvers_id = json_decode($travel->approvers_id);
            $key = array_search(0, $approvers_id);
            $approvers_id[$key] = Auth::id();

            $travel->update([
                'approvers_id' => json_encode($approvers_id),
                'current_approver' => $data['current_approver'],
            ]);
        }

        // update approver remark
        $this->updateRemark($data, $travel);

        if ($data['status'] == Status::rejected->value) {
            $data['status'] = Status::rejected->value;
        }
        else {
            // Check if current approver is the last on the list
            // Set the next approver
            $lastApprover = $this->assignNextApprover(
                approversID: json_decode($travel->approvers_id),
                currentApproverID: $data['current_approver'],
            );
            (!$lastApprover) && $data['status'] = Status::processing->value;
        }

        $travel->update([
            'approvers_remark' => json_encode($data['approvers_remark']),
            'current_approver' => $data['current_approver'],
            'status' => $data['status'],
        ]);

        return $travel;
    }

    private function updateRemark(&$data, TravelClaim $travelClaim): void
    {
        $key = array_search($data['current_approver'], json_decode($travelClaim->approvers_id));
        $approvers_remark = json_decode($travelClaim->approvers_remark);
        $approvers_remark[$key] = $data['remark'];

        $data['approvers_remark'] = $approvers_remark;
    }

    public function travelClaimSummary(Array $data)
    {
        $startDate = null; $department_id = null; $query = null;
        $current_approver = [0,179];
        $status = [Status::approved->value, Status::processing->value];

        array_key_exists('query', $data) && $query = $data['query'];
        array_key_exists('month', $data) && $startDate = Carbon::createFromFormat('Y-m-d', $data['month']);
        array_key_exists('department_id', $data) && $department_id = $data['department_id'];

        if (array_key_exists('status', $data))
        {
            $json = json_decode($data['status'], true);
            $current_approver = $json['current_approver'];
            $status = $json['status'];
        }

        return TravelClaim::with(['user', 'department'])
            ->with(['allowances' => function ($query) {
                $query->select(
                    'travel_id',
                    DB::raw('
                        CASE
                            WHEN allowance_type LIKE "Offshore%" THEN "Offshore"
                            WHEN allowance_type LIKE "Oversea%" THEN "Oversea"
                            ELSE allowance_type
                        END AS type'),
                    DB::raw('SUM(amount) as total_amount')
                )
                    ->groupBy('travel_id', 'type')
                    ->get();
            }])
            ->with(['expenses' => function ($query) {
                $query->select(
                    'travel_id',
                    DB::raw('
                        CASE
                            WHEN description LIKE "Parking%"
                                 OR description LIKE "Toll%"
                                 OR description LIKE "Public Transport%"
                                 OR description LIKE "Fuel%"
                            THEN "Transport"

                            WHEN description LIKE "Meal Allowance%"
                                 OR description LIKE "Refreshment%"
                                 OR description LIKE "Telephone%"
                                 OR description LIKE "Laundry%"
                            THEN "Accommodation"

                            WHEN description LIKE "Others%" THEN "Others"
                        ELSE description
                        END AS type'),
                    DB::raw('SUM(amount) as total_amount')
                )
                    ->groupBy('travel_id', 'type')
                    ->get();
            }])
            ->whereHas('user', function ($q) use ($query) {
                $q->where('name', 'LIKE', '%'. $query .'%');
            })
            ->when($startDate, function ($q) use ($startDate) {
                $q->where('submission_month', '>=', $startDate->toDateString())
                    ->where('submission_month', '<=', $startDate->copy()->endOfMonth()->toDateString());
            })
            ->when($department_id, function ($q) use ($department_id) {
                $q->where('department_id', $department_id);
            })
            ->whereIn('current_approver', $current_approver)
            ->whereIn('status', $status)
            ->orderBy('submission_month', 'desc')
            ->paginate(10);
    }
}
