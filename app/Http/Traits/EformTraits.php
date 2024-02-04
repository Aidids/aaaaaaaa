<?php

namespace App\Http\Traits;

use App\Enums\LeaveRequestStatus;
use App\Models\EForm;
use App\Models\User;
use Carbon\Carbon;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Arr;
use Illuminate\Support\Facades\Auth;

trait EformTraits
{
    use AttachmentTrait, ApproverTrait;

    // for applying new E-Form with approvers,
    public function storeEForm(Model $model, Array $data)
    {
        $data = $this->assignApproverPendingStatus($data);

        $EForm = EForm::createEForm(
            Arr::only($data,
                ['user_id', 'first_approver_id', 'first_approver_status',
                    'second_approver_id', 'second_approver_status', 'overall_status']),
            $model
        );

        $model_name = get_class($model);
        $text = explode('\\', $model_name)[2];
        $model_storage_path = strtolower(preg_replace('/(?<=\w)([A-Z])/', '-$1', $text));

        if (array_key_exists('files', $data)) {
            $res = $this->eFormAddMultipleAttachment(
                data: Arr::only($data, ['files']),
                storage_name: $model_storage_path,
                model: $EForm,
            );
        }

        return $EForm;
    }

    // To change choosen E-Form status into canceled
    public function eFormCancelStatus(Model $model)
    {
        $eform = EForm::getEFormDetails($model);

        $response = $eform->update([
            'overall_status' => LeaveRequestStatus::canceled->value,
        ]);

        if ($response) {
            return 'Cancel request successful';
        }

        return 'Cancel request failed';
    }

    // for updating approver status [approved/rejected]
    public function eFormStatusUpdate(Model $model, Array $data)
    {
        $eform = EForm::getEFormDetails($model);

        $response = $this->twoLevelApproval(
            approverID: Auth::id(),
            model: $eform,
            data: $data,
        );

        return $response;
    }

    // for E-Form that has 3 level approvers,
    // this is to update HR status [approved/rejected]
    public function eFormStatusHR(Model $model, Array $data)
    {
        $user = User::find(Auth::id());

        $eform = EForm::getEFormDetails($model);

        return $this->hrApproval(
            hrID: $user->id,
            model: $eform,
            data: $data,
        );
    }

    // to retrieve the summary of E-Form based on the overall_status
    // can filter name or month to retrieve different results
    public function eFormSummary(Model $model, Array $data)
    {

        $month = null; $query = null; $department_id = null; $purpose = null;
        $status = ['approved', 'completed'];

        array_key_exists('month', $data) && $month = Carbon::createFromFormat('Y-m-d', $data['month']);
        array_key_exists('query', $data) && $query = $data['query'];
        array_key_exists('department_id', $data) && $department_id = $data['department_id'];
        array_key_exists('status', $data) && $status = $data['status'];

        if (array_key_exists('purpose', $data))
        {
            ($data['purpose'] !== 'null') && $purpose = filter_var($data['purpose'], FILTER_VALIDATE_BOOLEAN);
        }

        return $model::with(['eform' => function ($q) {
                $q->with(['firstApprover', 'secondApprover', 'user', 'attachment', 'hrIncharge']);
            }])
            ->whereHas('eform.user', function ($q) use ($query, $status) {
                $q->where('name', 'LIKE', '%'. $query .'%');
                $q->whereIn('overall_status', $status);
            })
            ->when(!is_null($purpose), function ($q) use ($purpose) {
                $q->where('travel_purpose', $purpose);
            })
            ->when($month, function ($q) use ($month) {
                $q->where('created_at', '>=', $month->toDateString())
                    ->where('created_at', '<=', $month->copy()->endOfMonth()->toDateString());
            })
            ->when($department_id, function ($q) use ($department_id, $purpose) {
                $q->where('department_id', $department_id);
            })
            ->orderBy('created_at', 'desc')
            ->paginate(10);
    }

    // for editing E-Form Purpose, reset status to pending for all approver
    public function resetStatus(Model $model):void
    {
        $EForm = EForm::where([
            'eformable_id' => $model->id,
            'eformable_type' => get_class($model),
        ])->first();

        if($EForm->first_approver_id !== null) {
            $EForm->update(['first_approver_status' => 'pending']);
        }

        if($EForm->second_approver_id !== null) {
            $EForm->update(['second_approver_status' => 'pending']);
        }
    }



    #########################################################
    #########################################################
    #########################################################

    // assign the approver status, if the E-form data has approver details
    private function assignApproverPendingStatus(Array $data)
    {
        foreach (['first_approver', 'second_approver'] as $approver) {
            $approverID = $approver . '_id';
            $approverStatus = $approver . '_status';

            if (array_key_exists($approverID, $data)) {
                $data[$approverStatus] = 'pending';
            } else {
                $data[$approverID] = NULL;
                $data[$approverStatus] = NULL;
            }
        }
        $data['overall_status'] = ($data['first_approver_id'] || $data['second_approver_id']) ? 'pending' : NULL;

        return $data;
    }
}
