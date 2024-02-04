<?php

namespace App\Api\Controller;

use App\Enums\LeaveRequestStatus;
use App\Http\Controllers\Controller;
use App\Http\Resources\DepartmentResource;
use App\Models\Department;
use App\Models\User;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;


class DepartmentApi extends Controller
{
    public function index()
    {
        return DepartmentResource::collection(Department::all());
    }

    public function calendar(Request $request)
    {
        $params = $request->toArray();
        $date = Carbon::now(); $department_id = null;

        array_key_exists('department_id', $params) && $department_id = $params['department_id'];
        array_key_exists('month', $params) && $date = Carbon::createFromFormat('Y-m-d', $params['month']);

        $query = User::with('department')
            ->when($department_id, function ($q) use ($department_id) {
                $q->where('department_id', $department_id);
            })
            ->join('leave_requests', function ($join) {
                $join->on('users.id', '=', 'leave_requests.user_id')
                    ->join('leave_balances', function ($join) {
                        $join->on('leave_requests.leave_balance_id', '=', 'leave_balances.id')
                            ->join('leave_types', function ($join) {
                                $join->on('leave_balances.leave_type_id', '=', 'leave_types.id');
                            });
                    });
            })
            ->select(
                //users table
                'users.id', 'users.name',
                //leave requests table
                'start_date', 'start_date_type','end_date', 'end_date_type', 'duration', 'overall_status', 'leave_requests.leave_balance_id',
                //leave balances table
                'leave_balances.leave_type_id',
                //leave types table
                'leave_types.name as leave_name')
            ->where('overall_status', '=', LeaveRequestStatus::approved->value)
            ->where('start_date', '>=', $date->copy()->firstOfMonth()->toDateString())
            ->where('end_date', '<=', $date->copy()->endOfMonth()->toDateString())
            ->orderBy('start_date', 'desc')
            ->get();

        return response()->json($query);
    }

    public function dashboard(Department $department)
    {
        $user = User::find(Auth::id());

        if ($user->approver_id !== 2)
        {
            return response()->json([
                'message' => 'Only HOD can view leave request calendar',
            ], 401);
        }

        $date = Carbon::now();

        $query = Department::where('id', $department->id)
                    ->select('id','name')
                    ->with(['users' => function ($q) use ($date) {
                        $q->join('leave_requests', function ($join) {
                            $join->on('users.id', '=', 'leave_requests.user_id')

                            ->join('leave_balances', function ($join) {
                                $join->on('leave_requests.leave_balance_id', '=', 'leave_balances.id')

                                    ->join('leave_types', function ($join) {
                                        $join->on('leave_balances.leave_type_id', '=', 'leave_types.id');
                                    });
                            });})
                        ->select(
                            //department table
                            'department_id',
                            //users table
                            'users.id', 'users.name',
                            //leave requests table
                            'start_date', 'start_date_type','end_date', 'end_date_type', 'duration', 'overall_status', 'leave_requests.leave_balance_id',
                            //leave balances table
                            'leave_balances.leave_type_id',
                            //leave types table
                            'leave_types.name as leave_name')
                        ->where('overall_status', '=', LeaveRequestStatus::approved->value)
                        ->where('start_date', '>=', $date->copy()->subMonth()->toDateString())
                        ->where('end_date', '<=', $date->copy()->addMonth()->endOfMonth()->toDateString())
                        ->orderBy('start_date', 'desc');
                    }])
                    ->get();


        return response()->json($query);
    }
}
