<?php

namespace App\Api\Controller;

use App\Http\Controllers\Controller;
use App\Http\Requests\StoreRedeemReplacementLeaveRequest;
use App\Http\Resources\RedeemReplacementLeaveResource;
use App\Models\LeaveType;
use App\Models\RedeemReplacementLeave;
use App\Services\RedeemReplacementLeaveConsoleService;
use App\Services\RedeemReplacementLeaveService;
use Exception;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;

class RedeemReplacementLeaveApi extends Controller
{
    public function __construct()
    {
        $this->RedeemReplacementLeaveService = new RedeemReplacementLeaveService();
    }

    public function index()
    {
        $response = RedeemReplacementLeave::with(['leaveRequest', 'firstApprover', 'secondApprover', 'hrIncharge', 'attachment'])
            ->where('user_id', Auth::id())
            ->orderBy('updated_at', 'desc')
            ->paginate(5);

        return RedeemReplacementLeaveResource::collection($response);
    }

    public function getAllCompleted()
    {
        $response = RedeemReplacementLeave::with(['leaveRequest', 'firstApprover', 'secondApprover', 'hrIncharge', 'attachment'])
            ->where('user_id', Auth::id())
            ->where('overall_status', 'completed')
            ->where('balance_qty', '>', 0)
            ->orderBy('expired_date', 'ASC')
            ->get();

        return RedeemReplacementLeaveResource::collection($response);
    }

    public function applyReplacement(StoreRedeemReplacementLeaveRequest $request)
    {
        $response = $this->RedeemReplacementLeaveService->applyReplacement(
            userID: Auth::id(),
            data: $request->toArray()
        );

        return response()->json(['message' => $response]);
    }

    public function editReplacement(Request $request)
    {
        $response = $this->RedeemReplacementLeaveService->editReplacement($request->toArray());

        return $response;
    }

    public function approveIndex()
    {
        try {
            $response = $this->RedeemReplacementLeaveService->approvalIndexQuery();

            return RedeemReplacementLeaveResource::collection($response);
        }
        catch(Exception $exception) {
            Log::info('Error (approveIndex Redeem Replacement): ' . $exception);
            return response()->json([
                'message' => 'Error approveIndex Redeem Replacement (check log)...',
            ], 404);
        }
    }

    public function approveReplacement(Request $request)
    {
        $response = $this->RedeemReplacementLeaveService->approveReplacement(
            data: $request->toArray(),
        );

        return response()->json(['message' => $response], 202);
    }

    public function getReplacementSummary(Request $request)
    {
        $response = $this->RedeemReplacementLeaveService->getReplacementSummary($request->toArray());

        return RedeemReplacementLeaveResource::collection($response);
    }

    public function finalizeReplacement(Request $request)
    {
        $response = $this->RedeemReplacementLeaveService->finalizeReplacement(
            hrID: Auth::id(),
            data: $request->toArray()
        );

        return response()->json(['message' => $response]);
    }

    public function uploadAttachment(Request $request)
    {
        $response = $this->RedeemReplacementLeaveService->uploadReplacementAttachment($request->toArray());

        return response()->json(['message' => $response]);
    }

    public function deleteAttachment(Request $request)
    {
        $response = $this->RedeemReplacementLeaveService->deleteReplacementAttachment($request->toArray());

        return response()->json(['message' => $response]);
    }
}
