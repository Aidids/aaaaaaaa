<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class StoreRedeemReplacementLeaveRequest extends FormRequest
{
    public function authorize()
    {
        return true;
    }

    public function rules()
    {
        return [
            'id' => 'nullable',
            'leave_request_id' => 'nullable',
            'start_date' => 'required',
            'end_date' => 'required',
            'remark' => 'nullable',
            'first_approver_id' => 'nullable',
            'second_approver_id' => 'nullable',
            'file' => [
                'nullable',
                'max:5120',
                'mimes:image/*, application/pdf, eml, msg',
            ],
        ];
    }

    public function messages()
    {
        return [
            'start_date.required' => 'Please select the start date for your leave',
            'end_date.required' => 'Please select the end date for your leave',
        ];
    }
}
