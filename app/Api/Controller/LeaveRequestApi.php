<?php

namespace App\Api\Controller;

use App\Http\Controllers\Controller;
use App\Http\Requests\ApproveLeaveRequest;
use App\Http\Requests\StoreLeaveRequestRequest;
use App\Http\Resources\LeaveRequestResource;
use App\Http\Resources\LeaveBalanceResource;
use App\Models\LeaveRequest;
use App\Services\LeaveRequestService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;


class LeaveRequestApi extends Controller
{
    public function __construct()
    {
        $this->leaveRequestService = new LeaveRequestService();
    }

    public function index(int $userId)
    {
        $response =  LeaveRequest::with(['firstApprover', 'secondApprover', 'attachment', 'replacementCoupon'])
                        ->with(['leaveBalance' => function ($query) {
                            $query->with('leave');
                        }])
                        ->where('user_id', $userId)
                        ->whereYear('created_at', now()->year)
                        ->orderBy('created_at', 'desc')
                        ->paginate(10);

        return LeaveRequestResource::collection($response);
    }

    public function getAllLeave(int $userId)
    {
        $response = $this->leaveRequestService->getAllLeave($userId);

        return LeaveBalanceResource::collection($response);
    }

    public function applyLeave(int $userId, StoreLeaveRequestRequest $request)
    {
        $response = $this->leaveRequestService->applyLeave($userId, $request->toArray());

        if ($response instanceof LeaveRequest) {
            $message = [
                'message' => 'Leave request successfully sent. We have sent an email to your respective approvers.',
                'status' => 200
            ];

            return (new LeaveRequestResource($response))->additional($message);
        }
        else {
            return new JsonResponse([
                'message' => $response,
            ], 400);
        }
    }

    public function cancelLeave(int $userId,Request $request)
    {
        $this->leaveRequestService->cancelLeave($userId, $request->toArray());

        return response()->json(['message' => 'Successfully canceled selected leave']);
    }

    public function getPendingLeaveRequest(int $userId)
    {
        $response = $this->leaveRequestService->getPendingLeaveRequest($userId);

        return LeaveRequestResource::collection($response);
    }

    public function getLeaveSummary(Request $request)
    {
        $response = $this->leaveRequestService->getLeaveSummary($request->toArray());

        return LeaveRequestResource::collection($response);
    }

    public function approvePendingLeaveRequest(int $approverId, ApproveLeaveRequest $request)
    {
        $response = $this->leaveRequestService->approvePendingLeaveRequest($approverId, $request->toArray());

        return response()->json(['message' => $response]);
    }

    public function addHrNote(int $leaveId, Request $request)
    {
        $updated = $this->leaveRequestService->addHrNote($leaveId, $request->toArray());

        if ($updated)
        {
            return response()->json(['message' => 'Note successfully added']);
        }

        return response()->json(['message' => 'Opps something went wrong. please try again']);
    }
}
