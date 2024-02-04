<?php

namespace App\Api\Controller;

use App\Http\Controllers\Controller;
use App\Http\Resources\PersonalInformationResource;
use App\Models\PersonalInformation;
use App\Models\User;
use App\Services\UserInformationService;
use Illuminate\Http\Request;

class UserInformationApi extends Controller
{
    public function __construct()
    {
        $this->UserInformationService = new UserInformationService();
    }

    public function getPersonalInformation(int $userID)
    {
        $response = PersonalInformation::with(['family'])
            ->where('user_id', $userID)
            ->first();

        return $response ?
            new PersonalInformationResource($response) :
            response()->json(['data' => [], 'message' => 'Personal Information Not Exist']);
    }

    public function storePersonalInformation(int $userID, Request $request)
    {
        if (count($request->all()) == 0) {
            return response()->json(['message' => 'No new data to be update.']);
        }

        $response = $this->UserInformationService->storeInformation( array_merge(
            ['user_id' => $userID],
            $request->toArray()
        ));

        return response()->json($response);
    }

    public function storeFamilyDetail(int $userID, Request $request)
    {
        if (count($request->all()) == 0) {
            return response()->json(['message' => 'No new data to be update.']);
        }

        $response = $this->UserInformationService->storeFamilyDetail( array_merge(
            ['user_id' => $userID],
            $request->toArray()
        ));

        return response()->json($response);
    }
}
