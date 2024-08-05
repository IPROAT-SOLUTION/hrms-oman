<?php

namespace App\Http\Controllers\Leave;

use App\Model\Branch;
use App\Model\Employee;
use App\Model\LeaveType;
use App\Model\Department;
use Illuminate\Http\Request;
use App\Model\EmpLeaveBalance;
use App\Model\LeaveApplication;
use App\Model\PrintHeadSetting;
use Barryvdh\DomPDF\Facade as PDF;
use Illuminate\Support\Facades\DB;
use App\Http\Controllers\Controller;
use App\Lib\Enumerations\LeaveStatus;
use App\Model\Designation;
use App\Repositories\LeaveRepository;

class PaidLeaveReportController extends Controller
{

    protected $leaveRepository;

    public function __construct(LeaveRepository $leaveRepository)
    {
        $this->leaveRepository = $leaveRepository;
    }

    public function employeePaidLeaveReport(Request $request)
    {
        $employeeList = Employee::where('status', 1)->get();
        $results      = [];
        if ($_POST) {
            $results = LeaveApplication::with(['employee', 'leaveType', 'approveBy'])
                ->where('status', LeaveStatus::$APPROVE)
                ->where('leave_type_id', 2)
                ->where('employee_id', $request->employee_id)
                ->whereBetween('application_date', [dateConvertFormtoDB($request->from_date), dateConvertFormtoDB($request->to_date)])
                ->orderBy('leave_application_id', 'DESC')
                ->get();
        }
        return view('admin.leave.paidLeaveReport.paidLeaveReport', ['results' => $results, 'employeeList' => $employeeList, 'employee_id' => $request->employee_id, 'from_date' => $request->from_date, 'to_date' => $request->to_date]);
    }

    public function downloadPaidLeaveReport(Request $request)
    {

        $employeeInfo = Employee::with('department')->where('employee_id', $request->employee_id)->first();
        $printHead    = PrintHeadSetting::first();
        $results      = LeaveApplication::with(['employee', 'leaveType', 'approveBy'])
            ->where('status', LeaveStatus::$APPROVE)
            ->where('leave_type_id', 2)

            ->where('employee_id', $request->employee_id)
            ->whereBetween('application_date', [dateConvertFormtoDB($request->from_date), dateConvertFormtoDB($request->to_date)])
            ->orderBy('leave_application_id', 'DESC')
            ->get();
        $data = [
            'results'         => $results,
            'form_date'       => dateConvertFormtoDB($request->from_date),
            'to_date'         => dateConvertFormtoDB($request->to_date),
            'printHead'       => $printHead,
            'employee_name'   => $employeeInfo->first_name . ' ' . $employeeInfo->last_name,
            'department_name' => $employeeInfo->department->department_name,
        ];

        $pdf = PDF::loadView('admin.leave.paidLeaveReport.pdf.paidLeaveReportPdf', $data);
        $pdf->setPaper('A4', 'landscape');
        $pageName = $employeeInfo->first_name . "-leave-report.pdf";
        return $pdf->download($pageName);
    }

    public function paidLeaveSummaryReport(Request $request)
    {

        $employeeList = Employee::where('status', 1)->get();
        $result       = [];
        if ($_POST) {
            $result = $this->summaryReportDataFormat($request->employee_id);
        }
        $data = [
            'results'      => $result,
            'employeeList' => $employeeList,
            'from_date'    => $request->from_date,
            'to_date'      => $request->to_date,
            'employee_id'  => $request->employee_id,
        ];

        return view('admin.leave.paidLeaveReport.paidLeaveSummaryReport', $data);
    }

    public function summaryReportDataFormat($employee_id)
    {
        $leaveType                 = LeaveType::where('status', 1)->where('leave_type_id', 2)->get();
        $employeeTotalLeaveDetails = LeaveApplication::select('leave_application.*', DB::raw('SUM(leave_application.number_of_day) as leaveConsume'))
            ->where('employee_id', $employee_id)
            ->groupBy('leave_application.leave_type_id')
            ->get()->toArray();
        $arrayFormat = [];
        foreach ($leaveType as $value) {
            if ($value->leave_type_id == 1) {
                $action                  = "getEarnLeaveBalanceAndExpenseBalance";
                $getNumberOfEarnLeave    = $this->leaveRepository->calculateEmployeeEarnLeave($value->leave_type_id, $employee_id, $action);
                $temp['num_of_day']      = $getNumberOfEarnLeave['totalEarnLeave'];
                $temp['leave_consume']   = $getNumberOfEarnLeave['leaveConsume'];
                $temp['current_balance'] = $getNumberOfEarnLeave['currentBalance'];
            } else {
                $temp['num_of_day'] = $value->num_of_day;
                $a                  = array_search($value->leave_type_id, array_column($employeeTotalLeaveDetails, 'leave_type_id'));
                if (gettype($a) == 'integer') {
                    $temp['leave_consume']   = $employeeTotalLeaveDetails[$a]['leaveConsume'];
                    $temp['current_balance'] = $value->num_of_day - $employeeTotalLeaveDetails[$a]['leaveConsume'];
                } else {
                    $temp['leave_consume']   = 0;
                    $temp['current_balance'] = $value->num_of_day;
                }
            }
            $temp['leave_type_id']   = $value->leave_type_id;
            $temp['leave_type_name'] = $value->leave_type_name;
            $arrayFormat[]           = $temp;
        }

        return $arrayFormat;
    }

    public function downloadPaidLeaveSummaryReport(Request $request)
    {

        $employeeInfo = Employee::with('department')->where('employee_id', $request->employee_id)->first();
        $printHead    = PrintHeadSetting::first();

        $result = $this->summaryReportDataFormat($request->employee_id);
        $data   = [
            'results'         => $result,
            'form_date'       => dateConvertFormtoDB($request->from_date),
            'to_date'         => dateConvertFormtoDB($request->to_date),
            'printHead'       => $printHead,
            'employee_name'   => $employeeInfo->first_name . ' ' . $employeeInfo->last_name,
            'department_name' => $employeeInfo->department->department_name,
        ];

        $pdf = PDF::loadView('admin.leave.paidLeaveReport.pdf.paidLeaveSummaryReportPdf', $data);
        $pdf->setPaper('A4', 'landscape');
        $pageName = $employeeInfo->first_name . "-leave-summary-report.pdf";
        return $pdf->download($pageName);
    }

    public function index(Request $request)
    {

        if ((decrypt(session('logged_session_data.role_id'))) != 1 && (decrypt(session('logged_session_data.role_id'))) != 2) {
            $departmentList = Department::where('department_id', decrypt(session('logged_session_data.department_id')))->get();
            $branch = Branch::where('branch_id', decrypt(session('logged_session_data.branch_id')))->get();
            $designation = Designation::where('designation_id', decrypt(session('logged_session_data.designation_id')))->get();
        } else {
            $departmentList = Department::get();
            $branch = Branch::get();
            $designation = Designation::get();
        }

        $qry = ' 1';

        if ($request->department_id)
            $qry .= ' AND department_id=' . $request->department_id;
        if ($request->branch_id)
            $qry .= ' AND branch_id=' . $request->branch_id;
        if ($request->designation_id)
            $qry .= ' AND designation_id=' . $request->designation_id;

        $items = Employee::select('employee_id', 'finger_id', 'department_id', 'branch_id', 'designation_id', 'first_name', 'last_name')->with(['branch', 'department', 'designation', 'leaveBalance'])->whereRaw($qry)->get();

        $leaveType = LeaveType::where('status', 1)->orderBy('leave_type_id')->get(); //  dd($leaveType);
        $dataset = [];

        foreach ($items as $key =>  $item) {
            $leaveBalance = null;
            foreach ($leaveType as  $leave) {
                if (gettype($item->leaveBalance) == 'object') {
                    $leaveBalance = $item->leaveBalance->filter(function ($q) use ($leave) {
                        return  $q->leave_type_id == $leave->leave_type_id;
                    })->values()->first();
                }

                if ($leaveBalance) {
                    $dataset[$item->employee_id]['leave_type'][] = $leaveBalance->leave_balance ?? '0.0';
                } else {
                    $dataset[$item->employee_id]['leave_type'][] = '0.0';
                }
            }
            $dataset[$item->employee_id]['employee_id'] = $item->employee_id;
            $dataset[$item->employee_id]['department'] = $item->department->department_name;
            $dataset[$item->employee_id]['employee_name'] = trim($item->first_name . ' ' . $item->last_name);
            $dataset[$item->employee_id]['finger_id'] = $item->finger_id;
            $dataset[$item->employee_id]['designation'] = $item->designation->designation_name;
            $dataset[$item->employee_id]['branch'] = $item->branch->branch_name ?? '';
        }

        return view('admin.leave.employee_leave_balance.table', [
            'items' => $dataset,
            'branch' => $branch,
            'designation' => $designation,
            'leave_type' => $leaveType,
            'departmentList' => $departmentList,
            'department_id' => $request->department_id,
            'designation_id' => $request->designation_id,
            'branch_id' => $request->branch_id
        ]);
    }
}
