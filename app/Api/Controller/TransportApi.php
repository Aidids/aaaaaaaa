<?php

namespace App\Api\Controller;

use App\Http\Controllers\Controller;
use App\Http\Requests\GeneralAttachmentRequest;
use App\Http\Resources\TransportResource;
use App\Http\Traits\AttachmentTrait;
use App\Models\Transport;
use App\Models\TravelClaim;
use Carbon\Carbon;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Illuminate\Database\QueryException;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class TransportApi extends Controller
{
    use AttachmentTrait;

    public function index(int $travelId)
    {
        try {
            TravelClaim::findOrFail($travelId);
        } catch(ModelNotFoundException $ex) {
            return response()->json([
                'message' => 'Travel ID:'. $travelId .' not found, please contact IT',
            ], 400);
        }

        $response = Transport::where('travel_id', $travelId)
            ->paginate(10);

        return TransportResource::collection($response);
    }

    public function store(int $travelId, Request $request)
    {
        $storeSuccess = $this->storeTransport($travelId, $request);

        if (! $storeSuccess)
        {
            return response()->json([
                'message' => 'Fail updating expense total, please contact IT. Travel ID: ' . $travelId,
            ], 400);
        }

        $total = Transport::where('travel_id', $travelId)->sum('amount');

        try {
            TravelClaim::findOrFail($travelId)->update([
                'total_transport' => ($total) ?: 0,
                'index_page' => 3,
            ]);
        }
        catch (ModelNotFoundException | QueryException $ex) {
            return response()->json([
                'message' => 'Fail updating transport total, please contact IT. Travel ID:'.$travelId,
                'error' => $ex
            ], 400);
        }

        return response()->json([
            'message' => 'Transport saved',
        ], 201);
    }

    public function add(int $travelId, Request $request)
    {
        $storeSuccess = $this->storeTransport($travelId, $request);

        if (! $storeSuccess)
        {
            return response()->json([
                'message' => 'Fail updating expense total, please contact IT. Travel ID: ' . $travelId,
            ], 400);
        }

        try {
            $transport = Transport::create(['travel_id' => $travelId]);
        } catch(ModelNotFoundException $ex) {
            return response()->json([
                'message' => 'Travel ID:'. $travelId .' not found, please contact IT',
            ], 400);
        }

        return (new TransportResource($transport))
            ->response()
            ->setStatusCode(201);
    }

    public function delete(Request $request)
    {
        try {
            $transport = Transport::findOrFail($request->get('id'));
        } catch(\Illuminate\Database\Eloquent\ModelNotFoundException $ex) {
            return response()->json([
//                'message' => 'Transport ID not found. Please contact IT',
                'message' => 'Could not delete transport form. [id: '. $request->get('id') .'], please contact IT',
            ], 400);
        }

        $deleted = $transport->delete();

        return ($deleted) ?
            response()->json(['message' => 'Transport deleted.'],
                201)
            :
            response()->json([
                'message' => 'Transport form deletion error. Please contact IT'],
                404);

    }

    public function addAttachment(int $transportId, GeneralAttachmentRequest $request)
    {
        try {
            $transport = Transport::findOrFail($transportId);
        }
        catch (ModelNotFoundException $ex) {
            return response()->json([
                'message' => 'Transport form ID not found',
            ], 400);
        }

        $uploadStatus = $this->addLiveAttachment(
            model: $transport,
            file: $request->file('file'),
            directory: 'transport',
        );

        return ($uploadStatus) ?
            (new TransportResource(Transport::find($transport->id)))
                ->additional([
                    'message' => 'Attachment uploaded'
                ])
            :
            response()->json([
                'message' => 'Fail uploading attachment. Please try again',
            ], 400);
    }

    public function deleteAttachment(int $transportId)
    {
        try {
            $transport = Transport::findOrFail($transportId);
        }
        catch (ModelNotFoundException $ex) {
            return response()->json([
                'message' => 'Transport ID not found',
            ], 400);
        }

        $deleted = $this->deleteLiveAttachment(
            model: $transport,
            directory: 'transport',
        );

        return ($deleted) ?
            response()->json(['message' => 'Attachment deleted'],
                201)
            :
            response()->json(['message' => 'Attachment not found, Please try again'],
                404);
    }

    private function storeTransport(int $travelId, Request $request)
    {
        if ($request['transport'] == NULL) {
            return true;
        }

        foreach ($request->get('transport') as $json) {
            $item = json_decode($json, true);

            if($item['transport_type'] === 'Mileage')
            {
                ($item['start_location'] === 'Others') &&
                $item['start_location'] = $item['start_location'] .' ('. $item['start_name'] .')';

                ($item['end_location'] === 'Others') &&
                $item['end_location'] = $item['end_location'] .' ('. $item['end_name'] .')';
            }

            try {
                Transport::find($item['id'])->update([
                    'travel_id' => $travelId,
                    'transport_type' => $item['transport_type'],
                    'date' => $item['date'] ? Carbon::createFromFormat('Y-m-d', $item['date']) : null,
                    'start_location' => $item['start_location'] ??= null,
                    'end_location' => $item['end_location'] ??= null,
                    'total_distance' => $item['total_distance'] ??= null,
                    'amount' => $item['amount'],
                    'remark' => $item['remark'] ??= null,
                ]);
            } catch (QueryException $qex) {
                return false;
            }
        }

        return true;
    }
}

