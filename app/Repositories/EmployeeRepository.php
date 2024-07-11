<?php

namespace App\Repositories;

use App\Lib\Enumerations\UserStatus;
use App\Model\Employee;
use Carbon\Carbon;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;

class EmployeeRepository
{

    public function incentive()
    {
        $results = ['Not Applicable', 'Applicable'];
        $options = ['' => '---- Please select ----'];
        foreach ($results as $key => $value) {
            $options[$key] = $value;
        }
        return $options;
    }
    public function salaryLimit()
    {
        $results = ['< 20000', '> 20000'];
        $options = ['' => '---- Please select ----'];
        foreach ($results as $key => $value) {
            $options[$key] = $value;
        }
        return $options;
    }
    public function workShift()
    {
        $results = ['General', 'Rotational'];
        $options = ['' => '---- Please select ----'];
        foreach ($results as $key => $value) {
            $options[$key] = $value;
        }
        return $options;
    }
    public function workHours()
    {
        $results = ['08:00', '12:00'];
        $options = ['' => '---- Please select ----'];
        foreach ($results as $key => $value) {
            $options[$key + 1] = $value;
        }

        return $options;
    }
    public function nationality()
    {
        $results = ['Omanis', 'Expats'];
        $options = ['' => '---- Please select ----'];
        foreach ($results as $key => $value) {
            $options[$key] = $value;
        }
        return $options;
    }
    public function religion()
    {
        $results = ['Muslim', 'Non-Muslim'];
        $options = ['' => '---- Please select ----'];
        foreach ($results as $key => $value) {
            $options[$key] = $value;
        }
        return $options;
    }
    public function makeEmployeeAccountDataFormat($data, $action = false)
    {

        $employeeAccountData['role_id'] = $data['role_id'];

        if ($action != 'update') {

            $employeeAccountData['password'] = Hash::make($data['password']);
            // $employeeAccountData['password'] = Hash::make($data['password']);
        }

        $employeeAccountData['user_name'] = $data['user_name'];

        $employeeAccountData['status'] = $data['status'];

        $employeeAccountData['created_by'] = 1;

        $employeeAccountData['updated_by'] = 1;

        return $employeeAccountData;
    }

    public function makeEmployeePersonalInformationDataFormat($data)
    {
        // dd($data);
        $employeeData['first_name'] = $data['first_name'];

        $employeeData['last_name'] = $data['last_name'];

        $employeeData['finger_id'] = $data['finger_id'];

        $employeeData['document_title8'] = $data['document_title8'];

        if (isset($data['document_file8'])) {
            $employeeData['document_name8'] = date('Y_m_d_H_i_s') . '_' . $data['document_file8']->getClientOriginalName();
        }
        $employeeData['document_title9'] = $data['document_title9'];

        if (isset($data['document_file9'])) {
            $employeeData['document_name9'] = date('Y_m_d_H_i_s') . '_' . $data['document_file9']->getClientOriginalName();
        }
        $employeeData['document_title10'] = $data['document_title10'];

        if (isset($data['document_file10'])) {
            $employeeData['document_name10'] = date('Y_m_d_H_i_s') . '_' . $data['document_file10']->getClientOriginalName();
        }
        $employeeData['document_title11'] = $data['document_title11'];

        if (isset($data['document_file11'])) {
            $employeeData['document_name11'] = date('Y_m_d_H_i_s') . '_' . $data['document_file11']->getClientOriginalName();
        }
        $employeeData['document_title16'] = $data['document_title16'];

        if (isset($data['document_file16'])) {
            $employeeData['document_name16'] = date('Y_m_d_H_i_s') . '_' . $data['document_file16']->getClientOriginalName();
        }

        $employeeData['document_title17'] = $data['document_title17'];

        if (isset($data['document_file17'])) {
            $employeeData['document_name17'] = date('Y_m_d_H_i_s') . '_' . $data['document_file17']->getClientOriginalName();
        }

        $employeeData['document_title18'] = $data['document_title18'];

        if (isset($data['document_file18'])) {
            $employeeData['document_name18'] = date('Y_m_d_H_i_s') . '_' . $data['document_file18']->getClientOriginalName();
        }

        $employeeData['document_title19'] = $data['document_title19'];

        if (isset($data['document_file19'])) {
            $employeeData['document_name19'] = date('Y_m_d_H_i_s') . '_' . $data['document_file19']->getClientOriginalName();
        }

        $employeeData['document_title20'] = $data['document_title20'];

        if (isset($data['document_file20'])) {
            $employeeData['document_name20'] = date('Y_m_d_H_i_s') . '_' . $data['document_file20']->getClientOriginalName();
        }
        $employeeData['document_title21'] = $data['document_title21'];

        if (isset($data['document_file21'])) {
            $employeeData['document_name21'] = date('Y_m_d_H_i_s') . '_' . $data['document_file21']->getClientOriginalName();
        }

        $employeeData['expiry_date8'] = dateConvertFormtoDB($data['expiry_date8']);
        $employeeData['expiry_date9'] = dateConvertFormtoDB($data['expiry_date9']);
        $employeeData['expiry_date10'] = dateConvertFormtoDB($data['expiry_date10']);
        $employeeData['expiry_date11'] = dateConvertFormtoDB($data['expiry_date11']);
        $employeeData['expiry_date16'] = dateConvertFormtoDB($data['expiry_date16']);
        $employeeData['expiry_date17'] = dateConvertFormtoDB($data['expiry_date17']);
        $employeeData['expiry_date18'] = dateConvertFormtoDB($data['expiry_date18']);
        $employeeData['expiry_date19'] = dateConvertFormtoDB($data['expiry_date19']);
        $employeeData['expiry_date20'] = dateConvertFormtoDB($data['expiry_date20']);
        $employeeData['expiry_date21'] = dateConvertFormtoDB($data['expiry_date21']);

        $employeeData['department_id'] = $data['department_id'];

        $employeeData['designation_id'] = $data['designation_id'];

        $employeeData['branch_id'] = $data['branch_id'];

        $employeeData['supervisor_id'] = $data['supervisor_id'];

        $employeeData['operation_manager_id'] = $data['operation_manager_id'];

        $employeeData['special_allowance'] = $data['special_allowance'];

        $employeeData['mobile_allowance'] = $data['mobile_allowance'];

        $employeeData['living_allowance'] = $data['living_allowance'];

        $employeeData['transport_allowance'] = $data['transport_allowance'];

        $employeeData['utility_allowance'] = $data['utility_allowance'];

        $employeeData['housing_allowance'] = $data['housing_allowance'];

        $employeeData['increment'] = $data['increment'];

        $employeeData['basic_salary'] = $data['basic_salary'];

        $employeeData['account_holder'] = $data['account_holder'];

        $employeeData['name_of_the_bank'] = $data['name_of_the_bank'];

        $employeeData['ifsc_number'] = $data['ifsc_number'];

        $employeeData['account_number'] = $data['account_number'];

        $employeeData['pay_grade_id'] = $data['pay_grade_id'];

        $employeeData['hourly_salaries_id'] = $data['hourly_salaries_id'];

        $employeeData['email'] = $data['email'];

        $employeeData['date_of_birth'] = dateConvertFormtoDB($data['date_of_birth']);

        $employeeData['date_of_joining'] = dateConvertFormtoDB($data['date_of_joining']);

        $employeeData['date_of_leaving'] = dateConvertFormtoDB($data['date_of_leaving']);

        $employeeData['marital_status'] = $data['marital_status'];

        $employeeData['address'] = $data['address'];

        $employeeData['emergency_contacts'] = $data['emergency_contacts'];

        $employeeData['gender'] = $data['gender'] == 'Male' ? 0 : 1;

        $employeeData['religion'] = $data['religion'];

        $employeeData['nationality'] = $data['nationality'];

        $employeeData['country'] = $data['country'];

        $employeeData['phone'] = $data['phone'];

        $employeeData['status'] = $data['status'];

        $employeeData['prem_others'] = $data['prem_others'];

        $employeeData['created_by'] = 1;

        $employeeData['updated_by'] = 1;

        // 01-03
        $employeeData['ip_attendance'] = $data['ip_attendance'];

        $employeeData['employee_category'] = $data['employee_category'];

        // 05-03
        $employeeData['prem_others'] = $data['prem_others'];

        $employeeData['education_and_club_allowance'] = $data['education_and_club_allowance'];

        $employeeData['membership_allowance'] = $data['membership_allowance'];

        $employeeData['mobile_attendance'] = $data['mobile_attendance'];

        return $employeeData;
    }

    public function makeEmployeeEducationDataFormat($data, $employee_id, $action = false)
    {

        $educationData = [];

        if (isset($data['institute'])) {

            for ($i = 0; $i < count($data['institute']); $i++) {

                $educationData[$i] = [

                    'employee_id' => $employee_id,

                    'institute' => $data['institute'][$i],

                    'board_university' => $data['board_university'][$i],

                    'degree' => $data['degree'][$i],

                    'passing_year' => $data['passing_year'][$i],

                    'result' => $data['result'][$i],

                    'cgpa' => $data['cgpa'][$i],

                ];

                if ($action == 'update') {

                    $educationData[$i]['educationQualification_cid'] = $data['educationQualification_cid'][$i];
                }
            }
        }

        return $educationData;
    }

    public function makeEmployeeExperienceDataFormat($data, $employee_id, $action = false)
    {

        $experienceData = [];

        if (isset($data['organization_name'])) {

            for ($i = 0; $i < count($data['organization_name']); $i++) {

                $experienceData[$i] = [

                    'employee_id' => $employee_id,

                    'organization_name' => $data['organization_name'][$i],

                    'designation' => $data['designation'][$i],

                    'from_date' => dateConvertFormtoDB($data['from_date'][$i]),

                    'to_date' => dateConvertFormtoDB($data['to_date'][$i]),

                    'responsibility' => $data['responsibility'][$i],

                    'skill' => $data['skill'][$i],

                ];

                if ($action == 'update') {

                    $experienceData[$i]['employeeExperience_cid'] = $data['employeeExperience_cid'][$i];
                }
            }
        }

        return $experienceData;
    }

    public function bonusDayEligibility()
    {

        $employees = Employee::select(DB::raw('CONCAT(COALESCE(employee.first_name,\'\'),\' \',COALESCE(employee.last_name,\'\')) AS fullName'), 'designation_name', 'department_name', 'date_of_joining', 'date_of_leaving', 'finger_id', 'employee_id', 'branch_name')
            ->join('designation', 'designation.designation_id', 'employee.designation_id')
            ->join('department', 'department.department_id', '=', 'employee.department_id')
            ->join('branch', 'branch.branch_id', '=', 'employee.branch_id')
            ->where('status', UserStatus::$ACTIVE)->where("date_of_joining", "<=", Carbon::now()->subMonths(24))->orderBy('date_of_joining', 'asc')->get();
        $dataFormat = [];
        $tempArray = [];
        if (count($employees) > 0) {
            foreach ($employees as $employee) {
                $tempArray['date_of_joining'] = $employee->date_of_joining;
                $tempArray['date_of_leaving'] = $employee->date_of_leaving;
                $tempArray['employee_id'] = $employee->employee_id;
                $tempArray['designation_name'] = $employee->designation_name;
                $tempArray['fullName'] = $employee->fullName;
                $tempArray['phone'] = $employee->phone;
                $tempArray['finger_id'] = $employee->finger_id;
                $tempArray['department_name'] = $employee->department_name;
                $tempArray['branch_name'] = $employee->branch_name;

                $dataFormat[$employee->employee_id][] = $tempArray;
            }
        } else {
            $tempArray['status'] = 'No Data Found';
            $dataFormat[] = $tempArray['status'];
        }
        return $dataFormat;
    }
}
