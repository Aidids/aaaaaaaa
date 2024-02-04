<?php

namespace App\Api\Controller;

use App\Http\Controllers\Controller;
use App\Http\Requests\StoreLeaveTypeRequest;
use App\Http\Resources\LeaveTypeResource;
use App\Models\LeaveBalance;
use App\Models\LeaveType;
use App\Services\LeaveTypeService;
use Illuminate\Http\Request;

class LeaveTypeApi extends Controller
{
    public function __construct()
    {
        $this->leaveTypeService = new LeaveTypeService();
    }

    public function index()
    {
        return LeaveTypeResource::collection(LeaveType::all());
    }

    public function store(StoreLeaveTypeRequest $request)
    {
        $request->validated();

        $leaveType = $this->leaveTypeService->store(
            data: $request->toArray()
        );

        return new LeaveTypeResource($leaveType);
    }

    public function update(int $id, StoreLeaveTypeRequest $request)
    {
        $request->validated();

        $leaveType = $this->leaveTypeService->update(
            id: $id,
            data: $request->toArray()
        );

        return new LeaveTypeResource($leaveType);
    }

    public function delete(int $id)
    {
        $leaveType = LeaveType::find($id);
        $leaveType->delete();

        return new LeaveTypeResource($leaveType);
    }


}
