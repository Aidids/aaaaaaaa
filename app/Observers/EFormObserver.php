<?php

namespace App\Observers;

use App\Enums\LeaveRequestStatus;
use App\Http\Traits\NotificationTrait;
use App\Http\Traits\SendEmailTrait;
use App\Models\EForm;
use App\Models\TravelAuthorization;

class EFormObserver
{
    use NotificationTrait, SendEmailTrait;

    /**
     * Handle the EForm "created" event.
     *
     * @param  \App\Models\EForm  $eForm
     * @return void
     */
    public function created(EForm $eForm)
    {
        // if first approver exist, create notification for 1st approver only.
        // if first approver doesn't exist, create notification for 2nd approver,

        if($eForm->first_approver_id) {
            $this->createEformNotification(
                eform: $eForm,
                user_id: $eForm->first_approver_id,
                status: LeaveRequestStatus::pending->value,
            );

            // send email to approver here
            $this->EFormRequestMail(
                receiverID: $eForm->first_approver_id, eform: $eForm,
            );
        }
        else {
            $this->createEformNotification(
                eform: $eForm,
                user_id: $eForm->second_approver_id,
                status: LeaveRequestStatus::pending->value,
            );

            // send email to approver here
            $this->EFormRequestMail(
                receiverID: $eForm->second_approver_id, eform: $eForm,
            );
        }
    }

    /**
     * Handle the EForm "updated" event.
     *
     * @param  \App\Models\EForm  $eForm
     * @return void
     */
    public function updated(EForm $eForm)
    {
        // send email to requester after overall_status changed
        if ($eForm->overall_status !== 'pending') {
            if ($eForm->overall_status === 'canceled') {
                $this->EFormCancelMail($eForm);
                $this->EFormCancelNotification($eForm);
            }
            else {
                $this->EFormStatusMail($eForm);
            }
        }

        if (! $eForm->hr_ic_status && $eForm->overall_status !== 'canceled') {
            $this->updateEformNotification($eForm); // <- 'approved|rejected'

            // Create notification for 2nd approver
            if (($eForm->first_approver_status === 'approved' && $eForm->second_approver_id)
                && $eForm->second_approver_status === 'pending') {

                $this->createEformNotification(
                    eform: $eForm,
                    user_id: $eForm->second_approver_id,
                    status: LeaveRequestStatus::pending->value,
                );

                // send email to approver here
                $this->EFormRequestMail(
                    receiverID: $eForm->second_approver_id,
                    eform: $eForm,
                );
            }
        }

        if ($eForm->overall_status === 'approved') {
            if ($eForm->eformable_type === 'App\Models\TravelAuthorization') {
                $this->notifyHrTravel($eForm);

                // Create notification for user
                $this->createEformNotification(
                    eform: $eForm,
                    user_id: $eForm->user_id,
                    status: LeaveRequestStatus::hr_pending->value,
                    is_requester: true,
                );
            }
        }

        if ($eForm->hr_ic_status) {
            $this->updateHrEFormNotification(
                eform: $eForm,
                hr_id: [172, 24],
            );
        }
    }

    private function notifyHrTravel(EForm $eform)
    {
        $travel = TravelAuthorization::find($eform->eformable->id);

        if ($travel->travel_purpose) {
            // notify and send email to lin HR
            $this->createEformNotification(
                eform: $eform,
                user_id: (int)env('HR_PIC_ID'),
                status: LeaveRequestStatus::hr_processing->value,
            );

            $this->EFormHrMail(
                eform: $eform,
                hrEmail: env('HR_PIC_EMAIL'),
            );
        }
        else {
            // notify and send email to marissa HR
            $this->createEformNotification(
                eform: $eform,
                user_id: (int)env('TA_PIC_ID'),
                status: LeaveRequestStatus::hr_processing->value,
            );

            $this->EFormHrMail(
                eform: $eform,
                hrEmail: env('TA_PIC_EMAIL'),
            );
        }
    }
}
