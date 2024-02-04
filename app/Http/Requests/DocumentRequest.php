<?php

namespace App\Http\Requests;

use Illuminate\Contracts\Validation\Validator;
use Illuminate\Foundation\Http\FormRequest;

class DocumentRequest extends FormRequest
{
    public $validator = null;

    /**
     * Determine if the user is authorized to make this request.
     *
     * @return bool
     */
    public function authorize()
    {
        return true;
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, mixed>
     */
    public function rules()
    {
        return [
            'id' => 'nullable',
            'name' => 'required|max:100|unique:documents,name,'.$this->user()->id,
            'file' => 'required|mimes:pdf,msg,eml|max:5120',
        ];
    }

    protected function failedValidation(Validator $validator)
    {
        $this->validator = $validator;
    }
}
