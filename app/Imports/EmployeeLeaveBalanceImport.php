<?php

namespace App\Imports;

use App\Model\Employee;
use App\Model\EmpLeaveBalance;
use App\Lib\Enumerations\UserStatus;
use App\Model\LeaveType;
use Maatwebsite\Excel\Concerns\ToModel;
use Maatwebsite\Excel\Concerns\WithLimit;
use Maatwebsite\Excel\Concerns\Importable;
use Maatwebsite\Excel\Concerns\WithStartRow;
use Maatwebsite\Excel\Concerns\WithValidation;

class EmployeeLeaveBalanceImport implements ToModel, WithValidation, WithStartRow, WithLimit
{
    use Importable;

    private $data;

    public function __construct(array $data = [])
    {
        $this->data = $data;
    }

    public function sanitize()
    {
        $this->data['*.16'] = trim($this->data['*.16']);
    }

    public function rules(): array
    {
        return [
            '*.0' => 'required',
            '*.2' => 'required|exists:employee,finger_id',
            '*.3' => 'required|exists:branch,branch_name',
            '*.4' => 'required|exists:department,department_name',
            '*.5' => 'required|exists:designation,designation_name',
            '*.6' => 'required',
            '*.7' => 'required',
            '*.8' => 'required',
            '*.9' => 'required',
            '*.10' => 'required',
            '*.11' => 'required',
            '*.12' => 'required',
            '*.13' => 'required',
            '*.14' => 'required',
            '*.15' => 'required',
            '*.16' => 'required',
            '*.17' => 'required',
            '*.18' => 'required',
            '*.19' => 'required',

        ];
    }

    public function customValidationMessages()
    {
        return [
            '0.required' => 'Sr.No is required',
            '2.required' => 'FingerPrintId is required (ie: Device Unique id) ',
            '3.required' => 'Branch Name should be same as the name provided in Master',
            '4.required' => 'Department Name should be same as the name provided in Master',
            '5.required' => 'Designation Name should be same as the name provided in Master',
            '6.required' => 'Nationality is required',
            '7.required' => 'Religion is required',
            '8.required' => 'Gender is required',
            '9.required' => 'Comp Leave is required',
            '10.required' => 'Accompanying Leave is required',
            '11.required' => 'Hajj Leave is required',
            '12.required' => 'Meternity Leave is required',
            '13.required' => 'Annual Leave is required',
            '14.required' => 'Sick Leave is required',
            '15.required' => 'Marriage Leave is required',
            '16.required' => 'Exam Leave is required',
            '17.required' => 'Emergency Leave is required',
            '18.required' => 'Paternity Leave is required',
            '19.required' => 'UnPaid Leave is required',


        ];
    }

    public function model(array $row)
    {
        // dd($row[2]);

        $checkEmployee = Employee::where('finger_id', $row[2])->where('status', UserStatus::$ACTIVE)->first();

        if ($checkEmployee) {
            // $employeeData = EmpLeaveBalance::where('employee_id', $checkEmployee->employee_id)->update([
            //     'employee_id' => $checkEmployee->employee_id,
            //     'finger_id' => $checkEmployee->finger_id,
            //     "comp_leave" => $row[6],
            //     "emergency_leave" => $row[7],
            //     "exam_leave" => $row[8],
            //     "mrg_leave" => $row[9],
            //     "sick_leave" => $row[10],
            //     "accom_leave" => $row[11],
            //     "hajj_leave" => $row[12],
            //     "maternity_leave" => $row[13],
            //     "annual_leave" => $row[14],
            //     "paternity_leave" => $row[15],
            //     "unpaid_leave" => $row[16],

            // ]);

            $leaveType = LeaveType::where('status', 1)->get();
            foreach ($leaveType as $key => $leave) {
                $leaveBalance = EmpLeaveBalance::where('finger_id', $row[2])->where('employee_id', $checkEmployee->employee_id)->where('leave_type_id', $leave->leave_type_id)->first();
                if ($leaveBalance) {
                    $employeeData = EmpLeaveBalance::where('employee_id', $checkEmployee->employee_id)->where('leave_type_id', $leave->leave_type_id)->update([
                        'leave_balance' => $row[$key + 9],
                    ]);
                } else {
                    $employeeData = EmpLeaveBalance::create([
                        'employee_id' => $checkEmployee->employee_id,
                        'finger_id' => $row[2],
                        'branch_id' => $checkEmployee->branch_id,
                        'department_id' => $checkEmployee->department_id,
                        'designation_id' => $checkEmployee->designation_id,
                        'leave_type_id' => $leave->leave_type_id,
                        'leave_balance' => $row[$key + 9],
                    ]);
                }
            }
        }
    }

    public function startRow(): int
    {
        return 2;
    }

    public function limit(): int
    {
        return 200;
    }
}
