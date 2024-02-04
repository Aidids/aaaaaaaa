<?php

namespace App\Observers;

use App\Http\Traits\NotificationTrait;
use App\Models\LeaveRequest;

class LeaveRequestObserver
{
    use NotificationTrait;

    /**
     * Handle events after all transactions are committed.
     *
     * @var bool
     */
//    public $afterCommit = true;

    /**
     * Handle the LeaveRequest "created" event.
     *
     * @param  \App\Models\LeaveRequest  $leaveRequest
     * @return void
     */
    public function created(LeaveRequest $leaveRequest)
    {
        if($leaveRequest->first_approver_id) {
            $this->createNotification(
                model: $leaveRequest,
                user_id: $leaveRequest->first_approver_id,
            );
        }

        if($leaveRequest->second_approver_id) {
            $this->createNotification(
                model: $leaveRequest,
                user_id: $leaveRequest->second_approver_id,
            );
        }
    }

    /**
     * Handle the LeaveRequest "updated" event.
     *
     * @param  \App\Models\LeaveRequest  $leaveRequest
     * @return void
     */
    public function updated(LeaveRequest $leaveRequest)
    {
        $this->updateNotification($leaveRequest);
    }

    /**
     * Handle the LeaveRequest "deleted" event.
     *
     * @param  \App\Models\LeaveRequest  $leaveRequest
     * @return void
     */
    public function deleted(LeaveRequest $leaveRequest)
    {
        //
    }

//    /**
//     * Handle the LeaveRequest "restored" event.
//     *
//     * @param  \App\Models\LeaveRequest  $leaveRequest
//     * @return void
//     */
//    public function restored(LeaveRequest $leaveRequest)
//    {
//        //
//    }
//
//    /**
//     * Handle the LeaveRequest "force deleted" event.
//     *
//     * @param  \App\Models\LeaveRequest  $leaveRequest
//     * @return void
//     */
//    public function forceDeleted(LeaveRequest $leaveRequest)
//    {
//        //
//    }
}
