<?php

namespace App\Observers;

use App\Enums\Status;
use App\Http\Traits\NotificationTrait;
use App\Http\Traits\SendEmailTrait;
use App\Mail\RedeemReplacementRequestMail;
use App\Mail\RedeemReplacementStatusMail;
use App\Models\RedeemReplacementLeave;

class RedeemReplacementLeaveObserver
{
    use NotificationTrait, SendEmailTrait;

    /**
     * Handle the RedeemReplacementLeave "created" event.
     *
     * @param  \App\Models\RedeemReplacementLeave  $redeemReplacementLeave
     * @return void
     */
    public function created(RedeemReplacementLeave $redeemReplacementLeave)
    {
        $redeemReplacementLeave = RedeemReplacementLeave::find($redeemReplacementLeave->id);

        if (! is_null($redeemReplacementLeave->first_approver_id)) {
            // create notification for 1st approver
            $this->createNotification(
                model: $redeemReplacementLeave,
                user_id: $redeemReplacementLeave->first_approver_id,
            );

            //send email to 1st approver
            $this->sendRequestEmail(
                mailClass: RedeemReplacementRequestMail::class,
                model: $redeemReplacementLeave,
                mailSubject: 'Redeem Replacement Leave',
                receiverID: $redeemReplacementLeave->first_approver_id,
            );
        }
        else if (! is_null($redeemReplacementLeave->second_approver_id) ) {
            // create notification for 2nd approver
            $this->createNotification(
                model: $redeemReplacementLeave,
                user_id: $redeemReplacementLeave->second_approver_id,
            );

            // send email to 2nd approver
            $this->sendRequestEmail(
                mailClass: RedeemReplacementRequestMail::class,
                model: $redeemReplacementLeave,
                mailSubject: 'Redeem Replacement Leave',
                receiverID: $redeemReplacementLeave->second_approver_id,
            );
        }
    }

    /**
     * Handle the RedeemReplacementLeave "updated" event.
     *
     * @param  \App\Models\RedeemReplacementLeave  $redeemReplacementLeave
     * @return void
     */
    public function updated(RedeemReplacementLeave $redeemReplacementLeave)
    {
        if (! $redeemReplacementLeave->hr_ic_status) {
            $this->updateNotification($redeemReplacementLeave);
        }

        if ($redeemReplacementLeave->first_approver_status === Status::approved->value && $redeemReplacementLeave->overall_status === Status::pending->value && $redeemReplacementLeave->second_approver_id !== null) {
            // create notification for 2nd approver
            $this->createNotification(
                model: $redeemReplacementLeave,
                user_id: $redeemReplacementLeave->second_approver_id,
            );

            // send email to 2nd approver
            $this->sendRequestEmail(
                mailClass: RedeemReplacementRequestMail::class,
                model: $redeemReplacementLeave,
                mailSubject: 'Redeem Replacement Leave',
                receiverID: $redeemReplacementLeave->second_approver_id,
            );
        }
        else if ($redeemReplacementLeave->overall_status === Status::approved->value) {
            $this->createHRNotification(
                model: $redeemReplacementLeave,
                hr_id: [171, 172, 24],
            );

            $this->userHrPendingNotify($redeemReplacementLeave);
        }
        else if ($redeemReplacementLeave->overall_status === Status::rejected->value) {
            // send email to user(requester)
            $this->sendStatusUpdateEmail(
                mailClass: RedeemReplacementStatusMail::class,
                model: $redeemReplacementLeave,
                mailSubject: 'Redeem Replacement Leave',
            );
        }

        if ($redeemReplacementLeave->hr_ic_status) {
            $this->updateHRNotification(
                model: $redeemReplacementLeave,
                hr_id: [171, 172, 24],
            );

            if ($redeemReplacementLeave->added_qty ||
                $redeemReplacementLeave->overall_status === Status::hr_rejected->value) {
                // send email to user(requester)
                $this->sendStatusUpdateEmail(
                    mailClass: RedeemReplacementStatusMail::class,
                    model: $redeemReplacementLeave,
                    mailSubject: 'Redeem Replacement Leave',
                );
            }
        }
        // HR ID
        // 171 -> ID FAHIM
        // 172 -> ID LIN
    }
}
