<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class ApproveLeaveRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     *
     * @return bool
     */
    public function authorize()
    {
        return true;
    }

    public function rules()
    {
        return [
            'id' => 'required',
            'first_approver_id' => 'nullable',
            'first_approver_status' => 'nullable',
            'first_approver_remark' => 'nullable',
            'first_approver_date' => 'nullable',
            'second_approver_id' => 'nullable',
            'second_approver_status' => 'nullable',
            'second_approver_remark' => 'nullable',
            'second_approver_date' => 'nullable',
        ];
    }

    public function messages()
    {
        return [
            'id.required' => 'Please select leave to approve'
        ];
    }
}
