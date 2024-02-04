<?php

namespace App\Http\Resources;

use App\Http\Traits\ApproverTrait;
use App\Models\TravelClaim;
use App\Models\User;
use Carbon\Carbon;
use Illuminate\Http\Resources\Json\JsonResource;

class TravelClaimResource extends JsonResource
{
    use ApproverTrait;
    /**
     * Transform the resource into an array.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return array|\Illuminate\Contracts\Support\Arrayable|\JsonSerializable
     */
    public function toArray($request)
    {
        return [
            'id' => $this->id,

            'submission_month' => ($this->submission_month)
                ? Carbon::createFromFormat('Y-m-d', $this->submission_month)->format('d F Y')
                : null,
            'status' => $this->status,
            'isDraft' => (bool)$this->isDraft,
            'index_page' => $this->index_page,

            'total_allowance' => (double)$this->total_allowance,
            'total_transport' => (double)$this->total_transport,
            'total_expense' => (double)$this->total_expense,
            'custom_approver' => $this->custom_approver,
            'approver_array' => json_decode($this->approvers_id),

            'created_at' => $this->created_at,

            'requester' => new UserResource($this->whenLoaded('user')),
            'department' => new DepartmentResource($this->whenLoaded('department')),
            'allowances' => AllowanceGraphResource::collection($this->allowances),
            'transports' => TransportGraphResource::collection($this->transports),
            'expenses' => ExpenseGraphResource::collection($this->expenses),
            'first_id' => ($this->approvers_id && $this->custom_approver)
                ? $this->approverDisplay(approverList: json_decode($this->approvers_id),level: 1)
                : null,

            'second_id' => ($this->approvers_id && $this->custom_approver)
                ? $this->approverDisplay(approverList: json_decode($this->approvers_id),level: 2)
                : null,

            'current_approver' => ($this->current_approver)
                ? $this->currentApproverDetail($this->current_approver)
                : null,
            'approvers' => (json_decode($this->approvers_id) !== null)
                ? $this->getApproverDetail(json_decode($this->approvers_id))
                : NULL,
            'approvers_remark' => json_decode($this->approvers_remark),
        ];
    }

    private function approverDisplay(Array $approverList, int $level)
    {
        $length = count($approverList);

        if ($length === 4) {
            $approvers = User::select('id', 'name')
                ->where('approver_id', $level)
                ->find($approverList[0]);

            if ($approvers)
            {
                return $approvers;
            }

            return null;
        }
        else if($length === 5) {
            if ($level === 1) {
                $approvers = User::select('id', 'name')->find($approverList[0]);
            }
            else if($level === 2) {
                $approvers = User::select('id', 'name')->find($approverList[1]);
            }

            return $approvers;
        }

        return null;
    }
}
