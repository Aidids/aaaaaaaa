<?php

namespace App\Api\Controller;

use App\Enums\Status;
use App\Http\Controllers\Controller;
use App\Http\Resources\RedeemOffshoreLeaveResource;
use App\Http\Traits\AttachmentTrait;
use App\Models\RedeemOffshoreAttachment;
use App\Models\RedeemOffshoreLeave;
use App\Services\RedeemOffshoreLeaveService;
use Exception;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;

class RedeemOffshoreLeaveApi extends Controller
{
    use AttachmentTrait;

    public function __construct()
    {
        $this->RedeemOffshoreService = new RedeemOffshoreLeaveService();
    }

    public function index()
    {
        $RedeemOffshore = RedeemOffshoreLeave::with(['user', 'firstApprover', 'secondApprover', 'hrIncharge', 'attachment'])
            ->where('user_id', Auth::id())
            ->orderBy('created_at', 'desc')
            ->paginate(10);

        return RedeemOffshoreLeaveResource::collection($RedeemOffshore);
    }

    public function apply(Request $request)
    {
        try {
            $data = array_merge(
                $request->toArray(),
                ['user_id' => Auth::id()],
            );
            $redeemOffshore = RedeemOffshoreLeave::storeRedeemOffshore($data);

            if ($request->has('files')) {
                $this->addMultipleFileV2(
                    main_model: $redeemOffshore,
                    attachment_model: new RedeemOffshoreAttachment(),
                    data: $request->only('files'),
                    directory: 'redeem-offshore-leave',
                );
            }

            return new RedeemOffshoreLeaveResource($redeemOffshore);
        }
        catch(Exception $exception) {
            Log::info('Error (Apply Redeem Offshore): ' . $exception);
            return response()->json([
                'message' => 'Error Apply Redeem Offshore (check log)...',
            ], 404);
        }
    }

    public function cancel(Request $request)
    {
        try {
            $response = RedeemOffshoreLeave::find($request->get('id'))->update([
                'overall_status' => Status::canceled->value,
            ]);

            if($response) {
                return response()->json([
                    'message' => 'Selected Redeem Offshore Leave has been canceled.',
                ], 202);
            }

            return response()->json([
                'message' => 'Something Wrong, Pls Contact IT.',
            ], 400);

        }
        catch(Exception $exception)
        {
            Log::info('Error (cancel Redeem Offshore): ' . $exception);
            return response()->json([
                'message' => 'Error cancel Redeem Offshore (check log)...',
            ], 404);
        }
    }

    public function edit(Request $request)
    {
        $response = $this->RedeemOffshoreService->editOffshore($request->toArray());

        return response()->json([
            'message' => $response,
        ], 202);
    }

    public function approveIndex()
    {
        try {
            $response = $this->RedeemOffshoreService->approvalIndexQuery();

            return RedeemOffshoreLeaveResource::collection($response);
        }
        catch(Exception $exception) {
            Log::info('Error (approveIndex Redeem Offshore): ' . $exception);
            return response()->json([
                'message' => 'Error approveIndex Redeem Offshore (check log)...',
            ], 404);
        }
    }

    public function approve(Request $request)
    {
        try {
            $response = $this->RedeemOffshoreService->approveOffshore($request->toArray());

            return response()->json([
                'message' => $response
            ], 202);
        }
        catch(Exception $exception)
        {
            Log::info('Error (approve Redeem Offshore): ' . $exception);
            return response()->json([
                'message' => 'Error approve Redeem Offshore (check log)...',
            ], 404);
        }
    }

    public function summary(Request $request)
    {
        try {
            $response = $this->RedeemOffshoreService->summary($request->toArray());

            return RedeemOffshoreLeaveResource::collection($response);
        }
        catch(Exception $exception)
        {
            Log::info('Error (summary Redeem Offshore): ' . $exception);
            return response()->json([
                'message' => 'Error summary Redeem Offshore (check log)...',
            ], 404);
        }
    }

    public function finalizeOffshore(Request $request)
    {
        // check if admin sent balance_received from front-end
        // if not received, calculate automatically 7 offshore days => 1 days offshore leave
        try {
            $response = $this->RedeemOffshoreService->finalize($request->toArray());

            return response()->json([
                'message' => $response
            ], 202);
        }
        catch(Exception $exception)
        {
            Log::info('Error (approve Redeem Offshore): ' . $exception);
            return response()->json([
                'message' => 'Error approve Redeem Offshore (check log)...',
            ], 404);
        }
    }

    public function uploadAttachment(Request $request)
    {
        try{
            $this->RedeemOffshoreService->upload($request->toArray());

            return response()->json([
                'message' => 'Attachment Upload Successful',
            ], 202);
        }
        catch(Exception $exception)
        {
            Log::info('Error (uploadAttachment Redeem Offshore): ' . $exception);
            return response()->json([
                'message' => 'Error uploadAttachment Redeem Offshore (check log)...',
            ], 404);
        }
    }

    public function deleteAttachment(Request $request)
    {
        try {
            $response = $this->RedeemOffshoreService->deleteAttachment($request->toArray());

            return response()->json([
                'message' => $response,
            ],202);
        }
        catch(Exception $exception)
        {
            Log::info('Error (deleteAttachment Redeem Offshore): ' . $exception);
            return response()->json([
                'message' => 'Error deleteAttachment Redeem Offshore (check log)...',
            ], 404);
        }
    }
}
