<?php

namespace App\Api\Controller;

use App\Http\Controllers\Controller;
use App\Http\Requests\GeneralAttachmentRequest;
use App\Http\Resources\AllowanceResource;
use App\Http\Traits\AttachmentTrait;
use App\Models\Allowance;
use App\Models\TravelClaim;
use Illuminate\Http\Request;

class AllowanceApi extends Controller
{
    use AttachmentTrait;

    public function index(int $travelId)
    {
        try {
            TravelClaim::findOrFail($travelId);
        } catch(\Illuminate\Database\Eloquent\ModelNotFoundException $ex) {
            return response()->json([
                'message' => 'Travel ID:'. $travelId .' not found, please contact IT',
            ], 400);
        }

        $index = Allowance::where('travel_id', $travelId)
            ->paginate(10);

        return AllowanceResource::collection($index);
    }

    public function store(int $travelId, Request $request)
    {
        $storeSuccess = $this->storeAllowance($travelId, $request);

        if (! $storeSuccess)
        {
            return response()->json([
                'message' => 'Fail updating expense total, please contact IT. Travel ID: ' . $travelId,
            ], 400);
        }

        $total = Allowance::where('travel_id', $travelId)->sum('amount');

        try {
            TravelClaim::findOrFail($travelId)->update([
                'total_allowance' => ($total) ?: 0,
                'index_page' => 2,
            ]);
        } catch(\Illuminate\Database\Eloquent\ModelNotFoundException $ex) {
            return response()->json([
                'message' => 'Fail updating allowance total, please contact IT. Travel ID:'.$travelId,
            ], 400);
        }

        return response()->json([
            'message' => 'Allowance saved',
        ], 201);
    }

    public function add(int $travelId, Request $request)
    {
        $storeSuccess = $this->storeAllowance($travelId, $request);

        if (! $storeSuccess)
        {
            return response()->json([
                'message' => 'Fail updating expense total, please contact IT. Travel ID: ' . $travelId,
            ], 400);
        }

        try {
            $allowance = Allowance::create(['travel_id' => $travelId]);
        } catch(\Illuminate\Database\QueryException $ex) {
            return response()->json([
                'message' => 'Travel ID not found',
            ], 400);
        }

        return (new AllowanceResource($allowance))
                ->response()
                ->setStatusCode(201);
    }

    public function delete(Request $request)
    {
        try {
            $allowance = Allowance::findOrFail($request->get('id'));
        } catch(\Illuminate\Database\Eloquent\ModelNotFoundException $ex) {
            return response()->json([
                'message' => 'Allowance ID not found. Please contact IT',
            ], 400);
        }

        $deleted = $allowance->delete();

        if (! $deleted)
        {
            return response()->json([
                'message' => 'Allowance not found. Please contact IT',
            ], 404);
        }

        return response()->json([
            'message' => 'Allowance deleted.',
        ], 201);
    }

    public function addAttachment(int $allowanceId, GeneralAttachmentRequest $request)
    {
        try {
            $allowance = Allowance::findOrFail($allowanceId);
        } catch(\Illuminate\Database\Eloquent\ModelNotFoundException $ex) {
            return response()->json([
                'message' => 'Allowance ID not found',
            ], 400);
        }

        $uploaded = $this->addLiveAttachment(
            model: $allowance,
            file: $request->file('file'),
            directory: 'allowance'
        );

        if ($uploaded)
        {
            return (new AllowanceResource(Allowance::find($allowance->id)))
                ->additional([
                   'message' => 'Attachment uploaded'
                ]);
        }

        return response()->json([
            'message' => 'Fail uploading attachment. Please try again.',
        ], 400);

    }

    public function deleteAttachment(int $allowanceId)
    {
        try {
            $allowance = Allowance::findOrFail($allowanceId);
        } catch(\Illuminate\Database\Eloquent\ModelNotFoundException $ex) {
            return response()->json([
                'message' => 'Allowance ID not found',
            ], 400);
        }

        $deleted = $this->deleteLiveAttachment(model: $allowance, directory: 'allowance');

        if ($deleted)
        {
            return response()->json([
                'message' => 'Attachment successfully deleted',
            ], 201);
        }

        return response()->json([
            'message' => 'Attachment not found. Please try again',
        ], 404);
    }

    private function storeAllowance(int $travelId,Request $request)
    {
        if($request['allowance'] === NULL)
        {
            return true;
        }

        foreach ($request->get('allowance') as $json) {
            $item = json_decode($json, true);

            ($item['allowance_type'] === 'Others') &&
            $item['allowance_type'] = $item['allowance_type'] .' ('. $item['allowance_name'] .')';

            try {
                Allowance::find($item['id'])->update([
                    'travel_id' => $travelId,
                    'start_date' => $item['start_date'] ??= null,
                    'end_date' => $item['end_date'] ??= null,
                    'allowance_type' => $item['allowance_type'] ??= null,
                    'allowance_rate' => $item['allowance_rate'] ??= null,
                    'meal_total_hours' => $item['meal_total_hours'] ??= null,
                    'amount' => $item['amount'] ??= null,
                    'remark' => $item['remark'] ??= null,
                ]);
            } catch (\Illuminate\Database\QueryException $ex) {
                return false;
            }
        }

        return true;
    }
}
