<?php

namespace App\Observers;

use App\Enums\Status;
use App\Http\Traits\NotificationTrait;
use App\Http\Traits\SendEmailTrait;
use App\Mail\RedeemOffshoreRequestMail;
use App\Mail\RedeemOffshoreStatusMail;
use App\Models\RedeemOffshoreLeave;

class RedeemOffshoreLeaveObserver
{
    use NotificationTrait, SendEmailTrait;

    /**
     * Handle the RedeemOffshoreLeave "created" event.
     *
     * @param  \App\Models\RedeemOffshoreLeave  $redeemOffshoreLeave
     * @return void
     */
    public function created(RedeemOffshoreLeave $redeemOffshoreLeave)
    {
        if (! is_null($redeemOffshoreLeave->first_approver_id)) {
            // create notification for 1st approver
            $this->createNotification(
                model: $redeemOffshoreLeave,
                user_id: $redeemOffshoreLeave->first_approver_id,
            );

            //send email to 1st approver
            $this->sendRequestEmail(
                mailClass: RedeemOffshoreRequestMail::class,
                model: $redeemOffshoreLeave,
                mailSubject: 'Redeem Offshore Leave',
                receiverID: $redeemOffshoreLeave->first_approver_id,
            );
        }
        else if (! is_null($redeemOffshoreLeave->second_approver_id) ) {
            // create notification for 2nd approver
            $this->createNotification(
                model: $redeemOffshoreLeave,
                user_id: $redeemOffshoreLeave->second_approver_id,
            );

            // send email to 2nd approver
            $this->sendRequestEmail(
                mailClass: RedeemOffshoreRequestMail::class,
                model: $redeemOffshoreLeave,
                mailSubject: 'Redeem Offshore Leave',
                receiverID: $redeemOffshoreLeave->second_approver_id,
            );
        }
    }

    /**
     * Handle the RedeemOffshoreLeave "updated" event.
     *
     * @param  \App\Models\RedeemOffshoreLeave  $redeemOffshoreLeave
     * @return void
     */
    public function updated(RedeemOffshoreLeave $redeemOffshoreLeave)
    {
        if (is_null($redeemOffshoreLeave->hr_ic_status)) {
            $this->updateNotification($redeemOffshoreLeave);
        }

        if ($redeemOffshoreLeave->first_approver_status === Status::approved->value && $redeemOffshoreLeave->overall_status === Status::pending->value && $redeemOffshoreLeave->second_approver_id !== null) {
            // create notification for 2nd approver
            $this->createNotification(
                model: $redeemOffshoreLeave,
                user_id: $redeemOffshoreLeave->second_approver_id,
            );

            // send email  to 2nd approver
            $this->sendRequestEmail(
                mailClass: RedeemOffshoreRequestMail::class,
                model: $redeemOffshoreLeave,
                mailSubject: 'Redeem Offshore Leave',
                receiverID: $redeemOffshoreLeave->second_approver_id,
            );
        }
        else if ($redeemOffshoreLeave->overall_status === Status::approved->value) {
           $this->createHRNotification(
               model: $redeemOffshoreLeave,
               hr_id: [172, 24],
           );

           $this->userHrPendingNotify($redeemOffshoreLeave);
        }
        else if ($redeemOffshoreLeave->overall_status === Status::rejected->value) {
            // send email to user(requester)
            $this->sendStatusUpdateEmail(
                mailClass: RedeemOffshoreStatusMail::class,
                model: $redeemOffshoreLeave,
                mailSubject: 'Redeem Offshore Leave',
            );
        }

        if ($redeemOffshoreLeave->hr_ic_status) {
            $this->updateHRNotification(
                model: $redeemOffshoreLeave,
                hr_id: [172, 24],
            );

            if ($redeemOffshoreLeave->balance_received ||
                $redeemOffshoreLeave->overall_status === Status::hr_rejected->value) {
                // send email to user(requester)
                $this->sendStatusUpdateEmail(
                    mailClass: RedeemOffshoreStatusMail::class,
                    model: $redeemOffshoreLeave,
                    mailSubject: 'Redeem Offshore Leave',
                );
            }
        }
    }
}
