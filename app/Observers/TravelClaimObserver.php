<?php

namespace App\Observers;

use App\Enums\Status;
use App\Http\Traits\NotificationTrait;
use App\Http\Traits\SendEmailTrait;
use App\Models\TravelClaim;
use Illuminate\Support\Facades\Log;

class TravelClaimObserver
{
    use NotificationTrait, SendEmailTrait;

    /**
     * Handle the TravelClaim "created" event.
     *
     * @param  \App\Models\TravelClaim  $travelClaim
     * @return void
     */
    public function created(TravelClaim $travelClaim)
    {
        //
    }

    /**
     * Handle the TravelClaim "updated" event.
     *
     * @param  \App\Models\TravelClaim  $travelClaim
     * @return void
     */
    public function updated(TravelClaim $travelClaim)
    {
        $changes = $travelClaim->getChanges();
        $original = $travelClaim->getOriginal();

        if ( array_key_exists('isDraft', $changes) &&
            $travelClaim->status === Status::pending->value) {

            // create notification for approver
            $this->storeClaimNotification(
                model: $travelClaim,
                status: Status::pending->value,
                user_id: $travelClaim->current_approver,
            );

            // send email to first approver
            $this->ClaimRequestMail(
                receiverID: $travelClaim->current_approver,
                model: $travelClaim,
            );
        }
        else if(array_key_exists('current_approver', $changes)&&
            $travelClaim->status === Status::processing->value) {

            // Update previous approver notification status
            $this->updateClaimNotification(
                model: $travelClaim,
                user_id: $original['current_approver'],
                status: Status::approved->value,
            );

            // create notification for next approver
            $this->storeClaimNotification(
                model: $travelClaim,
                status: Status::pending->value,
                user_id: $travelClaim->current_approver,
            );
            // send email to next approver
            $this->ClaimRequestMail(
                receiverID: $travelClaim->current_approver,
                model: $travelClaim,
            );
        }

        // if status === rejected || approved
        if ($travelClaim->status === Status::approved->value ||
            $travelClaim->status === Status::rejected->value) {

            // update last approver notification
            $this->updateClaimNotification(
                model: $travelClaim,
                user_id: $travelClaim->current_approver,
                status: $travelClaim->status,
            );

            // create notification for requester
            $this->storeClaimNotification(
                model: $travelClaim,
                status: $travelClaim->status,
                user_id: $travelClaim->user_id,
                is_requester: true,
            );

            // send email to requester
            $this->ClaimStatusMail($travelClaim);
        }

        // if status === canceled
        if ($travelClaim->status === Status::canceled->value) {
            $this->cancelClaimNotification($travelClaim);
        }
    }

    public function deleted(TravelClaim $travelClaim)
    {
        $this->deleteNotification($travelClaim);
    }
}
