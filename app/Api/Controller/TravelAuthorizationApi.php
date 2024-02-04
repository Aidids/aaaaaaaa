<?php

namespace App\Api\Controller;

use App\Enums\LeaveRequestStatus;
use App\Http\Controllers\Controller;
use App\Http\Resources\TravelAuthorizationResource;
use App\Http\Traits\ApproverTrait;
use App\Http\Traits\EformTraits;
use App\Http\Traits\SendEmailTrait;
use App\Models\EForm;
use App\Models\EFormAttachment;
use App\Models\LeaveBalance;
use App\Models\LeaveRequest;
use App\Models\LeaveType;
use App\Models\TravelAuthorization;
use Exception;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class TravelAuthorizationApi extends Controller
{
    use ApproverTrait, EformTraits, sendEmailTrait;

    public function index()
    {
        $response = TravelAuthorization::with(['eform' => function ($query) {
            $query->with('user', 'firstApprover', 'secondApprover', 'hrIncharge', 'attachment');
        },
            'department'
        ])
            ->whereHas('eform' , function ($query) {
                $query->where('user_id', '=', Auth::id());
            })
            ->orderBy('created_at', 'desc')
            ->paginate(6);

        return TravelAuthorizationResource::collection($response);
    }

    public function show(int $travelId)
    {
        $travel = TravelAuthorization::with(['eform' => function ($query) {
            $query->with('user','firstApprover', 'secondApprover', 'hrIncharge', 'attachment');
            }])->find($travelId);

        return new TravelAuthorizationResource($travel);
    }

    public function applyTravel(Request $request)
    {
        $travel = TravelAuthorization::storeTravel($request->toArray());

        $response = $this->storeEForm($travel, $request->toArray());

        $message = [
            'message' => 'E-Form Application submitted. We have sent an email to your respective approvers.',
            'status' => 201
        ];

        $responseTravel = TravelAuthorization::with(['eform' => function ($query) {
                        $query->with('user','firstApprover', 'secondApprover', 'hrIncharge', 'attachment');
                        }])->find($travel->id);

        return (new TravelAuthorizationResource($responseTravel))->additional($message);
    }

    public function editTravel(int $travelId, Request $request)
    {
        $request['travel_purpose'] = filter_var($request['travel_purpose'], FILTER_VALIDATE_BOOLEAN);
        $data = $request->toArray();

        $travel = TravelAuthorization::findOrFail($travelId)->update($data);

        // if user edit new attachment
        if (! empty($request['files'])) {
            $EForm = TravelAuthorization::with('eform')->find($travelId)->eform->first();

            $this->eFormAddMultipleAttachment(
                data: $request->only(['files']),
                storage_name: 'travel-authorization',
                model: $EForm,
            );
        }

        if($travel) {
            $travelModel = TravelAuthorization::find($travelId);
            $this->resetStatus(model: $travelModel);

            return response()->json(['message' => 'Travel form edit success'],201);
        }

        return response()->json(['message' => 'Travel form edit failed']);
    }

    public function cancelTravel(int $travelId)
    {
        $travel = TravelAuthorization::find($travelId);

        $response = $this->eFormCancelStatus(model: $travel);

        return response()->json(['message' => $response]);
    }

    public function approvalIndex()
    {
        $pendingTravel = $this->approvalIndexQuery(new TravelAuthorization());

        return TravelAuthorizationResource::collection($pendingTravel);
    }

    public function reviewTravel(int $travelId, Request $request)
    {
        $data = $request->toArray();
        $travel = TravelAuthorization::find($travelId);

        $response = $this->eFormStatusUpdate(
          model: $travel,
          data: $data,
        );

        // send email to user if overall_status == approved/rejected

        return response()->json(['message' => $response]);
    }

    public function getTravelSummary(Request $request)
    {
        $data = $request->toArray();

        $response = $this->eFormSummary(
            model: new TravelAuthorization(),
            data: $data,
        );

        return TravelAuthorizationResource::collection($response);
    }

    public function hrReviewTravel(int $travelId, Request $request)
    {
        try {
            $travel = TravelAuthorization::find($travelId);

            $response = $this->eFormStatusHR(
                model: $travel,
                data: $request->toArray(),
            );

            $eform = EForm::where([
                'eformable_id' => $travelId,
                'eformable_type' => get_class($travel),
            ])->first();

            // if hr approve, create new leave_request
            if ( $request->has('start_date')
                && ($eform->overall_status === LeaveRequestStatus::completed->value)) {
                $this->createLeaveRequest(
                    eform: $eform,
                    travel: TravelAuthorization::find($travelId),
                    data: $request->toArray()
                );
            }

            return response()->json(['message' => $response]);
        }
        catch (Exception $ex) {
            return response()->json([
                'message' => 'Error.., Please contact IT, ',
            ], 400);
        }
    }

    public function hrUpload(int $travelID, Request $request)
    {
        $travel = TravelAuthorization::with('eform')->find($travelID);
        $EForm = $travel->eform->first();

        return $this->eFormAddMultipleAttachment(
            data: $request->only(['files']),
            storage_name: 'travel-authorization',
            model: $EForm,
            hrUpload: true,
        );
    }

    public function deleteHrUpload(int $travelID, Request $request)
    {
        $EFormAttachment = EFormAttachment::find($request['attachment_id']);

         return $this->deleteAttachment(
             attachment_id: $request['attachment_id'],
             model: $EFormAttachment,
             storage_name: 'travel-authorization',
         );
    }

    private function createLeaveRequest(EForm $eform, TravelAuthorization $travel, Array $data)
    {
            $leaveBalance = LeaveBalance::where([
                'leave_type_id' => LeaveType::getOutOfOfficeLeaveType()->id,
                'user_id' => $eform->user_id,
            ])->first();

            LeaveRequest::create([
                'user_id' => $eform->user_id,
                'leave_balance_id' => $leaveBalance->id,
                'start_date' => $data['start_date'],
                'start_date_type' => $data['start_date_type'],
                'end_date' => $data['end_date'],
                'end_date_type' => $data['end_date_type'],
                'duration' => $data['duration'], // calculate from front-end
                'reason' => $travel->purpose,
                'overall_status' => 'approved',
            ]);
    }
}
