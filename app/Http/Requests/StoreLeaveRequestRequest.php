<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use finfo;

class StoreLeaveRequestRequest extends FormRequest
{
    public function authorize()
    {
        return true;
    }

    public function rules()
    {
        return [
            'id' => 'nullable',
            'leave_balance_id' => 'required',
            'start_date' => 'required',
            'start_date_type' => 'required',
            'end_date' => 'required',
            'end_date_type' => 'required',
            'duration' => 'required',
            'leave_type' => 'nullable',
            'compassionate_type' => 'nullable',
            'reason' => 'nullable',
            'first_approver_id' => 'nullable',
            'second_approver_id' => 'nullable',
            'file_id' => 'nullable',
            'file' => [
                'nullable',
                'file',
                'max:5120',
                'mimes:pdf,png,jpeg,jpg,eml,msg'
            ],
        ];
    }

    public function messages()
    {
        return [
            'leave_balance_id.required' => 'Leave type is not selected',
            'start_date.required' => 'Please select the start date for your leave',
            'end_date.required' => 'Please select the end date for your leave',
            'duration.required' => 'Please enter the duration of your leave',
            'file.max' => 'Opps, file size exceeds 5mb. Please choose a different file',
        ];
    }
}
