<?php

namespace App\Http\Requests;

use App\Model\Employee;
use Illuminate\Foundation\Http\FormRequest;

class EmployeeRequest extends FormRequest
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
        if (isset($this->employee)) {
            $result = Employee::where('employee_id', $this->employee)->first();
            return [
                'role_id'        => 'required',
                'user_name'      => 'required|regex:/^\S*$/u|unique:user,user_name,' . $result->user_id . ',user_id',
                'first_name'     => 'required',
                'finger_id'      => 'required|regex:/^\S*$/u|unique:employee,finger_id,' . $this->employee . ',employee_id',
                'department_id'  => 'required',
                'designation_id' => 'required',
                // 'work_hours' => 'required',
                // 'hr_id' => 'required',
                'operation_manager_id' => 'required',
                'supervisor_id' => 'required',
                // 'work_shift'     => 'required',
                'email'             => 'required|unique:employee,email,' . $this->employee . ',employee_id',
                // 'phone'             => 'required',
                'gender'         => 'required',
                // 'date_of_birth'     => 'required',
                // 'date_of_joining'   => 'required',
                'status'         => 'required',
                // 'institute.*'       => 'required',
                // 'board_university.*'=> 'required',
                // 'degree.*'          => 'required',
                // 'passing_year.*'    => 'required',
                'designation.*'  => 'required',
                // 'organization_name.*' => 'required',
                // 'from_date.*'      => 'required',
                // 'to_date.*'        => 'required',
                // 'responsibility.*' => 'required',
                // 'skill.*'          => 'required',
                'photo'          => 'mimes:jpeg,jpg,png|max:200',
                'ip_attendance'  => 'required',
                'country'         => 'required',

            ];
        }
        return [
            'role_id'        => 'required',
            'user_name'      => 'required|unique:user|regex:/^\S*$/u',
            'password'       => 'required|confirmed',
            'first_name'     => 'required',
            'finger_id'      => 'required|unique:employee|regex:/^\S*$/u',
            'department_id'  => 'required',
            'designation_id' => 'required',
            // 'operation_manager_id' => 'required',
            'supervisor_id' => 'required',
            // 'work_hours' => 'required',
            // 'work_shift'       => 'required',
            'email'               => 'required|unique:employee',
            // 'phone'               => 'required',
            'gender'         => 'required',
            // 'date_of_birth'       => 'required',
            // 'date_of_joining'     => 'required',
            'status'         => 'required',
            // 'institute.*'         => 'required',
            // 'board_university.*'  => 'required',
            // 'degree.*'            => 'required',
            // 'passing_year.*'      => 'required',
            'designation.*'  => 'required',
            // 'organization_name.*' => 'required',
            // 'from_date.*'         => 'required',
            // 'to_date.*'           => 'required',
            // 'responsibility.*'    => 'required',
            // 'skill.*'             => 'required',
            'photo'          => 'mimes:jpeg,jpg,png|max:200',
            'ip_attendance'  => 'required',
            'education_and_club_allowance'  => 'nullable',
            'membership_allowance'  => 'nullable',
            'country'         => 'required',
        ];
    }

    public function messages()
    {
        return [
            'role_id.required'            => 'The role field is required.',
            'institute*.required'         => 'The institute field is required.',
            'board_university*.required'  => 'The board university field is required.',
            'degree*.required'            => 'The degree field is required.',
            'passing_year*.required'      => 'The passing year field is required.',
            'organization_name*.required' => 'The organization name field is required.',
            'from_date*.required'         => 'The from date field is required.',
            'to_date*.required'           => 'The to date field is required.',
            // 'work_shift*.required'           => 'The Work Shift field is required.',
            // 'work_hours*.required'           => 'The Work hour field is required.',
            // 'hr_id*.required'           => 'The Hr Name field is required.',

            // 'supervisor_id*.required'           => 'The HOD field is required.',
            'email*.required'           => 'The email field is required.',
            'ip_attendance.required'           => 'The ip attendance field is required.',
            'education_and_club_allowance'  => 'nullable',
            'membership_allowance'  => 'nullable',
        ];
    }
}
