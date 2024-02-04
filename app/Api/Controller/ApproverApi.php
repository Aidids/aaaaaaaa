<?php

namespace App\Api\Controller;

use App\Http\Controllers\Controller;
use App\Http\Requests\ApproverRequest;
use App\Http\Resources\ApproverResource;
use App\Http\Resources\UserResource;
use App\Models\User;
use App\Services\ApproverService;
use Illuminate\Http\Request;

class ApproverApi extends Controller
{
    public function __construct()
    {
        $this->approverService = new ApproverService();
    }

    public function index()
    {
        $users = User::whereNotNull('approver_id')->get();
        return ApproverResource::collection($users);
    }

    public function store(ApproverRequest $request)
    {
        $request->validated();

        $response = $this->approverService->assignApprover($request->toArray());

        return new ApproverResource($response);
    }

    public function getApproverLevel(int $userId)
    {
        $response = User::where('id',$userId)
            ->with(['department', 'approver'])
            ->first();

        return new UserResource($response);
    }

    public function getEveryone()
    {
        $response = User::all();

        return ApproverResource::collection($response);
    }
}
