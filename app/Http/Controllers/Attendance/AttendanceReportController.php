<?php

namespace App\Http\Controllers\Attendance;

use DateTime;
use Carbon\Carbon;
use App\Model\MsSql;
use App\Model\Branch;
use App\Model\Employee;
use App\Model\LeaveType;
use App\Model\WorkShift;
use Carbon\CarbonPeriod;
use App\Model\Department;
use Illuminate\Http\Request;
use App\Model\ManualAttendance;
use App\Model\PrintHeadSetting;
use App\Http\Controllers\Controller;
use App\Lib\Enumerations\UserStatus;
use Maatwebsite\Excel\Facades\Excel;
use App\Repositories\AttendanceRepository;
use App\Exports\DailyAttendanceReportExport;
use App\Exports\AttendanceMusterReportExport;
use App\Exports\MonthlyAttendanceReportExport;
use App\Exports\SummaryAttendanceReportExport;
use App\Exports\AttendanceSummaryReportExportCollection;
use App\Http\Controllers\View\EmployeeAttendaceController;

class AttendanceReportController extends Controller
{

    protected $attendanceRepository;
    protected $employeeAttendaceController;

    public function __construct(AttendanceRepository $attendanceRepository, EmployeeAttendaceController $employeeAttendaceController)
    {
        $this->attendanceRepository = $attendanceRepository;
        $this->employeeAttendaceController = $employeeAttendaceController;
    }

    public function dailyAttendance(Request $request)
    {
        \set_time_limit(0);
        if ((decrypt(session('logged_session_data.role_id'))) != 1 && (decrypt(session('logged_session_data.role_id'))) != 2) {
            $departmentList = Department::where('department_id', decrypt(session('logged_session_data.department_id')))->get();
        } else {
            $departmentList = Department::get();
        }

        $results = [];

        if ($_POST) {
            $results = $this->attendanceRepository->getEmployeeDailyAttendance($request->date, $request->department_id, $request->attendance_status);
        }
        return view('admin.attendance.report.dailyAttendance', ['results' => json_decode(json_encode($results)), 'departmentList' => $departmentList, 'date' => $request->date, 'department_id' => $request->department_id, 'attendance_status' => $request->attendance_status]);
    }

    public function monthlyAttendance(Request $request)
    {
        set_time_limit(0);

        $employeeList = Employee::whereHas('userName', function ($q) {
            $q->where('role_id', '>=', 3);
        })->where('supervisor_id', decrypt(session('logged_session_data.employee_id')))->orwhere('employee_id', decrypt(session('logged_session_data.employee_id')))->get();

        if (decrypt(session('logged_session_data.role_id')) == 1 || decrypt(session('logged_session_data.role_id')) == 2) {
            $employeeList = Employee::whereHas('userName', function ($q) {
                $q->where('role_id', '>=', 3);
            })->get();
        }
        if (decrypt(session('logged_session_data.role_id')) == 3) {
            $hasSupervisorWiseEmployee = Employee::select('employee_id')->where('operation_manager_id', decrypt(session('logged_session_data.employee_id')))->get()->toArray();
            $employeeList = Employee::whereHas('userName', function ($q) {
                $q->where('role_id', '>=', 3);
            })->whereIn('employee_id', array_values($hasSupervisorWiseEmployee))->get();
        }

        $results = [];

        $fromDate = dateConvertFormtoDB($request->from_date);
        $toDate = dateConvertFormtoDB($request->to_date);
        $dateRange = findFromDateToDateToAllDate($fromDate, $toDate);
        $dateRangeLength = count($dateRange);

        if ($_POST) {
            if ($request->employee_id == 'allData') {
                $employeeIds = $employeeList->pluck('employee_id')->toArray();
                foreach ($employeeIds as $employeeId) {
                    $results[] = $this->attendanceRepository->getEmployeeMonthlyAttendance($fromDate, $toDate, $employeeId);
                }
            } else {
                $employeeId = $request->employee_id;
                $results[] = $this->attendanceRepository->getEmployeeMonthlyAttendance($fromDate, $toDate, $employeeId);
            }
        }

        //   exit();
        return view('admin.attendance.report.monthlyAttendance', ['dateRangeLength' => $dateRangeLength, 'results' => $results, 'employeeList' => $employeeList, 'from_date' => $request->from_date, 'to_date' => $request->to_date, 'employee_id' => $request->employee_id]);
    }

    public function myAttendanceReport(Request $request)
    {
        set_time_limit(0);

        $employeeList = Employee::where('status', UserStatus::$ACTIVE)->where('employee_id', decrypt(session('logged_session_data.employee_id')))->get();
        $results = [];
        if ($_POST) {
            $results = $this->attendanceRepository->getEmployeeMonthlyAttendance(dateConvertFormtoDB($request->from_date), dateConvertFormtoDB($request->to_date), decrypt(session('logged_session_data.employee_id')));
        } else {
            $results = $this->attendanceRepository->getEmployeeMonthlyAttendance(date('Y-m-01'), date("Y-m-t", strtotime(date('Y-m-01'))), decrypt(session('logged_session_data.employee_id')));
        }

        return view('admin.attendance.report.mySummaryReport', ['results' => $results, 'employeeList' => $employeeList, 'from_date' => $request->from_date, 'to_date' => $request->to_date, 'employee_id' => $request->employee_id]);
    }

    public function attendanceSummaryReport(Request $request)
    {
        set_time_limit(0);
        if ($request->from_date && $request->to_date) {
            $from_date = $request->from_date;
            $to_date = $request->to_date;
        } else {
            $from_date = date("01/m/Y");
            $to_date = date("t/m/Y");
        }

        $month = date('Y-m', strtotime(dateConvertFormtoDB($from_date)));
        $monthAndYear = explode('-', $month);
        $month_data = $monthAndYear[1];
        $dateObj = DateTime::createFromFormat('!m', $month_data);
        $monthName = $dateObj->format('F');

        $monthToDate = findFromDateToDateToAllDate(dateConvertFormtoDB($from_date), dateConvertFormtoDB($to_date));
        $leaveType = LeaveType::where('status', 1)->get();
        $result = [];
        if ($_POST) {
            $result = $this->attendanceRepository->findAttendanceSummaryReport($month, dateConvertFormtoDB($from_date), dateConvertFormtoDB($to_date));
        }

        return view('admin.attendance.report.summaryReport', ['results' => $result, 'monthToDate' => $monthToDate, 'month' => $month, 'from_date' => $request->from_date, 'to_date' => $request->to_date, 'leaveTypes' => $leaveType, 'monthName' => $monthName]);
    }

    public function attendanceMusterReport(Request $request)
    {
        \set_time_limit(0);
        if ($request->from_date && $request->to_date) {
            $month_from = date('Y-m', strtotime(dateConvertFormtoDB($request->from_date)));
            $month_to = date('Y-m', strtotime(dateConvertFormtoDB($request->to_date)));
            $start_date = dateConvertFormtoDB(dateConvertFormtoDB($request->from_date));
            $end_date = dateConvertFormtoDB(dateConvertFormtoDB($request->to_date));
        } else {
            $month_from = date('Y-m');
            $month_to = date('Y-m');
            $start_date = $month_from . '-01';
            $end_date = date("Y-m-t", strtotime($start_date));
        }

        $departmentList = Department::get();
        $employeeList = Employee::with('department', 'branch', 'designation')->where('status', UserStatus::$ACTIVE)->get();
        $branchList = Branch::get();

        $monthAndYearFrom = explode('-', $month_from);
        $monthAndYearTo = explode('-', $month_to);

        $month_data_from = $monthAndYearFrom[1];
        $month_data_to = $monthAndYearTo[1];
        $dateObjFrom = DateTime::createFromFormat('!m', $month_data_from);
        $dateObjTo = DateTime::createFromFormat('!m', $month_data_to);
        $monthNameFrom = $dateObjFrom->format('F');
        $monthNameTo = $dateObjTo->format('F');

        $employeeInfo = Employee::with('department', 'branch', 'designation')->where('status', UserStatus::$ACTIVE)->where('employee_id', $request->employee_id)->first();
        $shiftName = WorkShift::pluck('shift_name')->toArray();
        $monthToDate = findMonthFromToDate($start_date, $end_date);

        if ($request->from_date && $request->to_date) {
            $result = $this->attendanceRepository->findAttendanceMusterReport($start_date, $end_date, $request->employee_id, $request->department_id, $request->branch_id);
        } else {
            $result = [];
        }
        // dd($result);
        return view('admin.attendance.report.musterReport', [
            'departmentList' => $departmentList, 'employeeInfo' => $employeeInfo, 'employeeList' => $employeeList, 'branchList' => $branchList,
            'results' => $result, 'monthToDate' => $monthToDate, 'month_from' => $month_from, 'month_to' => $month_to, 'monthNameFrom' => $monthNameFrom,
            'monthNameTo' => $monthNameTo, 'department_id' => $request->department_id, 'employee_id' => $request->employee_id, 'branch_id' => $request->branch_id,
            'from_date' => $request->from_date, 'to_date' => $request->to_date, 'monthAndYearFrom' => $monthAndYearFrom, 'monthAndYearTo' => $monthAndYearTo,
            'start_date' => $start_date, 'end_date' => $end_date, 'shift_name' => $shiftName
        ]);
    }
    public function musterExcelExportFromCollection(Request $request)
    {
        \set_time_limit(0);
        \ini_set('memory_limit', '512M');

        if ($request->from_date && $request->to_date) {
            $start_date = dateConvertFormtoDB($request->from_date);
            $end_date = dateConvertFormtoDB($request->to_date);
            $month_from = date('Y-m', strtotime($start_date));
            $month_to = date('Y-m', strtotime($end_date));
        } else {
            $month_from = date('Y-m');
            $month_to = date('Y-m');
            $start_date = $month_from . '-01';
            $end_date = date("Y-m-t", strtotime($start_date));
        }

        $departmentList = Department::get();
        $employeeList = Employee::with('department', 'branch', 'designation')->where('status', UserStatus::$ACTIVE)->get();
        $branchList = Branch::get();

        $monthAndYearFrom = explode('-', $month_from);
        $monthAndYearTo = explode('-', $month_to);

        $month_data_from = $monthAndYearFrom[1];
        $month_data_to = $monthAndYearTo[1];
        $dateObjFrom = DateTime::createFromFormat('!m', $month_data_from);
        $dateObjTo = DateTime::createFromFormat('!m', $month_data_to);
        $monthNameFrom = $dateObjFrom->format('F');
        $monthNameTo = $dateObjTo->format('F');

        $employeeInfo = Employee::with('department', 'branch', 'designation')->where('status', UserStatus::$ACTIVE)->where('employee_id', $request->employee_id)->first();

        $monthToDate = findMonthFromToDate($start_date, $end_date);
        //dd($monthToDate);
        $dataset = $this->attendanceRepository->findAttendanceMusterReportExcelDump($start_date, $end_date, $request->employee_id, $request->department_id, $request->branch_id);
        // dd($result);

        $inner_head = ['Sl.No', 'BRANCH', 'EMPLOYEE ID', 'EMPLOYEE NAME', 'DEPARTMENT', 'TITLE'];
        foreach ($monthToDate as $Day) {
            $inner_head[] = $Day['day'] . '/' . strtoupper($Day['day_name']);
        }

        $heading = [
            [
                strtoupper('Attendance Muster Report - ') . (date('F d Y', strtotime($start_date)) ?: '') . ' to ' . (date('F d Y', strtotime($end_date)) ?: ''),
            ],
            $inner_head,
        ];

        $extraData = ['heading' => $heading];
        // dd($dataset);
        return Excel::download(new AttendanceMusterReportExport($dataset, $extraData), 'detailedReport' . date('Ymd', strtotime($request->date)) . date('His') . '.xlsx');
    }
    public function attendancerecordExcelExportFromCollection(Request $request)
    {
        \set_time_limit(0);
        \ini_set('memory_limit', '512M');

        if ($request->from_date && $request->to_date) {
            $month_from = date('Y-m', strtotime($request->from_date));
            $month_to = date('Y-m', strtotime($request->to_date));
            $start_date = dateConvertFormtoDB($request->from_date);
            $end_date = dateConvertFormtoDB($request->to_date);
        } else {
            $month_from = date('Y-m');
            $month_to = date('Y-m');
            $start_date = $month_from . '-01';
            $end_date = date("Y-m-t", strtotime($start_date));
        }

        $departmentList = Department::get();
        $employeeList = Employee::with('department', 'branch', 'designation')->where('status', UserStatus::$ACTIVE)->get();
        $branchList = Branch::get();

        $monthAndYearFrom = explode('-', $month_from);
        $monthAndYearTo = explode('-', $month_to);

        $month_data_from = $monthAndYearFrom[1];
        $month_data_to = $monthAndYearTo[1];
        $dateObjFrom = DateTime::createFromFormat('!m', $month_data_from);
        $dateObjTo = DateTime::createFromFormat('!m', $month_data_to);
        $monthNameFrom = $dateObjFrom->format('F');
        $monthNameTo = $dateObjTo->format('F');

        $employeeInfo = Employee::with('department', 'branch', 'designation')->where('status', UserStatus::$ACTIVE)->where('employee_id', $request->employee_id)->first();

        $monthToDate = findMonthFromToDate($start_date, $end_date);
        //dd($monthToDate);
        $dataset = $this->attendanceRepository->findAttendanceMusterReportExcelDump($start_date, $end_date, $request->employee_id, $request->department_id, $request->branch_id);
        // dd($result);

        $inner_head = ['Sl.No', 'BRANCH', 'EMPLOYEE ID', 'EMPLOYEE NAME', 'DEPARTMENT', 'TITLE'];
        foreach ($monthToDate as $Day) {
            $inner_head[] = $Day['day'];
        }

        $heading = [
            [
                'Attendance Detailed Report',
            ],
            $inner_head,
        ];

        $extraData = ['heading' => $heading];
        // dd($dataset);
        return Excel::download(new AttendanceMusterReportExport($dataset, $extraData), 'detailedReport' . date('Ymd', strtotime($request->date)) . date('His') . '.xlsx');
    }
    public function attendanceRecord(Request $request)
    {
        set_time_limit(0);
        $results = [];
        $ms_sql = MsSql::with('employee:finger_id,first_name,last_name')->whereDate('datetime', date('Y-m-d'))->orderBy('ms_sql.datetime')->get()->toArray();
        $manual_attendance = ManualAttendance::with('employee:finger_id,first_name,last_name')->whereDate('datetime', date('Y-m-d'))->orderBy('manual_attendance.datetime')->get()->toArray();
        $results = (object) array_merge($ms_sql, $manual_attendance);

        if ($_POST) {

            // $from_date = dateConvertFormtoDB($request->from_date) . ' 00:00:00';
            // $to_date = dateConvertFormtoDB($request->to_date) . ' 23:59:59';
            $from_date = dateConvertFormtoDB($request->from_date);
            $to_date = dateConvertFormtoDB($request->to_date);

            if ($request->device_name != null) {
                $request->device_name = $request->device_name == 'N/A' ? null : $request->device_name;
                $ms_sql = MsSql::where('device_name', $request->device_name)->whereDate('datetime', '>=', $from_date)->whereDate('datetime', '<=', $to_date)
                    ->with('employee:finger_id,first_name,last_name')->get()->toArray();
                $manual_attendance = ManualAttendance::where('device_name', $request->device_name)->whereDate('datetime', '>=', $from_date)->whereDate('datetime', '<=', $to_date)
                    ->with('employee:finger_id,first_name,last_name')->get()->toArray();
                $results = (object) array_merge($ms_sql, $manual_attendance);
            } elseif ($request->from_date && $request->to_date) {
                $ms_sql = MsSql::whereDate('datetime', '>=', $from_date)->whereDate('datetime', '<=', $to_date)
                    ->with('employee:finger_id,first_name,last_name')->get()->toArray();
                $manual_attendance = ManualAttendance::whereDate('datetime', '>=', $from_date)->whereDate('datetime', '<=', $to_date)
                    ->with('employee:finger_id,first_name,last_name')->get()->toArray();
                $results = (object) array_merge($ms_sql, $manual_attendance);
            }
        }

        return \view('admin.attendance.report.attendanceRecord', ['results' => $results, 'device_name' => $request->device_name, 'from_date' => $request->from_date, 'to_date' => $request->to_date, 'employee_id ' => $request->employee_id]);
    }

    public function report(Request $request)
    {
        return view('admin.attendance.calculateAttendance.index');
    }

    public function calculateReport(Request $request)
    {

        $dates = CarbonPeriod::create(dateConvertFormtoDB($request->from_date), dateConvertFormtoDB($request->to_date))->toArray();

        $this->employeeAttendaceController->attendance(null, false, null, $dates);

        return redirect()->back()->with('success', 'reports generated successfully');
    }

    public function downloadDailyAttendance(Request $request)
    {
        set_time_limit(0);

        $printHead = PrintHeadSetting::first();
        $departmentList = Department::where('department_id', $request->department_id)->first();

        $results = $this->attendanceRepository->getEmployeeDailyAttendance($request->date, $request->department_id, $request->attendance_status);

        $data = [
            'results' => $results,
            'date' => $request->date,
            'printHead' => $printHead,
            'department_id' => $request->department_id,
            'department_name' => $departmentList->department_name ?? '',

        ];
        $pdf = \PDF::loadView('admin.attendance.report.pdf.dailyAttendancePdf', $data);
        $pdf->setPaper('A4', 'landscape');
        $pageName = "daily-attendance-" . dateConvertFormToDB($request->date) . ".pdf";
        return $pdf->download($pageName);
    }

    public function downloadMusterReport(Request $request)

    {
        set_time_limit(0);

        if ($request->from_date && $request->to_date) {
            $start_date = dateConvertFormtoDB($request->from_date);
            $end_date = dateConvertFormtoDB($request->to_date);
        } else {
            $start_date = date('Y-m') . '-01';
            $end_date = date("Y-m-t", strtotime($start_date));
        }

        $departmentList = Department::get();
        $employeeList = Employee::with('department', 'branch', 'designation')
            ->where('status', UserStatus::$ACTIVE)
            ->get();
        $branchList = Branch::get();

        $employeeInfo = Employee::with('department', 'branch', 'designation')
            ->where('status', UserStatus::$ACTIVE)
            ->where('employee_id', $request->employee_id)
            ->first();

        $monthToDate = findMonthFromToDate($start_date, $end_date);
        $results = $this->attendanceRepository->findAttendanceMusterReportExcelDump(
            $start_date,
            $end_date,
            $request->employee_id,
            $request->department_id,
            $request->branch_id
        );

        $month_from = date('Y-m', strtotime($start_date));
        $month_to = date('Y-m', strtotime($end_date));
        $monthAndYearFrom = explode('-', $month_from);
        $monthAndYearTo = explode('-', $month_to);
        $dateObjFrom = DateTime::createFromFormat('!m', $monthAndYearFrom[1]);
        $dateObjTo = DateTime::createFromFormat('!m', $monthAndYearTo[1]);
        $monthNameFrom = $dateObjFrom->format('F');
        $monthNameTo = $dateObjTo->format('F');
        // Reformat results to match Excel format
        $data = [
            'departmentList' => $departmentList,
            'employeeInfo' => $employeeInfo,
            'employeeList' => $employeeList,
            'branchList' => $branchList,
            'results' => $results,
            'monthToDate' => $monthToDate,
            'month_from' => $month_from,
            'month_to' => $month_to,
            'monthNameFrom' => $monthNameFrom,
            'monthNameTo' => $monthNameTo,
            'department_id' => $request->department_id,
            'employee_id' => $request->employee_id,
            'branch_id' => $request->branch_id,
            'from_date' => $request->from_date,
            'to_date' => $request->to_date,
            'monthAndYearFrom' => $monthAndYearFrom,
            'monthAndYearTo' => $monthAndYearTo,
            'start_date' => $start_date,
            'end_date' => $end_date,
            'shift_name' => WorkShift::pluck('shift_name')->toArray(),
        ];

        $pdf = \PDF::loadView('admin.attendance.report.pdf.musterReportpdf', $data);
        $pdf->setPaper('A3', 'landscape');
        $pageName = "muster-report-" . dateConvertFormToDB($start_date) . "_to_" . dateConvertFormToDB($end_date) . ".pdf";
        return $pdf->download($pageName);
    }


    public function downloadMonthlyAttendance(Request $request)
    {
        set_time_limit(0);

        $employeeInfo = Employee::whereHas('userName', function ($q) {
            $q->where('role_id', '>=', 3);
        })->with('department')->where('employee_id', $request->employee_id)->first();

        $printHead = PrintHeadSetting::first();
        $results = $this->attendanceRepository->getEmployeeMonthlyAttendance(dateConvertFormtoDB($request->from_date), dateConvertFormtoDB($request->to_date), $request->employee_id);

        $data = [
            'results' => $results,
            'form_date' => dateConvertFormtoDB($request->from_date),
            'to_date' => dateConvertFormtoDB($request->to_date),
            'printHead' => $printHead,
            'employeeInfo' => $employeeInfo,
        ];

        $pdf = \PDF::loadView('admin.attendance.report.pdf.monthlyAttendancePdf', $data);
        $pdf->setPaper('A4', 'portrait');
        return $pdf->download("monthly-attendance.pdf");
    }

    public function downloadMyAttendance(Request $request)
    {
        set_time_limit(0);

        $employeeInfo = Employee::with('department')->where('employee_id', $request->employee_id)->first();
        $printHead = PrintHeadSetting::first();
        $results = $this->attendanceRepository->getEmployeeMonthlyAttendance(dateConvertFormtoDB($request->from_date), dateConvertFormtoDB($request->to_date), $request->employee_id);
        $data = [
            'results' => $results,
            'form_date' => dateConvertFormtoDB($request->from_date),
            'to_date' => dateConvertFormtoDB($request->to_date),
            'printHead' => $printHead,
            'employee_name' => $employeeInfo->first_name . ' ' . $employeeInfo->last_name,
            'department_name' => $employeeInfo->department->department_name,
        ];

        $pdf = PDF::loadView('admin.attendance.report.pdf.mySummaryReportPdf', $data);
        $pdf->setPaper('A4', 'portrait');
        return $pdf->download("my-attendance.pdf");
    }

    public function downloadAttendanceSummaryReport($from_date, $to_date)
    {
        $printHead = PrintHeadSetting::first();
        $month = date('Y-m', strtotime($from_date));
        $monthToDate = findMonthToAllDate($month);
        $leaveType = LeaveType::where('status', 1)->get();
        $result = $this->attendanceRepository->findAttendanceSummaryReport($month, $from_date, $to_date);

        $monthAndYear = explode('-', $month);
        $month_data = $monthAndYear[1];
        $dateObj = DateTime::createFromFormat('!m', $month_data);
        $monthName = $dateObj->format('F');

        $data = [
            'results' => $result,
            'month' => $month,
            'printHead' => $printHead,
            'monthToDate' => $monthToDate,
            'leaveTypes' => $leaveType,
            'monthName' => $monthName,
            'from_date' => $from_date,
            'to_date' => $to_date,
        ];
        $pdf = PDF::loadView('admin.attendance.report.pdf.attendanceSummaryReportPdf', $data);
        $pdf->setPaper('A4', 'landscape');
        return $pdf->download("attendance-summaryReport.pdf");
    }

    public function dailyExcel(Request $request)
    {
        \set_time_limit(0);
        $results = [];
        $date = dateConvertFormtoDB($request->date);
        $departmentList = Department::get();
        $results = $this->attendanceRepository->getEmployeeDailyAttendance($request->date, $request->department_id, $request->attendance_status);
        $excel = new DailyAttendanceReportExport(
            'admin.attendance.report.dailyAttendancePagination',
            ['results' => json_decode(json_encode($results)), 'departmentList' => $departmentList, 'date' => $request->date, 'department_id' => $request->department_id, 'attendance_status' => $request->attendance_status]
        );
        $excelFile = Excel::download($excel, 'dailyReport' . date('Ymd', strtotime($date)) . '.xlsx');
        return $excelFile;
    }
    public function attendanceRecordExcel(Request $request)
    {
        \set_time_limit(0);
        $results = [];
        $departmentList = Department::get();
        $results = $this->attendanceRepository->getEmployeeDailyAttendance($request->date, $request->department_id, $request->attendance_status);
        $excel = new DailyAttendanceReportExport(
            'admin.attendance.report.excel.attendanceRecordPagination',
            ['results' => json_decode(json_encode($results)), 'departmentList' => $departmentList, 'date' => $request->date, 'department_id' => $request->department_id, 'attendance_status' => $request->attendance_status]
        );
        $excelFile = Excel::download($excel, 'Attendance Record Excel.xlsx');
        return $excelFile;
    }
    public function monthlyExcelExportFromCollection(Request $request)
    {
        \set_time_limit(0);
        $employeeList = Employee::whereHas('userName', function ($q) {
            $q->where('role_id', '>=', 3);
        })->pluck('employee_id')->toArray();

        $results = [];

        if ($_GET) {
            if ($request->employee_id == 'allData') {
                foreach ($employeeList as $employeeId) {
                    $fromDate = dateConvertFormtoDB($request->from_date);
                    $toDate = dateConvertFormtoDB($request->to_date);
                    $results[] = $this->attendanceRepository->getEmployeeMonthlyAttendance($fromDate, $toDate, $employeeId);
                }
            } else {
                $fromDate = dateConvertFormtoDB($request->from_date);
                $toDate = dateConvertFormtoDB($request->to_date);
                $employeeId = $request->employee_id;
                $results[] = $this->attendanceRepository->getEmployeeMonthlyAttendance($fromDate, $toDate, $employeeId);
            }
        }

        $excel = new MonthlyAttendanceReportExport('admin.attendance.report.monthlyAttendancePagination', [
            'results' => $results,
            'from_date' => $request->from_date,
            'to_date' => $request->to_date,
            'employee_id' => $request->employee_id,
        ]);

        $excelFile = Excel::download($excel, 'monthlyReport' . date('Y-m', strtotime($fromDate)) . '.xlsx');

        return $excelFile;
    }
    public function monthlyPdfExportFromCollection(Request $request)
    {
        set_time_limit(0);

        $employeeList = Employee::whereHas('userName', function ($q) {
            $q->where('role_id', '>=', 3);
        })->pluck('employee_id', 'finger_id')->toArray();

        $results = [];
        $fromDate = dateConvertFormtoDB($request->from_date);
        $toDate = dateConvertFormtoDB($request->to_date);

        if ($request->has(['from_date', 'to_date', 'employee_id'])) {
            if ($request->employee_id == 'allData') {
                foreach ($employeeList as $employeeId) {
                    $results[] = $this->attendanceRepository->getEmployeeMonthlyAttendance($fromDate, $toDate, $employeeId);
                }
            } else {
                $employeeId = $request->employee_id;
                $results[] = $this->attendanceRepository->getEmployeeMonthlyAttendance($fromDate, $toDate, $employeeId);
            }
        }
        // dd($results);
        $data = [
            'results' => $results,
            'from_date' => $request->from_date,
            'to_date' => $request->to_date,
            'employee_id' => $request->employee_id,
        ];

        $pdf = \PDF::loadView('admin.attendance.report.pdf.monthlyAttendancePdf', $data);
        $pdf->setPaper('A4', 'portrait');

        $pageName = "monthly-report-" . dateConvertFormToDB($request->from_date) . "_to_" . dateConvertFormToDB($request->to_date) . ".pdf";

        return $pdf->download($pageName);
    }

    public function summaryExcel(Request $request)
    {
        \set_time_limit(0);

        if ($request->from_date && $request->to_date) {
            $from_date = $request->from_date;
            $to_date = $request->to_date;
        } else {
            $from_date = date("01/m/Y");
            $to_date = date("t/m/Y");
        }

        $monthToDate = findFromDateToDateToAllDate(dateConvertFormtoDB($from_date), dateConvertFormtoDB($to_date));
        $result = $this->attendanceRepository->findAttendanceSummaryReport($request->month, dateConvertFormtoDB($from_date), dateConvertFormtoDB($to_date));

        $leaveType = LeaveType::where('status', 1)->get();

        $sl = null;
        $totalPresent = 0;
        $leaveData = [];
        $totalCol = 0;
        $totalWeeklyHoliday = 0;
        $totalGovtHoliday = 0;
        $totalAbsent = 0;
        $totalLeave = 0;

        $dataSet = [];

        foreach ($result as $key => $value) {
            $dataSet[$key]['sl_no'] =   ++$sl;
            $dataSet[$key]['finger_id'] = $value[0]['finger_id'];
            $dataSet[$key]['fullName'] = $value[0]['fullName'];
            $dataSet[$key]['designation_name'] =  $value[0]['designation_name'];
            $dataSet[$key]['department_name'] = $value[0]['department_name'];

            foreach ($value as $v) {
                if ($sl == 1) {
                    $totalCol++;
                }
                if ($v['attendance_status'] == 'present') {
                    $totalPresent++;
                    if ($v['shift_name'] != '' && $v['shift_name'] != null) {
                        $shiftName = acronym($v['shift_name']);
                    } else {
                        $shiftName = 'NA';
                    }

                    if ($v['inout_status'] == 'O') {
                        $dataSet[$key][$v['date']] =  $v['inout_status'] . '' . $shiftName;
                    } else {
                        $dataSet[$key][$v['date']] = $shiftName;
                    }
                } elseif ($v['attendance_status'] == 'absence') {
                    $totalAbsent++;
                    $dataSet[$key][$v['date']] = 'AA';
                } elseif ($v['attendance_status'] == 'leave') {

                    if ($v['day'] == 'FL') {
                        $totalLeave += 1;
                        $leaveData[$key][$v['leave_type']]['day'][] = 1;
                    }

                    if ($v['day'] == 'HL') {
                        $totalLeave += 0.5;
                        $leaveData[$key][$v['leave_type']]['day'][] = 0.5;
                    }

                    $leaveData[$key][$v['leave_type']][] = $v['leave_type'];
                    $dataSet[$key][$v['date']] = $v['day'] . '(' . acronym($v['leave_type']) . ')' ?? 'NA';
                } elseif ($v['attendance_status'] == 'holiday') {
                    $totalWeeklyHoliday++;
                    $dataSet[$key][$v['date']] = 'WH';
                } elseif ($v['attendance_status'] == 'publicHoliday') {
                    $totalGovtHoliday++;
                    $dataSet[$key][$v['date']] = "PH";
                } else {
                    $dataSet[$key][$v['date']] = '';
                }
            }

            $dataSet[$key]['total_present'] = (string)$totalPresent;
            $dataSet[$key]['total_holiday'] = (string)$totalGovtHoliday;

            foreach ($leaveType as $leave_type) {

                if ($sl == 1) {
                    $totalCol++;
                }
                if (isset($leaveData[$key][$leave_type->leave_type_name])) {
                    $c = array_sum($leaveData[$key][$leave_type->leave_type_name]['day']);
                } else {
                    $c = 0;
                }

                $dataSet[$key][$leave_type->leave_type_name] =  (string)$c;
            }

            $dataSet[$key]['total_paid_days'] =  (string)((int)$totalPresent + (int)$totalLeave + (int)$totalGovtHoliday);
            $dataSet[$key]['total_week_off'] =  (string)((int)$totalWeeklyHoliday);
            $dataSet[$key]['total_days'] =  (string)((int)$totalPresent + (int)$totalWeeklyHoliday + (int)$totalAbsent + (int)$totalLeave);

            $totalPresent =  $totalWeeklyHoliday =  $totalAbsent = $totalLeave =  $totalGovtHoliday = 0;
        }
        // dd($dataSet);
        $inner_head = ['Sl.No', 'EMPLOYEE ID', 'EMPLOYEE NAME', 'DESIGNATION', 'DEPARTMENT',];

        foreach ($monthToDate as $Day) {
            $inner_head[] = $Day['day'] . '/' . strtoupper($Day['day_name']);
        }

        $inner_head[] = 'TOTAL PRESENT';
        $inner_head[] =  'TOTAL HOLIDAY';

        foreach ($leaveType as $leave_type) {
            $inner_head[] = acronym($leave_type->leave_type_name);
        }

        $inner_head[] = 'TOTAL PAID DAYS';
        $inner_head[] = 'TOTAL WEEKOFF';
        $inner_head[] = 'TOTAL DAYS';

        $heading = [
            [
                strtoupper('Attendance Summary Report - ') . (date('F d Y', strtotime(dateConvertFormToDB($from_date))) ?: '') . ' to ' . (date('F d Y', strtotime(dateConvertFormToDB($to_date))) ?: ''),
            ],
            $inner_head,
        ];

        $extraData = ['heading' => $heading];
        // dd([$extraData, $dataSet['MIC182']]);
        // $response[] = $dataSet['MIC182'];

        //  dd([$extraData, $response]);
        return Excel::download(new AttendanceSummaryReportExportCollection($dataSet, $extraData), 'summaryReport' . date('Ym', strtotime($request->month)) . date('His') . '.xlsx');
    }

    public function summaryPdfExportFromCollection(Request $request)
    {
        set_time_limit(0);
        if ($request->from_date && $request->to_date) {
            $from_date = $request->from_date;
            $to_date = $request->to_date;
        } else {
            $from_date = date("01/m/Y");
            $to_date = date("t/m/Y");
        }

        $month = date('Y-m', strtotime(dateConvertFormtoDB($from_date)));
        $monthAndYear = explode('-', $month);
        $month_data = $monthAndYear[1];
        $dateObj = DateTime::createFromFormat('!m', $month_data);
        $monthName = $dateObj->format('F');

        $monthToDate = findFromDateToDateToAllDate(dateConvertFormtoDB($from_date), dateConvertFormtoDB($to_date));
        $leaveType = LeaveType::where('status', 1)->get();
        $result = [];
        if ($_GET) {
            $result = $this->attendanceRepository->findAttendanceSummaryReport($month, dateConvertFormtoDB($from_date), dateConvertFormtoDB($to_date));
        }
        $data = [
            'results' => $result,
            'month' => $request->month,
            'monthToDate' => $monthToDate,
            'leaveTypes' => $leaveType,
            'monthName' => $monthName,
            'from_date' => $from_date,
            'to_date' => $to_date,
        ];

        $pdf = \PDF::loadView('admin.attendance.report.pdf.summaryAttendancePdf', $data);
        $pdf->setPaper('A2', 'landscape');
        $pageName = "summary-attendance-" . date('Ym', strtotime($request->month)) . ".pdf";
        return $pdf->download($pageName);
    }
}
