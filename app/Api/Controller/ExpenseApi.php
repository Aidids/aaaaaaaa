<?php

namespace App\Api\Controller;

use App\Http\Controllers\Controller;
use App\Http\Requests\GeneralAttachmentRequest;
use App\Http\Resources\ExpenseResource;
use App\Http\Traits\AttachmentTrait;
use App\Models\Expense;
use App\Models\TravelClaim;
use Exception;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Illuminate\Database\QueryException;
use Illuminate\Http\Request;

class ExpenseApi extends Controller
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

        $index = Expense::where('travel_id', $travelId)
            ->paginate(10);

        return ExpenseResource::collection($index);
    }

    public function add(int $travelId, Request $request)
    {
        $storeSuccess = $this->storeExpense($travelId, $request);

        if (! $storeSuccess)
        {
            return response()->json([
                'message' => 'Fail updating expense total, please contact IT. Travel ID: ' . $travelId,
            ], 400);
        }

        try {
            $expense = Expense::create(['travel_id' => $travelId]);
        }
        catch(QueryException $ex) {
            return response()->json([
                'message' => 'Travel ID not found',
            ], 400);
        }

        return (new ExpenseResource($expense))
            ->response()
            ->setStatusCode(201);
    }

    public function store(int $travelId, Request $request)
    {
        $storeSuccess = $this->storeExpense($travelId, $request);

        if (! $storeSuccess)
        {
            return response()->json([
                'message' => 'Fail updating expense total, please contact IT. Travel ID: ' . $travelId,
            ], 400);
        }

        $total = Expense::where('travel_id', $travelId)->sum('amount');

        try {
            TravelClaim::findOrfail($travelId)->update([
                'isDraft' => 0,
                'total_expense' => ($total) ?: 0,
                'index_page' => 0,
            ]);
        }
        catch (ModelNotFoundException $ex) {
            return response()->json([
                'message' => 'Fail updating expense total, please contact IT. Travel ID: ' . $travelId,
            ], 400);
        }

        return response()->json([
            'message' => 'Expense saved',
        ], 201);
    }

    public function delete(Request $request)
    {
        try{
            $expense = Expense::findOrFail($request->get('id'));
        }
        catch(ModelNotFoundException $ex){
            return response()->json([
                'message' => 'Could not delete expense . [id: '. $request->get('id') .'], please contact IT',
            ], 400);
        }

        $expense->delete();

        return ($expense) ?
            response()->json(['message' => 'Expense deleted.'],
                201)
            :
            response()->json([
                'message' => 'Expense deletion error. Please contact IT'],
                404);
    }

    public function addAttachment(int $expenseId, GeneralAttachmentRequest $request)
    {
        try {
            $expense = Expense::findOrFail($expenseId);
        }
        catch (ModelNotFoundException $ex) {
            return response()->json([
                'message' => 'Expense ID not found',
            ], 400);
        }

        $uploadStatus = $this->addLiveAttachment(
            model: $expense,
            file: $request->file('file'),
            directory: 'expense',
        );

        return ($uploadStatus) ?
            (new ExpenseResource(Expense::find($expense->id)))
                ->additional([
                    'message' => 'Attachment uploaded'
                ])
            :
            response()->json([
                'message' => 'Fail uploading attachment. Please try again',
            ], 400);
    }

    public function deleteAttachment(int $expenseId)
    {
        try {
            $expense = Expense::findOrFail($expenseId);
        }
        catch (ModelNotFoundException $ex) {
            return response()->json([
                'message' => 'Expense ID not found',
            ], 400);
        }

        $deleted = $this->deleteLiveAttachment(
          model: $expense,
            directory: 'expense',
        );

        return ($deleted) ?
            response()->json(['message' => 'Attachment deleted'],
                201)
            :
            response()->json(['message' => 'Attachment not found, Please try again'],
                404);
    }

    private function storeExpense(int $travelId, Request $request)
    {
        if ($request['expense'] == NULL) {
            return true;
        }

        foreach ($request->get('expense') as $json) {
            $item = json_decode($json, true);

            ($item['description'] === 'Others') &&
            $item['description'] = $item['description'] .' ('. $item['description_name'] .')';

            try {
                Expense::find($item['id'])->update([
                    'travel_id' => $travelId,
                    'description' => $item['description'],
                    'account_code' => $item['account_code'],
                    'total_hours' => $item['total_hours'],
                    'amount' => $item['amount'],
                    'remark' => $item['remark'],
                ]);
            }
            catch(QueryException|Exception $ex) {
                return false;
            }
        }

        return true;
    }
}
