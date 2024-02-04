<?php

namespace App\Api\Controller;

use App\Enums\Status;
use App\Http\Controllers\Controller;
use App\Http\Resources\AllowanceGraphResource;
use App\Http\Resources\ExpenseGraphResource;
use App\Http\Resources\TransportResource;
use App\Http\Resources\TravelClaimResource;
use App\Http\Traits\ApproverTrait;
use App\Models\Allowance;
use App\Models\Expense;
use App\Models\FixedApprover;
use App\Models\PortalNotification;
use App\Models\TravelClaim;
use App\Models\User;
use App\Services\TravelClaimService;
use Exception;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Illuminate\Database\QueryException;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Support\Arr;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;
use ZipArchive;

class TravelClaimApi extends Controller
{
    use ApproverTrait;

    public function __construct()
    {
        $this->TravelClaimService = new TravelClaimService();
    }

    public function index()
    {
        $fa = FixedApprover::where('user_id', Auth::id())->first();
        if (!$fa) {
            return response()->json([
                'message' => 'Fixed Approvers Not Assigned, Contact HR!'
            ], 404);
        }

        $draft = TravelClaim::where('user_id', Auth::id())
            ->where('isDraft', '=', true)
            ->first();

        if (is_null($draft))
        {
            $travel = TravelClaim::create(['user_id' => Auth::id()]);
            return new TravelClaimResource($travel);
        }

        return new TravelClaimResource($draft);
    }

    public function edit(int $travelId)
    {
        $fa = FixedApprover::where('user_id', Auth::id())->first();
        if (!$fa) {
            return response()->json([
                'message' => 'Fixed Approvers Not Assigned, Contact HR!'
            ], 404);
        }

        $draftEdit = TravelClaim::where([
            'id' => $travelId,
            'user_id'=> Auth::id(),
        ])->first();

        return new TravelClaimResource($draftEdit);
    }

    public function history()
    {
        try {
            $travel = TravelClaim::with(['user', 'department'])
                ->with(['allowances' => function ($query) {
                    $query->select(
                        'travel_id',
                        DB::raw('
                        CASE
                            WHEN allowance_type LIKE "Offshore%" THEN "Offshore"
                            WHEN allowance_type LIKE "Oversea%" THEN "Oversea"
                            WHEN allowance_type LIKE "Others%" THEN "Others"
                            ELSE allowance_type
                        END AS type'),
                        DB::raw('SUM(amount) as total_amount')
                    )
                        ->groupBy('travel_id', 'type')
                        ->get();
                }])
                ->with(['transports' => function ($query) {
                    $query->select(
                        'travel_id',
                        DB::raw('
                        CASE
                            WHEN transport_type LIKE "Mileage%" THEN "Mileage"
                        ELSE transport_type
                        END AS transport_type'),
                        DB::raw('SUM(amount) as total_amount')
                    )
                        ->groupBy('travel_id', 'transport_type')
                        ->get();
                }])
                ->with(['expenses' => function ($query) {
                    $query->select(
                        'travel_id',
                        DB::raw('
                        CASE
                            WHEN description LIKE "Telephone%"
                            OR description LIKE "Laundry%"
                            OR description LIKE "Others%"
                            THEN "Others"
                        ELSE description
                        END AS expense_type'),
                        DB::raw('SUM(amount) as total_amount')
                    )
                        ->groupBy('travel_id', 'expense_type')
                        ->get();
                }])
                ->where([
                    'user_id' => Auth::id(),
                    'isDraft' => 0,
                ])
                ->orderBy('updated_at', 'desc')
                ->paginate(10);

            return TravelClaimResource::collection($travel);
        }
        catch(QueryException $ex) {
            return response()->json([
                'message' => 'Error: Invalid Query ' .$ex,
            ], 400);
        }
    }

    public function show(int $travelId)
    {
        try {
            $travel = TravelClaim::with(['user', 'department'])
                ->with(['allowances' => function ($query) {
                    $query->select(
                        'travel_id',
                        DB::raw('
                        CASE
                            WHEN allowance_type LIKE "Offshore%" THEN "Offshore"
                            WHEN allowance_type LIKE "Oversea%" THEN "Oversea"
                            WHEN allowance_type LIKE "Others%" THEN "Others"
                            ELSE allowance_type
                        END AS type'),
                        DB::raw('SUM(amount) as total_amount')
                    )
                        ->groupBy('travel_id', 'type')
                        ->get();
                }])
                ->with(['transports' => function ($query) {
                    $query->select(
                        'travel_id',
                        DB::raw('
                        CASE
                            WHEN transport_type LIKE "Mileage%" THEN "Mileage"
                        ELSE transport_type
                        END AS transport_type'),
                        DB::raw('SUM(amount) as total_amount')
                    )
                        ->groupBy('travel_id', 'transport_type')
                        ->get();
                }])
                ->with(['expenses' => function ($query) {
                    $query->select(
                        'travel_id',
                        DB::raw('
                        CASE
                            WHEN description LIKE "Telephone%"
                            OR description LIKE "Laundry%"
                            OR description LIKE "Others%"
                            THEN "Others"
                        ELSE description

                        END AS expense_type'),
                        DB::raw('SUM(amount) as total_amount')
                    )
                        ->groupBy('travel_id', 'expense_type')
                        ->get();
                }])
                ->find($travelId);

            return response()->json([
                'travelClaim' => new TravelClaimResource($travel)
            ], 201);
        }
        catch(QueryException $ex) {
            return response()->json([
                'message' => 'Error: Invalid Query ' . $ex,
            ], 400);
        }
    }

    public function store(Request $request)
    {
        // EDIT
        try {
            $travel = TravelClaim::find($request['id']);
            if ($travel->status === Status::rejected->value) {
                // Reset current approver to the 1st approver
                $approvers_list = json_decode($travel->approvers_id);
                $request['current_approver'] = reset($approvers_list);

                // Delete Notifications
                PortalNotification::where([
                    'model_name' => get_class($travel),
                    'model_id' => $travel->id,
                ])->delete();
            }
        }
        catch (QueryException $ex) {
                return response()->json([
                    'message' => 'Travel Claim Reset approver flow failed!',
                ], 400);
         }

         // STORE
         try {
            $this->assignApproverAndDepartment($request);

            $request['current_approver'] = $request['approvers_id'][0];
            $request['approvers_remark'] = array_fill(
                start_index: 0,
                count: count($request['approvers_id']),
                value: NULL,
            );

            // Reset status pending
            $request['status'] = Status::pending->value;
            // Set draft page to Allowance Page
            $request['index_page'] = 1;

            $data = $request->toArray();

            $updated = TravelClaim::findOrFail($request['id'])
                ->update(Arr::except($data, ['first_id', 'second_id']));

            if ($updated)
            {
                $travel = TravelClaim::find($request['id']);
                return (new TravelClaimResource($travel))
                    ->response()
                    ->setStatusCode(202);
            }

            return response()->json([
                'message' => 'Update Fail. Please try again',
            ], 400);
        }
        catch (Exception $ex) {
            return response()->json([
                'message' => 'Error: ' . $ex,
            ], 400);
        }
    }

    public function approvalIndex()
    {
        try {
            $pendingClaim =  TravelClaim::with(['user', 'department'])
                ->with(['allowances' => function ($query) {
                    $query->select(
                        'travel_id',
                        DB::raw('
                        CASE
                            WHEN allowance_type LIKE "Offshore%" THEN "Offshore"
                            WHEN allowance_type LIKE "Oversea%" THEN "Oversea"
                            WHEN allowance_type LIKE "Others%" THEN "Others"
                            ELSE allowance_type
                        END AS type'),
                        DB::raw('SUM(amount) as total_amount')
                    )
                        ->groupBy('travel_id', 'type')
                        ->get();
                }])
                ->with(['transports' => function ($query) {
                    $query->select(
                        'travel_id',
                        DB::raw('
                        CASE
                            WHEN transport_type LIKE "Mileage%" THEN "Mileage"
                        ELSE transport_type
                        END AS transport_type'),
                        DB::raw('SUM(amount) as total_amount')
                    )
                        ->groupBy('travel_id', 'transport_type')
                        ->get();
                }])
                ->with(['expenses' => function ($query) {
                    $query->select(
                        'travel_id',
                        DB::raw('
                        CASE
                            WHEN description LIKE "Telephone%"
                            OR description LIKE "Laundry%"
                            OR description LIKE "Others%"
                            THEN "Others"
                        ELSE description

                        END AS expense_type'),
                        DB::raw('SUM(amount) as total_amount')
                    )
                        ->groupBy('travel_id', 'expense_type')
                        ->get();
                }])
                ->where('current_approver', Auth::id())
                ->where(function ($query) {
                    $query->where('status', Status::pending->value)
                        ->orWhere('status', Status::processing->value);
                })
                ->orderBy('updated_at', 'desc')
                ->paginate(10);

            return TravelClaimResource::collection($pendingClaim);
        }
        catch (QueryException $ex) {
            return response()->json([
                'message' => 'Error: Invalid Query',
            ], 400);
        }
        catch(Exception $ex) {
            return response()->json([
                'message' => 'Error: ' . $ex,
            ], 400);
        }
    }

    public function approve(Request $request)
    {
        try {
            $response = $this->TravelClaimService->approveTravelClaim($request->toArray());

            return response()->json([
                'message' => 'Travel Claim status has been updated.'
            ], 202);
        }
        catch (Exception $ex) {
            return response()->json([
                'message' => 'Error during approval process...',
            ], 400);
        }
    }

    public function cancel(Request $request)
    {
        $travel = TravelClaim::find($request['travel_id']);
        $travel->update(['status' => Status::canceled->value]);

        return response()->json([
            'message' => 'Selected Travel Claim has been canceled.'
        ], 202);
    }

    public function summary(Request $request)
    {
        $response = $this->TravelClaimService->travelClaimSummary(
            data: $request->toArray()
        );

        return TravelClaimResource::collection($response);
    }

    public function reset(Request $request)
    {
        try {
            $travelID = $request->get('id');
            TravelClaim::find($travelID)->delete();

            return response()->json([
                'message' => 'Travel Claim data delete successful.'
            ], 201);
        }
        catch(ModelNotFoundException $exception) {
            return response()->json([
                'message' => 'Model Travel Not Found ID: ' . $request['id'],
            ], 400);
        }
    }

    public function notes(Request $request)
    {
        try {
            TravelClaim::findOrFail($request->get('id'))
                ->update([
                    'hr_note' => $request->get('hr_note')
                ]);

            return response()->json([
                'message' => 'Notes successfully added',
            ], 201);
        }
        catch (QueryException $ex) {
            return response()->json([
                'message' => 'Query Error: '. $ex,
            ], 400);
        }
        catch (Exception $exception) {
            return response()->json([
                'message' => 'Error, Please contact IT',
            ], 400);
        }
    }

    public function download(int $travelID)
    {
        try {
            $zip_file = storage_path('/app/travel-claim-attachment/'.$travelID.'/Travel_Claim_attachments.zip');

            // Initializing PHP class
            $zip = new \ZipArchive();
            if ($zip->open($zip_file, \ZipArchive::CREATE | \ZipArchive::OVERWRITE))
            {
                $path = storage_path('app/travel-claim-attachment/'.$travelID);

                // Recursively add files from subdirectories
                $this->addFilesToZip($path, $zip);

                $zip->close();

            }
            return response()->download($zip_file, 'Travel_Claim_attachments.zip', array('Content-Type: application/octet-stream','Content-Length: '. filesize($zip_file)))->deleteFileAfterSend(true);
        }
        catch(Exception $fileException) {
            return response()->json([
                'message' => 'No attachment available...',
            ], 404);
        }
    }

    private function addFilesToZip($path, $zip, $basePath = ''): void
    {
        $files = new \RecursiveIteratorIterator(new \RecursiveDirectoryIterator($path));
        foreach ($files as $name => $file) {
            if (!$file->isDir()) {
                $filePath = $file->getRealPath();
                $relativePath = $basePath . substr($filePath, strlen($path) + 1);
                $zip->addFile($filePath, $relativePath);
            }
        }
    }

    private function assignApproverAndDepartment(&$request)
    {
        $request['department_id'] = User::getDepartment(Auth::id())->id;

        $approvers = [];
        if (array_key_exists('first_id', $request->toArray()) ||
            array_key_exists('second_id', $request->toArray())) {

            //push first approver id into array if exist
            array_key_exists('first_id', $request->toArray()) && array_push($approvers, (int)$request['first_id']);
            //push second approver id into array if exist
            array_key_exists('second_id', $request->toArray()) && array_push($approvers, (int)$request['second_id']);

            //0->HR DEPARTMENT ,145->ALIAS, 179->TENGKU
            $request['approvers_id'] = array_merge($approvers, [0, 145, 179]);
        }
        else {
            $fa = FixedApprover::where('user_id', Auth::id())->firstOrFail();
            if (!$fa) {
                return response()->json([
                    'message' => 'Fixed Approvers Not Assigned, Contact HR!'
                ], 400);
            }

            $request['approvers_id'] = json_decode($fa->approvers_id);
        }
    }
}
