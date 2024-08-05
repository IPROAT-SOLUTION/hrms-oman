<?php

namespace App\Http\Controllers\leave;

use App\Model\Employee;
use App\Model\LeaveType;
use Illuminate\Http\Request;
use App\Model\EmpLeaveBalance;
use App\Exports\LeaveBalanceExport;
use App\Http\Controllers\Controller;
use Maatwebsite\Excel\Facades\Excel;
use App\Imports\EmployeeLeaveBalanceImport;
use App\Lib\Enumerations\UserStatus;


class LeaveBalanceController extends Controller
{
    public function importLeaveBalance(Request $request)
    {
        try {

            $file = $request->file('select_file');
            // dd($file);
            Excel::import(new EmployeeLeaveBalanceImport($request->all()), $file);

            return back()->with('success', 'Holiday Details Imported Successfully.');
        } catch (\Maatwebsite\Excel\Validators\ValidationException $e) {
            info($e);
            $import = new EmployeeLeaveBalanceImport();
            $import->import($file);

            foreach ($import->failures() as $failure) {
                $failure->row(); // row that went wrong
                $failure->attribute(); // either heading key (if using heading row concern) or column index
                $failure->errors(); // Actual error messages from Laravel validator
                $failure->values(); // The values of the row that has failed.
            }
        }
    }
    public function leaveBalanceTemplate()
    {

        $extraData = [];
        $data = [];

        $items = Employee::select('employee_id', 'finger_id', 'department_id', 'branch_id', 'designation_id', 'first_name', 'last_name')->with(['branch', 'department', 'designation', 'leaveBalance'])->where('status', 1)->get();
        $leaveType = LeaveType::orderBy('leave_type_id')->get(); //  dd($leaveType);

        foreach ($items as $key =>  $item) {
            $data[$item->employee_id]['sl'] = $key + 1;
            $data[$item->employee_id]['employee_name'] = trim($item->first_name . ' ' . $item->last_name);
            $data[$item->employee_id]['finger_id'] = $item->finger_id;
            $data[$item->employee_id]['branch'] = $item->branch->branch_name ?? '';
            $data[$item->employee_id]['department'] = $item->department->department_name;
            $data[$item->employee_id]['designation'] = $item->designation->designation_name;
            $data[$item->employee_id]['nationality'] = $item->nationality == 0 ? 'Omanis' : "Expacts";
            $data[$item->employee_id]['religion'] = $item->religion == 0 ? 'Muslim' : 'Non-muslim';
            $data[$item->employee_id]['gender'] = $item->gender == 0 ? 'Male' : 'Female';

            $leaveBalance = null;

            foreach ($leaveType as  $leave) {
                if (gettype($item->leaveBalance) == 'object') {
                    $leaveBalance = $item->leaveBalance->filter(function ($q) use ($leave) {
                        return  $q->leave_type_id == $leave->leave_type_id;
                    })->values()->first();
                }

                if ($leaveBalance) {
                    $data[$item->employee_id][$leave->leave_type_id] = $leaveBalance->leave_balance ?? '0.0';
                } else {
                    $data[$item->employee_id][$leave->leave_type_id] = '0.0';
                }
            }
        }

        $heading = [
            [
                'Sl NO',
                'EMPLOYEE NAME',
                'FINGER ID',
                'BRANCH',
                'DEPARTMENT',
                'DESIGNATION',
                'NATIONALITY',
                'RELIGION',
                'GENDER',

            ],
        ];

        foreach ($leaveType as $key => $leave_type) {
            $heading[0][] = strtoupper($leave_type->leave_type_name);
        }

        $extraData['heading'] = $heading;
        $filename = 'leave-balance-template-' . DATE('d-m-Y His') . '.xlsx';
        $response = Excel::download(new LeaveBalanceExport($data, $extraData), $filename);
        ob_end_clean();
        return $response;
    }
}
