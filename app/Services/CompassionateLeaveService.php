<?php

namespace App\Services;

use App\Enums\LeaveRequestStatus;
use App\Http\Controllers\Controller;
use App\Http\Traits\SendEmailTrait;
use App\Models\LeaveBalance;
use App\Models\LeaveRequest;
use App\Models\LeaveRequestAttachment;
use App\Models\LeaveType;
use Carbon\Carbon;
use Illuminate\Support\Arr;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

class CompassionateLeaveService extends Controller
{
    use SendEmailTrait;

    public function __construct()
    {
        $this->now = Carbon::now()->timezone('Asia/Kuala_Lumpur');
    }

    public function applyCompassionate(int $userID, Array $data)
    {
        $additional_data = [
            'user_id' => $userID,
            'calculated' => true,
        ];

        $compassionateLeave = LeaveRequest::create( array_merge(
            $additional_data, Arr::except($data, 'file') )
        );

        $this->uploadAttachment($compassionateLeave->id, $data);

        $this->sendEmail($compassionateLeave, $userID);

        return $compassionateLeave;
    }

    private function uploadAttachment(int $id, Array $data): void
    {
        if (array_key_exists('file', $data) && $data['file']->isValid())
        {
            $file = file_get_contents($data['file']);
            $fileName = $id.'_'.time().'.'.$data['file']->getClientOriginalExtension();
            Storage::disk('leave-request')->put($fileName, $file);

            LeaveRequestAttachment::create([
                'leave_request_id' => $id,
                'path' => $fileName
            ]);
        }
    }

    private function sendEmail(LeaveRequest $leaveRequest, int $user_id): void
    {
        $leaveType = LeaveBalance::with('leave')
            ->where('id', $leaveRequest->leave_balance_id)
            ->first();

        $leaveTypeName = $leaveType->leave->name;
        $this->sendEmailRequestLeave($leaveRequest, $user_id, $leaveTypeName);
    }
}
