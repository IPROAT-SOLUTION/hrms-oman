<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class ApproveOverTimeRequest extends FormRequest
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
        return [
            'finger_print_id' => 'required',
            'date' => 'required',
            'over_time' => 'required',
            'approved_over_time' => 'required',
           
        ];
    }

    public function messages()
    {
        return [
            'finger_print_id.required' => 'Employee Finger Id field is required.',
            'date.required' => 'The  date field is required.',
            'over_time.required' => 'The OverTime field is required.',
            'approved_over_time.required' => 'The Approved OverTime field is required.',
            
        ];
    }
}
