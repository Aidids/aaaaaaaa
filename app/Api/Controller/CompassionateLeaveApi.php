<?php

namespace App\Api\Controller;

use App\Http\Controllers\Controller;
use App\Http\Requests\StoreLeaveRequestRequest;
use App\Http\Resources\LeaveRequestResource;
use App\Services\CompassionateLeaveService;
use Illuminate\Http\Request;

class CompassionateLeaveApi extends Controller
{
    public function __construct()
    {
        $this->compassionateLeaveService = new CompassionateLeaveService();
    }

    public function applyCompassionate(int $user_id, StoreLeaveRequestRequest $request)
    {
        $response = $this->compassionateLeaveService->applyCompassionate(
            userID: $user_id,
            data: $request->toArray(),
        );

        $message = [
            'message' => 'Compassionate Leave request successfully sent. We have sent an email to your respective approvers.',
            'status' => 200
        ];

        return (new LeaveRequestResource($response))->additional($message);
    }
}
