<?php

namespace App\Api\Controller;

use App\Http\Controllers\Controller;
use App\Models\LeaveBalance;
use App\Models\User;
use App\Services\LeaveBalanceService;
use Illuminate\Http\Request;
use App\Services\ProfileServices;
use App\Services\DocumentService;
use App\Http\Resources\ProfileResource;
use App\Http\Resources\AddressResource;
use App\Http\Resources\DocumentResource;
use App\Http\Resources\EmergencyContactResource;
use App\Http\Requests\EmergencyContactRequest;
use App\Http\Requests\DocumentRequest;
use Illuminate\Support\Facades\Validator;


/**
 * @property ProfileServices $profile
 */
class ProfileApi extends Controller
{
    public function __construct()
    {
        $this->profileService = new ProfileServices();
        $this->documentService = new DocumentService();
        $this->leaveBalanceService = new LeaveBalanceService();
    }

    public function index(int $id)
    {
        $user = $this->profileService->getProfile($id);

        return new ProfileResource($user);
    }

    public function update(int $id, Request $request)
    {
        $user = User::find($id);
        $original = $user->getOriginal();

        $user->update($request->toArray());

        if (!is_null($original['gender'])) {
            $leaveTypeId = ($original['gender'] === 'm') ? 6 : 7;

            LeaveBalance::where([['user_id', '=', $id], ['leave_type_id', '=', $leaveTypeId]])
                ->first()->delete();
        }

        $this->leaveBalanceService->assignLeaveBalance($user);
        $this->leaveBalanceService->calcLeaveBalance($user);

        $status = [
            'status' => '200',
            'message' => 'Job profile updated',
        ];

        return (new ProfileResource($user))->additional($status);
    }

    public function getPermAddress(int $id)
    {
        $address = $this->profileService->getPermanentAddress($id);

        return new AddressResource($address);
    }

    public function updatePermAddress(int $id, Request $request)
    {
        $address = $this->profileService->updatePermanentAddress(
            id: $id,
            data: $request->only(['details', 'city', 'state', 'zip', 'country', 'phone'])
        );

        $msg = [
          'message' => 'Successfully updated permanent address',
          'status' => 200
        ];

        return (new AddressResource($address))->additional($msg);
    }

    public function deletePermAddress(int $id)
    {
        $msg = $this->profileService->deletePermanentAddress($id);

        return response()->json([
            'message' => $msg,
            'status' => 200
        ]);
    }

    public function getCurrAddress(int $id)
    {
        $address = $this->profileService->getCurrentAddress($id);

        return new AddressResource($address);
    }

    public function updateCurrAddress(int $id, Request $request)
    {
        $address = $this->profileService->updateCurrentAddress(
            id: $id,
            data: $request->only(['details', 'city', 'state', 'zip', 'country', 'phone'])
        );

        $msg = [
            'message' => 'Successfully updated current address',
            'status' => 200
        ];

        return (new AddressResource($address))->additional($msg);
    }

    public function deleteCurrAddress(int $id)
    {
        $msg = $this->profileService->deleteCurrentAddress($id);

        return response()->json([
            'message' => $msg,
            'status' => 200
        ]);
    }

    public function getEmergency(int $id)
    {
        $address = $this->profileService->getEmergencyContact($id);

        return EmergencyContactResource::collection($address);
    }

    public function updateEmergency(int $id, EmergencyContactRequest $request)
    {
        $request->validated();

        $contact = $this->profileService->updateEmergencyContact(
            $id,
            $request->only('id', 'name', 'relationship', 'phone', 'email', 'address', 'city', 'state',
            'zip', 'country')
        );

        $msg = [
            'message' => 'Emergency contact updated',
            'status' => 200
        ];

        return (new EmergencyContactResource($contact))->additional($msg);
    }

    public function deleteEmergency(int $userId, int $contactId)
    {
        $msg =  $this->profileService->deleteEmergencyContact($userId, $contactId);

        return response()->json(['message' => $msg]);
    }

    public function getDocument(int $userId, $query = null)
    {
        $documents = $this->documentService->get($userId, $query);

        return DocumentResource::collection($documents);
    }

    public function uploadDocument(int $userId, DocumentRequest $request)
    {
        if (isset($request->validator) && $request->validator->fails())
        {
            return response()->json([
                'message' => 'Invalid format',
                'errors' => $request->validator->messages(),
            ],422);
        }

        $doc =  $this->documentService->upload($userId, $request->only('id','name','file'));

        $msg = [
            'message' => $request->get('name').' successfully uploaded',
            'status' => 200
        ];

        return (new DocumentResource($doc))->additional($msg);
    }

    public function editDocument(int $userId, Request $request)
    {
        $validator = Validator::make($request->all(),[
            'name' => 'nullable|max:100',
            'file' => 'nullable|mimes:pdf|max:5120',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'message' => 'Invalid format',
                'errors' => $validator->messages(),
                'status' => 422
            ]);
        }

        $doc = $this->documentService->edit($userId,
                    $request->only('id', 'name', 'file'));

        $msg = [
            'message' => $doc->name.' successfully updated',
            'status' => 200
        ];

        return (new DocumentResource($doc))->additional($msg);
    }

    public function deleteDocument(int $userId, int $documentId)
    {
        $msg = $this->documentService->delete($documentId);

        return response()->json([
            'message' => $msg,
            'status' => 200
        ]);
    }

    public function getAllEmployees($query = null)
    {
        $employees = $this->profileService->getAllEmployees($query);


        if (count($employees)) {
            return ProfileResource::collection($employees);
        }

        return response()->json([
            'message' => 'No employee or email found',
            'status' => 404
        ]);
    }
}
