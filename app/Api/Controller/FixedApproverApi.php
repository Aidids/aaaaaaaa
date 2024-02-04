<?php

namespace App\Api\Controller;

use App\Http\Controllers\Controller;
use App\Http\Resources\FixedApproverResource;
use App\Models\FixedApprover;
use App\Services\FixedApproverService;
use Illuminate\Http\Request;

class FixedApproverApi extends Controller
{
    public function __construct()
    {
        $this->FixedApproverService = new FixedApproverService();
    }

    public function getApprovers(Request $request)
    {
        $response = FixedApprover::with('user')
                        ->where('user_id', $request['user_id'])
                        ->first();

        if (is_null($response))
        {
            return response()->json([
                'message' => 'This profile has no assign approvers',
            ], 404);
        }


        return new FixedApproverResource($response);
    }

    public function storeApprovers(Request $request)
    {
        $response = $this->FixedApproverService->storeApprovers(
            data: $request->toArray(),
        );

        if ($response)
        {
            return (new FixedApproverResource($response))
                ->additional([
                    'message' => 'Successfully updated fixed approvers'
                ]);
        }

        return response()->setStatusCode(401, 'Opps something went wrong. Please try again');
    }
}
