<?php

namespace App\Http\Traits;

use App\Enums\LeaveRequestStatus;
use App\Http\Resources\ApproverResource;
use App\Http\Resources\UserResource;
use App\Models\User;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Auth;
use PhpParser\Node\Scalar\String_;

trait ApproverTrait
{
    public function twoLevelApproval(int $approverID, Model $model, Array $data)
    {
        $approverLevel = ($model->first_approver_id === $approverID) ? 'first_approver' : 'second_approver';

        if ($model->overall_status === LeaveRequestStatus::rejected->value) {
            return 'This request has been rejected by another approver.';
        }

        if ($model->overall_status === LeaveRequestStatus::canceled->value) {
            return 'This request has been canceled by requester.';
        }

        $model->update([
            $approverLevel.'_status' => $data[$approverLevel.'_status'],
            $approverLevel.'_remark' => $data[$approverLevel.'_remark'],
            $approverLevel.'_date' => $data[$approverLevel.'_date'],
        ]);

        if ($data[$approverLevel.'_status'] === 'rejected') {
            $model->update([
                'overall_status' => $data[$approverLevel.'_status'],
            ]);

            return 'You have rejected this request';
        }

        // check if another approver status is pending
        $another_status = $this->checkStatus($approverLevel, $model);

        // if another status is false == (another approver is empty) //
        if($another_status === false && $data[$approverLevel.'_status'] === 'approved') {
            $model->update([
                'overall_status' => $data[$approverLevel.'_status'],
            ]);

            return 'You have fully approved this request';
        }

        return 'You have approved this request';
    }

    public function hrApproval(int $hrID, Model $model, Array $data)
    {
        if($model->overall_status !== 'approved') {
            return 'This request is not approve by Supervisor/Head of Department yet';
        }

        if ($data['hr_status'] === 'rejected') {
            $model->update([
                'hr_ic_id' => $hrID,
                'hr_ic_status' => $data['hr_status'],
                'hr_ic_remark' => $data['hr_remark'],
                'hr_ic_date' => $data['hr_date'],
                'overall_status' => LeaveRequestStatus::hr_rejected->value,
            ]);

            return 'You have rejected this request';
        }
        else {
            $model->update([
                'hr_ic_id' => $hrID,
                'hr_ic_status' => $data['hr_status'],
                'hr_ic_remark' => $data['hr_remark'],
                'hr_ic_date' => $data['hr_date'],
                'overall_status' => LeaveRequestStatus::completed->value,
            ]);

            return 'You have fully approve this request';
        }
    }

    // approver index for E-Form Module
    public function approvalIndexQuery(Model $model)
    {
        return $model::with(['eform' => function ($query) {
                    $query->with('user','firstApprover', 'secondApprover', 'hrIncharge', 'attachment');
                }])->whereHas('eform', function ($query) {
                    $query->where('first_approver_id', '=', Auth::id());
                    $query->where('first_approver_status', '=', LeaveRequestStatus::pending->value);
                    $query->where('overall_status', '=', LeaveRequestStatus::pending->value);
                })->orWhereHas('eform', function ($query) {
                    $query->whereNull('first_approver_id');
                    $query->where('second_approver_id', '=', Auth::id());
                    $query->where('second_approver_status', '=', LeaveRequestStatus::pending->value);
                    $query->where('overall_status', '=', LeaveRequestStatus::pending->value);
                })->orWhereHas('eform', function ($query) {
                    $query->where('first_approver_status', '=', LeaveRequestStatus::approved->value);

                    $query->where('second_approver_id', '=', Auth::id());
                    $query->where('second_approver_status', '=', LeaveRequestStatus::pending->value);
                    $query->where('overall_status', '=', LeaveRequestStatus::pending->value);
                })
                ->orderBy('updated_at', 'desc')
                ->paginate(10);
    }

    public function assignNextApprover(Array $approversID, int &$currentApproverID): bool
    {
        if (end($approversID) === $currentApproverID) {
            return true;
        }

        $key = array_search($currentApproverID, $approversID);
        $currentApproverID = $approversID[$key + 1];

        return false;
    }

    public function getApproverDetail(Array $approvers_id)
    {
        $approvers = [];
        foreach($approvers_id as $approverID) {
            if ($approverID == 0) {
                $approvers[] = $this->getHRa();
            }
            else {
                $data = User::with('department')
                    ->select('id', 'name', 'approver_id', 'department_id')
                    ->find($approverID);

                $approvers[] = new ApproverResource($data);
            }
        }

        return $approvers;
    }

    public function currentApproverDetail(int $approverID)
    {
        return new ApproverResource(User::find($approverID));
    }

    private function checkStatus(String $ApproverType, Model $model)
    {
        $second_approver = ($ApproverType == 'first_approver') ? 'second_approver_status' : 'first_approver_status';

        return ($model->$second_approver == 'pending');
    }

    private function getHra()
    {
        return [
            'id' => 0,
            'name' => 'HRA',
            'department' => 'HR DEPARTMENT',
            'approver_id' => null,
            'approver_level' => null,
        ];
    }
}
