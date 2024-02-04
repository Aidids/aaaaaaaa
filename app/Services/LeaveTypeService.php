<?php

namespace App\Services;

use App\Models\LeaveBalance;
use App\Models\LeaveCarryForward;
use App\Models\LeaveEntitlement;
use App\Models\LeaveType;
use App\Models\User;


class LeaveTypeService
{
    public function __construct()
    {
        $this->leaveBalanceService = new LeaveBalanceService();
    }

    public function store(Array $data): LeaveType
    {
        $leaveType = LeaveType::create($data);

        $this->updateLeaveEntitlement(leaveTypeId: $leaveType->id, data: $data);
        $this->updateCarryForward(leaveTypeId: $leaveType->id, data: $data);

//        $this->leaveBalanceService->assignToAllUsers($leaveType);

        return $leaveType;
    }

    public function update(int $id, Array $data): LeaveType
    {
        $leaveType = LeaveType::find($id);
        $leaveType->update($data);

        $this->updateLeaveEntitlement(leaveTypeId: $id, data: $data);
        $this->updateCarryForward(leaveTypeId: $id, data: $data);
        $this->leaveBalanceService->updateLeaveBalance($leaveType);

        return $leaveType;
    }

    private function updateLeaveEntitlement(int $leaveTypeId, Array $data): void
    {
        $checkEntitlement = $data['entitlement'] ?? null;

        if (!is_null($checkEntitlement))
        {
            foreach ($data['entitlement'] as $entitlement)
            {
                LeaveEntitlement::updateOrCreate(
                    ['id' => $entitlement['id'] ?? null],
                    array_merge(['leave_type_id' => $leaveTypeId], $entitlement),
                );
            }
        }
    }

    private function updateCarryForward(int $leaveTypeId, Array $data): void
    {
        $checkEntitlement = $data['carry_forward'] ?? null;

        if (!is_null($checkEntitlement))
        {
            foreach ($data['carry_forward'] as $carryForward)
            {
                LeaveCarryForward::updateOrCreate(
                    ['id' => $carryForward['id'] ?? null],
                    array_merge(['leave_type_id' => $leaveTypeId], $carryForward),
                );
            }
        }
    }
}
