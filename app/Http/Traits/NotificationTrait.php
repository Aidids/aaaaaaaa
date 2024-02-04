<?php

namespace App\Http\Traits;

use App\Enums\LeaveRequestStatus;
use App\Enums\Status;
use App\Models\PortalNotification;
use App\Models\User;
use Illuminate\Database\Eloquent\Model;
use Carbon\Carbon;
use Illuminate\Support\Facades\Log;

trait NotificationTrait
{
    public function createNotification(Model $model, int $user_id): void
    {
        $requester = User::find($model->user_id);

        PortalNotification::updateOrCreate([
            'user_id' => $user_id,
            'model_name' => get_class($model),
            'model_id' => $model->id,
        ],[
            'color' => $this->getColor($model),
            'requester_name' => $requester->name,
            'status' => Status::pending->value,
        ]);
    }

    public function updateNotification(Model $model) // can be used by all model
    {
        $changes = $model->getChanges();

        $requester = User::find($model->user_id);

        if ($model->overall_status === 'canceled') {
            $this->canceledRequest($model);
            return;
        }

        // update 1st approver notification
        if ($model->first_approver_id) {
            PortalNotification::updateOrCreate([
                'user_id' => $model->first_approver_id,
                'model_name' => get_class($model),
                'model_id' => $model->id,
            ],[
                'status' => $model->first_approver_status,
                'read_at' => Carbon::now()->timezone('Asia/Kuala_Lumpur'),
            ]);
        }

        // update 2nd approver notification
        if ($model->second_approver_id) {
            PortalNotification::updateOrCreate([
                'user_id' => $model->second_approver_id,
                'model_name' => get_class($model),
                'model_id' => $model->id,
            ],[
                'status' => $model->second_approver_status,
                'read_at' => Carbon::now()->timezone('Asia/Kuala_Lumpur'),
            ]);
        }

        // create new notification for user, if model has been fully approved/rejected
        if (array_key_exists('overall_status', $changes)) {
            $this->changeRejectedApproverStatus($model);

            // create new notification row for user,
            PortalNotification::updateOrCreate([
                'user_id' => $model->user_id,
                'color' => $this->getColor($model),
                'model_name' => get_class($model),
                'model_id' => $model->id,
            ], [
                'status' => $changes['overall_status'],
                'read_at' => Carbon::now()->timezone('Asia/Kuala_Lumpur'),
            ]);
        }
    }

    private function changeRejectedApproverStatus(Model $model): void
    {
        if($model->overall_status === 'rejected') {
            PortalNotification::where([
                'model_id' => $model->id,
                'model_name' => get_class($model),
            ])
            ->update([
                'status' => LeaveRequestStatus::rejected->value,
                'read_at' => Carbon::now()->timezone('Asia/Kuala_Lumpur'),
            ]);
        }
    }

    public function createHRNotification(Model $model, Array $hr_id)
    {
        // only can be used by 3 level model flow
        $requester = User::find($model->user_id);

        foreach($hr_id as $hr) {
            if($model->overall_status === 'approved') {
                PortalNotification::create([
                    'user_id' => $hr,
                    'color' => $this->getColor($model),
                    'model_name' => get_class($model),
                    'model_id' => $model->id,
                    'requester_name' => $requester->name,
                    'status' => LeaveRequestStatus::hr_processing->value,
                ]);
            }
        }
    }

    public function updateHRNotification(Model $model, Array $hr_id)
    {
        // only can be used by 3 level model flow
        $hrStatus = ($model->overall_status === Status::completed->value) ?
            LeaveRequestStatus::hr_approved->value : LeaveRequestStatus::hr_rejected->value;

        ($model->overall_status === Status::expired->value) && $hrStatus = Status::expired->value;

        // update USER notification status
        $userNotification = PortalNotification::where([
            'user_id' => $model->user_id,
            'model_name' => get_class($model),
            'model_id' => $model->id,
            ])
            ->first();

        $userNotification->update([
            'status' => $hrStatus,
            'read_at' => Carbon::now()->timezone('Asia/Kuala_Lumpur'),
        ]);

        //update all hr status
        foreach($hr_id as $hr) {
            // update HR notification status
            $hrNotify = PortalNotification::where([
                'user_id' => $hr,
                'model_name' => get_class($model),
                'model_id' => $model->id,
                'status' => 'hr_processing',
            ])->first();

            if (! is_null($hrNotify))
            {
                $hrNotify->update([
                    'status' => $hrStatus,
                    'read_at' => Carbon::now()->timezone('Asia/Kuala_Lumpur'),
                ]);
            }
        }
    }

    private function canceledRequest(Model $model): void
    {
        PortalNotification::where([
            'model_id' => $model->id,
            'model_name' => get_class($model),
        ])
        ->update([
            'status' => LeaveRequestStatus::canceled->value,
            'read_at' => Carbon::now()->timezone('Asia/Kuala_Lumpur'),
        ]);
    }

    public function userHrPendingNotify(Model $model)
    {
        // Create a notification for user (notifying that his request are in HR pending status)
        $notificationUpdate = PortalNotification::where('user_id', $model->user_id)
            ->where('model_name', get_class($model))
            ->where('model_id', $model->id)
            ->orderBy('id', 'desc')
            ->first();

        $notificationUpdate->update([
            'status' => LeaveRequestStatus::hr_pending->value,
            'read_at' => Carbon::now()->timezone('Asia/Kuala_Lumpur'),
        ]);
    }

    #########################
    // E-FORM NOTIFICATIONS
    #########################
    public function createEformNotification(Model $eform, int $user_id, String $status, bool $is_requester = false)
    {
        $requester = (!$is_requester) ?
            User::find($eform->user_id) :  //<- if true, set name of user
            NULL;

        PortalNotification::updateOrCreate([
            'user_id' => $user_id,
            'color' => $this->getColor($eform),
            'model_name' => $eform->eformable_type,
            'model_id' => $eform->eformable_id,
        ],[
            'requester_name' => ($requester) ? $requester->name : NULL,
            'status' => $status,
        ]);
    }

    public function updateEformNotification(Model $eform)
    {
        $changes = $eform->getChanges();
        $requester = User::find($eform->user_id);

        // create new notification for each approver if they are selected
        $approverStatuses = ['first_approver_status', 'second_approver_status'];
        foreach ($approverStatuses as $statusKey) {
            if (array_key_exists($statusKey, $changes)) {
                $approverId = ($statusKey === 'first_approver_status') ? $eform->first_approver_id : $eform->second_approver_id;

                PortalNotification::updateOrCreate([
                    'user_id' => $approverId,
                    'model_name' => $eform->eformable_type,
                    'model_id' => $eform->eformable_id,
                ], [
                    'color' => $this->getColor($eform),
                    'requester_name' => $requester->name,
                    'status' => $changes[$statusKey],
                    'read_at' => Carbon::now()->timezone('Asia/Kuala_Lumpur'),
                ]);
            }
        }

        // create new notification for user, if model has been fully approved/rejected
        if (array_key_exists('overall_status', $changes)) {
            $this->changeRejectedEFormNotification($eform);

            // create new notification row for user,
            PortalNotification::updateOrCreate([
                'user_id' => $eform->user_id,
                'color' => $this->getColor($eform),
                'model_name' => $eform->eformable_type,
                'model_id' => $eform->eformable_id,
            ], [
                'status' => $changes['overall_status'],
                'read_at' => Carbon::now()->timezone('Asia/Kuala_Lumpur'),
            ]);
        }
    }

    private function changeRejectedEFormNotification(Model $eform): void
    {
        // Update all status to rejected (based on query results)
        if($eform->overall_status === 'rejected') {
            PortalNotification::where([
                'model_id' => $eform->eformable_id,
                'model_name' => $eform->eformbale_type,
            ])
                ->update([
                    'status' => LeaveRequestStatus::rejected->value,
                    'read_at' => Carbon::now()->timezone('Asia/Kuala_Lumpur'),
                ]);
        }
    }

    public function EFormCancelNotification(Model $eform): void
    {
        PortalNotification::where([
            'model_id' => $eform->eformable_id,
            'model_name' => $eform->eformable_type,
        ])
        ->update([
            'status' => LeaveRequestStatus::canceled->value,
            'read_at' => Carbon::now()->timezone('Asia/Kuala_Lumpur'),
        ]);
    }

    public function updateHrEFormNotification(Model $eform, Array $hr_id)
    {
        // only can be used by 3 level model flow
        $hrStatus = ($eform->overall_status === 'completed') ?
            LeaveRequestStatus::hr_approved->value : LeaveRequestStatus::hr_rejected->value;

        ($eform->overall_status === 'expired') && $hrStatus = LeaveRequestStatus::expired->value;

        // update USER notification status
        $userNotification = PortalNotification::where([
            'user_id' => $eform->user_id,
            'model_name' => $eform->eformable_type,
            'model_id' => $eform->eformable_id,
        ])->first();

        $userNotification->update([
            'status' => $hrStatus,
            'read_at' => Carbon::now()->timezone('Asia/Kuala_Lumpur'),
        ]);

        //update all hr status
        foreach($hr_id as $hr) {
            // update HR notification status
            $hrNotify = PortalNotification::where([
                'user_id' => $hr,
                'model_name' => $eform->eformable_type,
                'model_id' => $eform->eformable_id,
                'status' => 'hr_processing',
            ])->first();

            if (! is_null($hrNotify))
            {
                $hrNotify->update([
                    'status' => $hrStatus,
                    'read_at' => Carbon::now()->timezone('Asia/Kuala_Lumpur'),
                ]);
            }
        }

    }


    #########################
    // TRAVEL CLAIM NOTIFICATIONS -> using approver_list []
    #########################
    public function storeClaimNotification(Model $model, String $status, int $user_id, bool $is_requester = false)
    {
        $requester = (!$is_requester) ?
            User::find($model->user_id) :  //<- if true, set name of user
            NULL;

        if ($user_id !== 0) {
            PortalNotification::updateOrCreate([
                'user_id' => $user_id,
                'color' => $this->getColor($model),
                'model_name' => get_class($model),
                'model_id' => $model->id,
            ], [
                'requester_name' => ($requester) ? $requester->name : NULL,
                'status' => $status,
            ]);
        }
        else {
            $hr_id = [172];
            foreach ($hr_id as $hr) {
                // create notification for HR
                PortalNotification::create([
                    'user_id' => $hr,
                    'color' => $this->getColor($model),
                    'model_name' => get_class($model),
                    'model_id' => $model->id,
                    'requester_name' => ($requester) ? $requester->name : NULL,
                    'status' => LeaveRequestStatus::hr_processing->value,
                ]);
            }
        }
    }

    public function updateClaimNotification(Model $model, int $user_id, String $status)
    {
        if ($user_id !== 0) {
            PortalNotification::updateOrCreate([
                'user_id' => $user_id,
                'color' => $this->getColor($model),
                'model_name' => get_class($model),
                'model_id' => $model->id,
            ], [
                'status' => $status,
                'read_at' => Carbon::now()->timezone('Asia/Kuala_Lumpur'),
            ]);
        }
        else {
            $hr_id = [172];
            foreach ($hr_id as $hr) {
                // create notification for HR
                PortalNotification::updateOrCreate([
                    'user_id' => $hr,
                    'color' => $this->getColor($model),
                    'model_name' => get_class($model),
                    'model_id' => $model->id,
                ], [
                    'status' => Status::approved->value,
                    'read_at' => Carbon::now()->timezone('Asia/Kuala_Lumpur'),
                ]);
            }
        }
    }

    public function cancelClaimNotification(Model $model): void
    {
        // Update notification to canceled for all involved user/approvers
        PortalNotification::where([
            'model_id' => $model->id,
            'model_name' => get_class($model),
        ])
            ->update([
                'status' => Status::canceled->value,
                'read_at' => Carbon::now()->timezone('Asia/Kuala_Lumpur'),
            ]);
    }

    public function deleteNotification(Model $model): void
    {
        PortalNotification::where([
            'model_id' => $model->id,
            'model_name' => get_class($model),
        ])->delete();
    }

    private function getColor(Model $model): String
    {
        $ModelClass = explode('\\', get_class($model));

        switch($ModelClass[2]) {
            case 'LeaveRequest':
                $color = 'green';
                break;
            case 'RedeemReplacementLeave':
            case 'RedeemOffshoreLeave':
                $color = 'yellow';
                break;
            case 'TravelClaim':
            case 'EForm':
                $color = 'blue';
                break;
        }
        return $color;
    }
}
