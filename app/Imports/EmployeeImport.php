<?php

namespace App\Imports;

use App\User;
use App\Model\Role;
use App\Model\Branch;
use App\Model\Employee;
use App\Model\LeaveType;
use App\Model\Department;
use App\Model\Designation;
use App\Model\EmpLeaveBalance;
use App\Lib\Enumerations\UserStatus;
use Illuminate\Support\Facades\Hash;
use Maatwebsite\Excel\Concerns\ToModel;
use Maatwebsite\Excel\Concerns\WithLimit;
use Maatwebsite\Excel\Concerns\Importable;
use Maatwebsite\Excel\Concerns\WithStartRow;
use Maatwebsite\Excel\Concerns\WithValidation;
use Maatwebsite\Excel\Concerns\WithBatchInserts;
use Maatwebsite\Excel\Concerns\WithChunkReading;

class EmployeeImport implements ToModel, WithValidation, WithStartRow, WithChunkReading, WithBatchInserts
{
    use Importable;

    private $data;

    public function __construct(array $data = [])
    {
        $this->data = $data;
    }

    public function rules(): array
    {
        return [
            '*.0' => 'required', #A SL.NO
            '*.1' => 'required|regex:/^\S*$/u', #B User Name
            '*.2' => 'required|exists:role,role_name', #C Role Name
            '*.3' => 'required|string', #D Employee Code
            '*.4' => 'required|exists:department,department_name', #E Department
            '*.5' => 'required|exists:designation,designation_name', #F Designation
            '*.6' => 'required|exists:branch,branch_name', #G Branch
            '*.7' => 'required|exists:user,user_name', #H HR
            '*.8' => 'required|exists:user,user_name', #I Manager
            '*.9' => 'nullable', #J Phone
            '*.10' => 'nullable', #K Email
            '*.11' => 'required', #L First Name
            '*.12' => 'nullable', #M Last Name
            '*.13' => 'nullable', #N DOB
            '*.14' => 'nullable', #O DOJ
            '*.15' => function ($attribute, $value, $onFailure) {
                $value = trim($value);
                $arr = [null, 'Male', 'Female'];
                if (!in_array($value, $arr)) {
                    $onFailure('Gender is invalid, it should be Male/Female');
                }
            }, #P Gender
            '*.16' => function ($attribute, $value, $onFailure) {
                $value = trim($value);
                $arr = [null, 'Married', 'Unmarried', 'NoDisclosure'];
                if (!in_array($value, $arr)) {
                    $onFailure('Martial Status is invalid, it should be Married/Unmarried/NoDisclosure');
                }
            }, #Q Maritals
            '*.17' => 'nullable', #R Address
            '*.18' => 'nullable', #S Emergency Contact
            '*.19' => function ($attribute, $value, $onFailure) {
                $value = trim($value);
                $arr = [null, 'Muslim', 'Non-Muslim'];
                if (!in_array($value, $arr)) {
                    $onFailure('Religion is invalid, it should be Muslim/Non-Muslim');
                }
            }, #T Religion
            '*.20' => function ($attribute, $value, $onFailure) {
                $value = trim($value);
                $arr = [null, 'Omanis', 'Expats'];
                if (!in_array($value, $arr)) {
                    $onFailure('Nationality is invalid, it should be Omanis/Expats');
                }
            }, #U Nationality
            '*.21' => function ($attribute, $value, $onFailure) {
                $value = trim($value);
                $arr = [null, 'Employee', 'Chiefs'];
                if (!in_array($value, $arr)) {
                    $onFailure('Category is invalid, it should be Employee/Chiefs');
                }
            }, #V Category
            '*.22' => 'required|string', #W Country
            '*.46' => 'nullable', #AU DOL
            '*.47' => function ($attribute, $value, $onFailure) {
                $value = trim($value);
                $arr = [null, 'Active', 'In-Active', 'Terminated', 'Resigned'];
                if (!in_array($value, $arr)) {
                    $onFailure('Employee Status is invalid, it should be Active/In-Active/Terminated/Resigned');
                }
            }, #AV Status
            '*.49' => function ($attribute, $value, $onFailure) {
                $value = trim($value);
                $arr = [null, 'Yes', 'No'];
                if (!in_array($value, $arr)) {
                    $onFailure('IP Attendance is invalid, it should be Yes/No');
                }
            }, #AX Ip Attendance
            '*.50' => function ($attribute, $value, $onFailure) {
                $value = trim($value);
                $arr = [null, 'Yes', 'No'];
                if (!in_array($value, $arr)) {
                    $onFailure('mobile Attendance is invalid, it should be Yes/No');
                }
            }, #AY Mobile Attendance
        ];
    }

    public function customValidationMessages()
    {
        return [
            '0.required' => 'Sr.No is required',
            '1.required' => 'User name is required',
            '2.required' => 'Role name should be same as the name provided in Master',
            '3.required' => 'Finger Print Id / Employee Code is required (ie: Device Unique id) ',
            '4.required' => 'Department Name should be same as the name provided in Master',
            '5.required' => 'Designation Name should be same as the name provided in Master',
            '6.required' => 'Branch Name should be same as the name provided in Master',
            '7.required' => 'HOD Name should be same as the  user name provided in Master',
            '8.required' => 'Operation Manager Name should be same as the  user name provided in Master',
            '10.nullable' => 'Email is required',
            '11.required' => 'Employee first name is required',
            '12.required' => 'Employee last name is required',
            '22.required' => 'Country field is required',

            '1.unique' => 'Username should be unique',
            '1.regex' => 'Space not allowed in Username',
            '2.exists' => 'Role name doest not exists',
            '3.string' => "Employee code must be in text format (add single codes in the leading edge when its contains only numbers)",
            '4.exists' => 'Department name doest not exists',
            '5.exists' => 'Designation name doest not exists',
            '6.exists' => 'Branch name doest not exists',
            '7.exists' => 'HR user name doest not exists',
            '8.exists' => 'Manager user name doest not exists',
            '22.string' => 'Country name should be text',
        ];
    }

    public function model(array $row)
    {

        $dataUpdate = $dataInsert = false;
        $dob = $doj = $dol = "0000-00-00";
        $passportExpiryDate = $visaExpiryDate = $drivingExpiryDate = $civilExpiryDate = null;

        $checkEmployee = Employee::where('finger_id', $row[3])->first();

        if ($checkEmployee) {
            $checkUser = User::where('user_id', $checkEmployee->user_id)->first();
            $dataUpdate = true;
        } else {
            $dataInsert = true;
        }

        if ($row[13]) {
            try {
                $dob = \PhpOffice\PhpSpreadsheet\Shared\Date::excelToDateTimeObject($row[13])->format('Y-m-d');
            } catch (\Throwable $th) {
                $dob = date('Y-m-d', strtotime($row[13]));
            }
        }

        if ($row[14]) {
            try {
                $doj = \PhpOffice\PhpSpreadsheet\Shared\Date::excelToDateTimeObject($row[14])->format('Y-m-d');
            } catch (\Throwable $th) {
                $doj = date('Y-m-d', strtotime($row[14]));
            }
        }
        if ($row[46]) {
            try {
                $dol = \PhpOffice\PhpSpreadsheet\Shared\Date::excelToDateTimeObject($row[15])->format('Y-m-d');
            } catch (\Throwable $th) {
                $dol = date('Y-m-d', strtotime($row[15]));
            }
        }
        if ($row[24]) {
            try {
                $passportExpiryDate = \PhpOffice\PhpSpreadsheet\Shared\Date::excelToDateTimeObject($row[24])->format('Y-m-d');
            } catch (\Throwable $th) {
                $passportExpiryDate = date('Y-m-d', strtotime($row[24]));
            }
        }
        if ($row[26]) {
            try {
                $visaExpiryDate = \PhpOffice\PhpSpreadsheet\Shared\Date::excelToDateTimeObject($row[26])->format('Y-m-d');
            } catch (\Throwable $th) {
                $visaExpiryDate = date('Y-m-d', strtotime($row[26]));
            }
        }
        if ($row[28]) {
            try {
                $drivingExpiryDate = \PhpOffice\PhpSpreadsheet\Shared\Date::excelToDateTimeObject($row[28])->format('Y-m-d');
            } catch (\Throwable $th) {
                $drivingExpiryDate = date('Y-m-d', strtotime($row[28]));
            }
        }

        if ($row[30]) {
            try {
                $civilExpiryDate = \PhpOffice\PhpSpreadsheet\Shared\Date::excelToDateTimeObject($row[30])->format('Y-m-d');
            } catch (\Throwable $th) {
                $civilExpiryDate = date('Y-m-d', strtotime($row[30]));
            }
        }

        $role = Role::where('role_name', $row[2])->first();
        $dept = Department::where('department_name', $row[4])->first();
        $designation = Designation::where('designation_name', $row[5])->first();

        if (isset($row[7]) && isset($row[8])) {
            $hod = User::where('user_name', $row[7])->first();
            $manager = User::where('user_name', $row[8])->first();
            $emp = Employee::where('user_id', $hod->user_id)->first();
            $manager = Employee::where('user_id', $manager->user_id)->first();
        }

        $branch = Branch::where('branch_name', $row[6])->first();

        if ($row[47] == 'In-Active') {
            $usr_status = UserStatus::$INACTIVE;
        } elseif ($row[47] == 'Terminated') {
            $usr_status = UserStatus::$TERMINATE;
        } elseif ($row[47] == 'Resigned') {
            $usr_status = UserStatus::$RESIGNED;
        } else {
            $usr_status = UserStatus::$ACTIVE;
        }

        if ($dataInsert) {

            $userData = User::create([
                'user_name' => $row[1],
                'role_id' => $role->role_id,
                'password' => Hash::make('demo1234'),
                'status' => $usr_status,
                'created_by' => auth()->user()->user_id,
                'updated_by' => auth()->user()->user_id,
            ]);
            $employeeData = Employee::create([
                'user_id' => $userData->user_id,
                'finger_id' => $row[3],
                'department_id' => $dept->department_id,
                'designation_id' => $designation->designation_id,
                'branch_id' => $branch->branch_id,
                'supervisor_id' => isset($emp->employee_id) ? $emp->employee_id : 1,
                'operation_manager_id' => isset($manager->employee_id) ? $manager->employee_id : 1,
                'phone' => $row[9],
                'email' => $row[10],
                'first_name' => $row[11],
                'last_name' => $row[12],
                'date_of_birth' => $dob,
                'date_of_joining' => $doj,
                'gender' => $row[15] == 'Male' ? 0 : 1,
                'marital_status' => $row[16],
                'address' => $row[17],
                'emergency_contacts' => $row[18],
                'religion' => $row[19] == 'Muslim' ? 0 : 1,
                'nationality' => $row[20] == 'Omanis' ? 0 : 1,
                'employee_category' => $row[21] == 'Employee' ? 0 : 1,
                'country' => $row[22],
                'document_title8' => $row[23],
                'expiry_date8' => $passportExpiryDate,
                'document_title9' => $row[25],
                'expiry_date9' => $visaExpiryDate,
                'document_title10' => $row[27],
                'expiry_date10' => $drivingExpiryDate,
                'document_title11' => $row[29],
                'expiry_date11' => $civilExpiryDate,
                'account_number' => $row[31],
                'ifsc_number' => $row[32],
                'name_of_the_bank' => $row[33],
                'account_holder' => $row[34],
                'basic_salary' => $row[35],
                'increment' => $row[36],
                'housing_allowance' => $row[37],
                'utility_allowance' => $row[38],
                'transport_allowance' => $row[39],
                'living_allowance' => $row[40],
                'mobile_allowance' => $row[41],
                'special_allowance' => $row[42],
                'prem_others' => $row[43],
                'education_and_club_allowance' => $row[44], # Education allowance
                'membership_allowance' => $row[45], #Club and Membership Allowance
                'date_of_leaving' => $dol,
                'status' => $usr_status,
                'status_remark' => $row[48],
                'ip_attendance' => $row[49] == 'Yes' ? 1 : 0,
                'mobile_attendance' => $row[50] == 'Yes' ? 1 : 0,
                'created_by' => auth()->user()->user_id,
                'updated_by' => auth()->user()->user_id,
            ]);

            $employeeList = LeaveType::where('status', 1)->get();

            foreach ($employeeList as $leaveType) {

                $employee = Employee::where('employee_id', $employeeData->employee_id)->first();

                if (isset($employee) && isset($leaveType)) {
                    $status = 0;

                    // Check Eligibility OR Common for all
                    if (($leaveType->nationality == $employee->nationality || $leaveType->nationality == 2)
                        && ($leaveType->religion == $employee->religion || $leaveType->religion == 2)
                        && ($leaveType->gender == $employee->gender || $leaveType->gender == 2)
                    ) {
                        $status = 1;
                    }

                    if ($status) {
                        if ($leaveType->leave_type_id != 7) {
                            EmpLeaveBalance::create([
                                'employee_id' => $employeeData->employee_id,
                                'finger_id' => $employeeData->finger_id,
                                'branch_id' => $employeeData->branch_id,
                                'department_id' => $employeeData->department_id,
                                'designation_id' => $employeeData->designation_id,
                                'leave_balance' => $leaveType->num_of_day,
                                'leave_type_id' => $leaveType->leave_type_id,
                            ]);
                        } else {
                            EmpLeaveBalance::create([
                                'employee_id' => $employeeData->employee_id,
                                'finger_id' => $employeeData->finger_id,
                                'branch_id' => $employeeData->branch_id,
                                'department_id' => $employeeData->department_id,
                                'designation_id' => $employeeData->designation_id,
                                'leave_balance' => 0,
                                'leave_type_id' => $leaveType->leave_type_id,
                            ]);
                        }
                    }
                }
            }
        }


        if ($dataUpdate) {
            $userData = User::where('user_id', $checkUser->user_id)->update([
                'user_name' => $row[1],
                'role_id' => $role->role_id,
                'status' => $usr_status,
                'created_by' => auth()->user()->user_id,
                'updated_by' => auth()->user()->user_id,
            ]);

            $employeeData = Employee::where('employee_id', $checkEmployee->employee_id)->update([
                'user_id' => $checkUser->user_id,
                'finger_id' => $row[3],
                'department_id' => $dept->department_id,
                'designation_id' => $designation->designation_id,
                'branch_id' => $branch->branch_id,
                'supervisor_id' => isset($emp->employee_id) ? $emp->employee_id : 1,
                'operation_manager_id' => isset($manager->employee_id) ? $manager->employee_id : 1,
                'phone' => $row[9],
                'email' => $row[10],
                'first_name' => $row[11],
                'last_name' => $row[12],
                'date_of_birth' => $dob,
                'gender' => $row[15] == 'Male' ? 0 : 1,
                'marital_status' => $row[16],
                'address' => $row[17],
                'emergency_contacts' => $row[18],
                'religion' => $row[19] == 'Muslim' ? 0 : 1,
                'nationality' => $row[20] == 'Omanis' ? 0 : 1,
                'employee_category' => $row[21] == 'Employee' ? 0 : 1,
                'country' => $row[22],
                'document_title8' => $row[23],
                'expiry_date8' => $passportExpiryDate,
                'document_title9' => $row[25],
                'expiry_date9' => $visaExpiryDate,
                'document_title10' => $row[27],
                'expiry_date10' => $drivingExpiryDate,
                'document_title11' => $row[29],
                'expiry_date11' => $civilExpiryDate,
                'account_number' => $row[31],
                'ifsc_number' => $row[32],
                'name_of_the_bank' => $row[33],
                'account_holder' => $row[34],
                'basic_salary' => $row[35],
                'increment' => $row[36],
                'housing_allowance' => $row[37],
                'utility_allowance' => $row[38],
                'transport_allowance' => $row[39],
                'living_allowance' => $row[40],
                'mobile_allowance' => $row[41],
                'special_allowance' => $row[42],
                'prem_others' => $row[43],
                'education_and_club_allowance' => $row[44], # Education allowance
                'membership_allowance' => $row[45], #Club and Membership Allowance
                'date_of_leaving' => $dol,
                'status' => $usr_status,
                'status_remark' => $row[48],
                'ip_attendance' => $row[49] == 'Yes' ? 1 : 0,
                'mobile_attendance' => $row[50] == 'Yes' ? 1 : 0,
                'updated_by' => auth()->user()->user_id,
            ]);

            $leaveTypeList = LeaveType::where('status', 1)->get();

            foreach ($leaveTypeList as $leaveType) {

                $check = EmpLeaveBalance::where('employee_id', $checkEmployee->employee_id)->where('leave_type_id', $leaveType->leave_type_id)->first();

                if (!$check) {
                    $employee = Employee::where('employee_id', $checkEmployee->employee_id)->first();

                    if (isset($employee) && isset($leaveType)) {
                        $status = 0;

                        // Check Eligibility OR Common for all
                        if (($leaveType->nationality == $employee->nationality || $leaveType->nationality == 2)
                            && ($leaveType->religion == $employee->religion || $leaveType->religion == 2)
                            && ($leaveType->gender == $employee->gender || $leaveType->gender == 2)
                        ) {
                            $status = 1;
                        }

                        if ($status) {
                            EmpLeaveBalance::create([
                                'employee_id' => $checkEmployee->employee_id,
                                'finger_id' => $checkEmployee->finger_id,
                                'branch_id' => $checkEmployee->branch_id,
                                'department_id' => $checkEmployee->department_id,
                                'designation_id' => $checkEmployee->designation_id,
                                'leave_balance' => $leaveType->num_of_day,
                                'leave_type_id' => $leaveType->leave_type_id,
                            ]);
                        } else {
                            EmpLeaveBalance::create([
                                'employee_id' => $checkEmployee->employee_id,
                                'finger_id' => $checkEmployee->finger_id,
                                'branch_id' => $checkEmployee->branch_id,
                                'department_id' => $checkEmployee->department_id,
                                'designation_id' => $checkEmployee->designation_id,
                                'leave_balance' => 0,
                                'leave_type_id' => $leaveType->leave_type_id,
                            ]);
                        }
                    }
                }
            }
        }
    }

    public function startRow(): int
    {
        return 2;
    }

    public function chunkSize(): int
    {
        return 200;
    }

    public function batchSize(): int
    {
        return 200;
    }
}
