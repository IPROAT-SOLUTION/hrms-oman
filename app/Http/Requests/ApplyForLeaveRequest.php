<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class ApplyForLeaveRequest extends FormRequest
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

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array
     */
    public function rules()
    {
        $rules = [
            'leave_type_id'         => 'required',
            'application_from_date' => 'required',
            'application_to_date'   => 'required',
            'number_of_day'         => 'required|numeric',
            'purpose'               => 'required',
        ];
    
        if ($this->input('leave_type_id') != 7) {
            $rules['document'] = 'required|mimes:jpeg,jpg,png,gif,csv,txt,pdf';
        }
    
        return $rules;
    }
    
    public function messages()
    {
        return [
            'leave_type_id.required'         => 'The leave type field is required.',
            'application_from_date.required' => 'The from date field is required.',
            'application_to_date.required'   => 'The to date field is required.',
            'document.required'   => 'The document field is required must be jpeg,jpg,png,gif,csv,txt,pdf',
        ];
    }
}
