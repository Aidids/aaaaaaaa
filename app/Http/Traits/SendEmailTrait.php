<?php

namespace App\Http\Traits;

use App\Mail\CancelLeaveMail;
use App\Mail\EFormCancelMail;
use App\Mail\EFormRequestMail;
use App\Mail\EFormStatusUpdateMail;
use App\Mail\LeaveRequestMail;
use App\Mail\TravelClaimRequestMail;
use App\Mail\TravelClaimStatusMail;
use App\Models\LeaveRequest;
use App\Models\User;
use Exception;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Log;
use Illuminate\Database\Eloquent\Model;

trait SendEmailTrait
{
    // FOR LEAVE REQUEST MODULE
    public function sendEmailRequestLeave(LeaveRequest $leaveRequest, int $userId, string $type): void
    {
        $requester = User::with('department')->findOrFail($userId);
        $approvers = [$leaveRequest->first_approver_id, $leaveRequest->second_approver_id];

        try{
            foreach ($approvers as $approverID) {
                if ($approverID === null) {
                    continue;
                }

                $approver = User::find($approverID);
                if ( $approver->email !== null && User::isEnabled($approver->id) ) {
                    $flag = 'RequestLeave';
                    Mail::to($approver->email)->send(new LeaveRequestMail($requester, $leaveRequest, $type, $flag));
                }
            }
        }
        catch (Exception $ex) {
            $errorMessage = "Error on SendEmailTrait: " . $ex->getMessage();
            Log::error($errorMessage);
        }
    }

    public function sendEmailApproveLeave(LeaveRequest $leaveRequest, int $userId, string $type): void
    {
        $flag = 'LeaveStatusUpdate';
        $requester = User::with('department')->findOrFail($userId);

        try {
            if($requester->email !== null && User::isEnabled($requester->id) ) {
                Mail::to($requester->email)->send(new LeaveRequestMail($requester, $leaveRequest, $type, $flag));
            }
        }
        catch (Exception $ex) {
            $errorMessage = "Error on SendEmailTrait: " . $ex->getMessage();
            Log::error($errorMessage);
        }
    }

    public function sendCancelEmail(int $userId, Model $leaveRequest)
    {
        $requester = User::with('department')->findOrFail($userId);
        $approvers = [$leaveRequest->first_approver_id, $leaveRequest->second_approver_id];


        try{
            foreach ($approvers as $approverID) {
                if ($approverID === null) {
                    continue;
                }

                $approver = User::find($approverID);
                if ($approver->email !== null && User::isEnabled($approver->id) ) {
                    Mail::to($approver->email)->send(new CancelLeaveMail($requester, $leaveRequest));
                }
            }
        }
        catch (Exception $ex) {
            $errorMessage = "Error on sendCancelEmail: " . $ex->getMessage();
            Log::error($errorMessage);
        }
    }

    // FOR LEAVE REQUEST MODULE

    // FOR REDEEM REPLACEMENT LEAVE MODULE
    public function sendRequestEmail($mailClass, Model $model, String $mailSubject, int $receiverID): void
    {
        $requester = User::with('department')->findOrFail($model->user_id);

        try {
            $approver = User::find($receiverID);
            if ($approver->email !== null && User::isEnabled($approver->id) ) {
                Mail::to($approver->email)->send(new $mailClass(
                    $mailSubject, $requester, $model
                ));
            }
        }
        catch (Exception $ex) {
            $errorMessage = "Error on SendEmailTrait: " . $ex->getMessage();
            Log::error($errorMessage);
        }
    }

    public function sendStatusUpdateEmail($mailClass, Model $model, String $mailSubject): void
    {
        $requester = User::with('department')->findOrFail($model->user_id);

        try {
            if($requester->email !== null && User::isEnabled($requester->id) ) {
                Mail::to($requester->email)->send(new $mailClass(
                    $mailSubject, $requester, $model
                ));
            }
        }
        catch (Exception $ex) {
            $errorMessage = "Error on SendEmailTrait: " . $ex->getMessage();
            Log::error($errorMessage);
        }
    }


    // FOR E-FORM MODULE
    public function EFormRequestMail(int $receiverID, Model $eform): void
    {
        $requester = User::with('department')->findOrFail($eform->user_id);

        try{
            $receiver = User::findOrFail($receiverID);
            if ($receiver->email !== null && User::isEnabled($receiver->id) ) {
                Mail::to($receiver->email)->send(new EFormRequestMail(
                    $requester, $eform,
                    mailSubject: $eform->eformable_type
                ));
            }
        }
        catch (Exception $ex) {
            $errorMessage = "sendEmailTrait Error on (EFormRequestMail): " . $ex->getMessage();
            Log::error($errorMessage);
        }
    }

    public function EFormStatusMail(Model $eform): void
    {
        $recipient = User::with('department')->findOrFail($eform->user_id);
        try{
            if ($recipient->email !== null && User::isEnabled($recipient->id)) {
               Mail::to($recipient->email)->send(new EFormStatusUpdateMail(
                   $recipient, $eform,
                   mailSubject: $eform->eformable_type,
               ));
            }
        }
        catch (Exception $ex) {
            $errorMessage = "sendEmailTrait Error on (EFormStatusMail): " . $ex->getMessage();
            Log::error($errorMessage);
        }
    }

    public function EFormCancelMail(Model $eform): void
    {
        $requester = User::with('department')->findOrFail($eform->user_id);
        $approvers = [$eform->first_approver_id, $eform->second_approver_id];

        try{
            foreach ($approvers as $approverID) {
                if ($approverID === null) {
                    continue;
                }

                $approver = User::find($approverID);
                if ($approver->email !== null && User::isEnabled($approver->id)) {
                    Mail::to($approver->email)->send(new EFormCancelMail(
                        $requester, $eform,
                        mailSubject: $eform->eformable_type
                    ));
                }
            }
        }
        catch (Exception $ex) {
            $errorMessage = "sendEmailTrait Error on (EFormCancelMail): " . $ex->getMessage();
            Log::error($errorMessage);
        }
    }

    public function EFormHrMail(Model $eform, String $hrEmail = null): void
    {
        $requester = User::with('department')->findOrFail($eform->user_id);
        try {
            if ($hrEmail !== null) {
                Mail::to($hrEmail)->send( new EFormRequestMail(
                    $requester, $eform,
                    mailSubject: $eform->eformable_type,
                ));
            }
        }
        catch (Exception $ex) {
            $errorMessage = "sendEmailTrait Error on (EFormHrMail): " . $ex->getMessage();
            Log::error($errorMessage);
        }
    }



    // FOR TRAVEL CLAIM MODULE
    public function ClaimRequestMail(int $receiverID, Model $model): void
    {
        $requester = User::with('department')->findOrFail($model->user_id);
        try {
            $receiver = User::findOrFail($receiverID);

            if ($receiver->email !== null && User::isEnabled($receiver->id) ) {
                Mail::to($receiver->email)->send(new TravelClaimRequestMail(
                    $requester, $model,
                    mailSubject: get_class($model),
                ));
            }
        }
        catch (Exception $ex) {
            $errorMessage = "sendEmailTrait Error on (ClaimRequestMail): " . $ex->getMessage();
            Log::error($errorMessage);
        }
    }

    public function ClaimStatusMail(Model $model)
    {
        $recipient = User::with('department')->findOrFail($model->user_id);
        try{
            if ($recipient->email !== null && User::isEnabled($recipient->id)) {
                Mail::to($recipient->email)->send(new TravelClaimStatusMail(
                    $recipient, $model,
                    mailSubject: get_class($model),
                ));
            }
        }
        catch (Exception $ex) {
            $errorMessage = "sendEmailTrait Error on (ClaimStatusMail): " . $ex->getMessage();
            Log::error($errorMessage);
        }
    }
}
