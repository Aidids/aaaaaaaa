<?php

namespace App\Services;

use App\Http\Controllers\Controller;
use App\Models\FixedApprover;

class FixedApproverService extends Controller
{
    public function storeApprovers(Array $data)
    {
        $approversID = collect([]); // for experiment purpose only

        $approvers = array_map('intval', explode(',', $data['approvers']) );

        foreach ($approvers as $approver_id) {
            $approversID->push($approver_id);
        }

        return FixedApprover::storeApprovers(
            user_id: $data['user_id'],
            approversID: $approversID->all(),
        );
    }
}
