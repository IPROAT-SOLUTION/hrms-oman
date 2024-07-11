<?php

namespace App\Http\Controllers\User;

use DateTime;
use Carbon\Carbon;
use App\Model\MsSql;
use App\Model\Notice;
use App\Model\OnDuty;
use App\Model\Warning;
use App\Model\Employee;
use App\Model\IpSetting;
use App\Model\LeaveType;
use App\Model\WorkShift;
use Carbon\CarbonPeriod;
use App\Model\Department;
use App\Model\Termination;
use App\Model\EmployeeAward;
use Illuminate\Http\Request;
use App\Model\LeavePermission;
use App\Model\LeaveApplication;
use App\Model\EmployeeAttendance;
use App\Model\EmployeeExperience;
use App\Model\EmployeePerformance;
use Illuminate\Support\Facades\DB;
use App\Http\Controllers\Controller;
use App\Model\EmpLeaveBalance;
use Illuminate\Support\Facades\Mail;
use App\Repositories\AttendanceRepository;
use App\Model\EmployeeEducationQualification;

class HomeController extends Controller
{

    protected $employeePerformance, $leaveApplication, $notice, $employeeExperience, $department, $employee, $employeeAward, $attendanceRepository, $warning, $termination;

    public function __construct(
        EmployeePerformance $employeePerformance,
        LeaveApplication $leaveApplication,
        Notice $notice,
        EmployeeExperience
        $employeeExperience,
        Department
        $department,
        Employee $employee,
        EmployeeAward
        $employeeAward,
        AttendanceRepository $attendanceRepository,
        Warning $warning,
        Termination $termination
    ) {
        $this->employeePerformance = $employeePerformance;
        $this->leaveApplication = $leaveApplication;
        $this->notice = $notice;
        $this->employeeExperience = $employeeExperience;
        $this->department = $department;
        $this->employee = $employee;
        $this->employeeAward = $employeeAward;
        $this->attendanceRepository = $attendanceRepository;
        $this->warning = $warning;
        $this->termination = $termination;
    }

    public function index()
    {
        $ip_setting = IpSetting::orderBy('id', 'desc')->first();
        $ip_attendance_status = $ip_check_status = 0;

        $login_employee = employeeInfo();
        $count_user_login_today = EmployeeAttendance::where('finger_print_id', '=', $login_employee[0]->finger_id)->whereDate('in_out_time', '=', date('Y-m-d'))->count();
        $last_log_date = MsSql::max('datetime');
        $setting_sync_live = DB::table('sync_to_live')->first();
        $notifications = auth()->user()->unreadNotifications;

        if ($ip_setting && $login_employee[0]->ip_attendance == 1) {
            // if 0 then attendance will not take
            $ip_attendance_status = $ip_setting->status;
            // if 0 then ip will not checked for attendance
            $ip_check_status = $ip_setting->ip_status;
        }

        $employeePerformance = $employeeAward = $employeeTotalAward = [];

        if (decrypt(session('logged_session_data.role_id')) == 3) {
            $hasSupervisorWiseEmployee = $this->employee->select('employee_id')->where('operation_manager_id', decrypt(session('logged_session_data.employee_id')))->pluck('employee_id')->toArray();
            $leaveApplication = [];
            $permissionApplication = [];
            // Leave
            $leaveApplication  =  LeaveApplication::with('employee', 'leaveType')->orderBy('leave_application_id', 'desc')->get();

            $leaveApplication = $leaveApplication->filter(function ($q) {
                if (decrypt(session('logged_session_data.role_id')) == 1) {
                    return $q;
                } else {
                    if ($q->employee->operation_manager_id == decrypt(session('logged_session_data.employee_id')) && $q->manager_status == 1) {
                        return $q;
                    } else  if ($q->employee->supervisor_id == decrypt(session('logged_session_data.employee_id')) && ($q->manager_status == 2 && $q->status == 1)) {
                        return $q;
                    }
                }
            })->values();

            $leaveApplication = $leaveApplication->transform(function ($q) {
                if ($q->employee->operation_manager_id == decrypt(session('logged_session_data.employee_id')) && $q->manager_status == 1) {
                    $q->action = true;
                    return $q;
                } else if ($q->employee->supervisor_id == decrypt(session('logged_session_data.employee_id')) && ($q->manager_status == 2 && $q->status == 1)) {
                    $q->action = true;
                    return $q;
                } else {
                    $q->action = false;
                    return $q;
                }
            });


            //Permission
            $permissionApplication  =  LeavePermission::with('employee')->orderBy('leave_permission_id', 'desc')->get();

            $permissionApplication = $permissionApplication->filter(function ($q) {
                if (decrypt(session('logged_session_data.role_id')) == 1) {
                    return $q;
                } else {
                    if ($q->employee->operation_manager_id == decrypt(session('logged_session_data.employee_id')) && $q->manager_status == 1) {
                        return $q;
                    } else  if ($q->employee->supervisor_id == decrypt(session('logged_session_data.employee_id')) && ($q->manager_status == 2 && $q->status == 1)) {
                        return $q;
                    }
                }
            })->values();

            $permissionApplication = $permissionApplication->transform(function ($q) {
                if ($q->employee->operation_manager_id == decrypt(session('logged_session_data.employee_id')) && $q->manager_status == 1) {
                    $q->action = true;
                    return $q;
                } else if ($q->employee->supervisor_id == decrypt(session('logged_session_data.employee_id')) && ($q->manager_status == 2 && $q->status == 1)) {
                    $q->action = true;
                    return $q;
                } else {
                    $q->action = false;
                    return $q;
                }
            });
            // $notice = $this->notice->with('createdBy')->orderBy('notice_id', 'DESC')->where('status', 'Published')->get();
            $start_time = WorkShift::orderBy('start_time', 'ASC')->first()->start_time;
            $minTime = date('Y-m-d H:i:s', strtotime('-120 minutes', strtotime($start_time)));

            $dailyData = MsSql::where('datetime', '>=', ($minTime))->where('type', 'IN')->whereIn('employee', array_values($hasSupervisorWiseEmployee))
                ->groupBy('ms_sql.ID')->orderBy('ms_sql.datetime')->get();

            $notice = $this->notice->with('createdBy')->orderBy('notice_id', 'DESC')->where('status', 'Published')->get();

            // date of birth in this month
            $attendanceData = $this->attendanceRepository->getEmployeeMonthlyAttendance(date("Y-m-01"), date("Y-m-d"), decrypt(session('logged_session_data.employee_id')));
            $employeeInfo = $this->employee->with('designation')->where('employee_id', decrypt(session('logged_session_data.employee_id')))->first();
            $firstDayThisMonth = date('Y-m-d');
            $lastDayThisMonth = date("Y-m-d", strtotime("+1 month", strtotime($firstDayThisMonth)));

            $from_date_explode = explode('-', $firstDayThisMonth);
            $from_day = $from_date_explode[2];
            $from_month = $from_date_explode[1];
            $concatFormDayAndMonth = $from_month . '-' . $from_day;

            $to_date_explode = explode('-', $lastDayThisMonth);
            $to_day = $to_date_explode[2];
            $to_month = $to_date_explode[1];
            $concatToDayAndMonth = $to_month . '-' . $to_day;

            $upcoming_birtday = Employee::orderBy('date_of_birth', 'desc')->whereRaw("DATE_FORMAT(date_of_birth, '%m-%d') >= '" . $concatFormDayAndMonth . "' AND DATE_FORMAT(date_of_birth, '%m-%d') <= '" . $concatToDayAndMonth . "' ")->get();
            $employeeLeave = EmpLeaveBalance::with('leaveType')->where('employee_id', decrypt(session('logged_session_data.employee_id')))->get();


            $data = [
                'attendanceData' => $attendanceData,
                'notice' => $notice,
                'employeeInfo' => $employeeInfo,
                'leaveApplication' => $leaveApplication,
                'permissionApplication' => $permissionApplication,
                // 'ondutyApplication' => $ondutyApplication,
                'upcoming_birtday' => $upcoming_birtday,
                'ip_attendance_status' => $ip_attendance_status,
                'ip_check_status' => $ip_check_status,
                'count_user_login_today' => $count_user_login_today,
                'dailyAttendanceData' => isset($dailyAttendanceData) ? $dailyAttendanceData : 0,
                'dailyData' => $dailyData,
                'last_log_date' => $last_log_date,
                'setting_sync_live' => $setting_sync_live,
                'notifications' => $notifications,
                'employeeLeave' => $employeeLeave,

            ];

            return view('admin.managerhome', $data);
        } elseif (decrypt(session('logged_session_data.role_id')) != 1 && decrypt(session('logged_session_data.role_id')) != 2) {

            $attendanceData = $this->attendanceRepository->getEmployeeMonthlyAttendance(date("Y-m-01"), date("Y-m-d"), decrypt(session('logged_session_data.employee_id')));

            // $employeePerformance = $this->employeePerformance->select('employee_performance.*', DB::raw('AVG(employee_performance_details.rating) as rating'))
            //     ->with(['employee' => function ($d) {
            //         $d->with('department');
            //     }])
            //     ->join('employee_performance_details', 'employee_performance_details.employee_performance_id', '=', 'employee_performance.employee_performance_id')
            //     ->where('month', function ($query) {
            //         $query->select(DB::raw('MAX(`month`) AS month'))->from('employee_performance');
            //     })->where('employee_performance.status', 1)->groupBy('employee_id')->get();

            // $employeeTotalAward = $this->employeeAward
            //     ->select(DB::raw('count(*) as totalAward'))
            //     ->where('employee_id', decrypt(session('logged_session_data.employee_id')))
            //     ->whereBetween('month', [date("Y-01"), date("Y-12")])
            //     ->first();

            $notice = $this->notice->with('createdBy')->orderBy('notice_id', 'DESC')->where('status', 'Published')->get();

            $terminationData = $this->termination->with('terminateBy')->where('terminate_to', decrypt(session('logged_session_data.employee_id')))->first();

            $hasSupervisorWiseEmployee = $this->employee->select('employee_id')->where('supervisor_id', decrypt(session('logged_session_data.employee_id')))->get()->toArray();
            if (count($hasSupervisorWiseEmployee) == 0) {
                $leaveApplication = [];
            } else {
                $leaveApplication = $this->leaveApplication->with(['employee', 'leaveType'])
                    ->whereIn('employee_id', array_values($hasSupervisorWiseEmployee))
                    ->where('status', 1)
                    ->orderBy('status', 'asc')
                    ->orderBy('leave_application_id', 'desc')
                    ->get();
            }

            $employeeInfo = $this->employee->with('designation')->where('employee_id', decrypt(session('logged_session_data.employee_id')))->first();

            $employeeTotalLeave = $this->leaveApplication->select(DB::raw('IFNULL(SUM(number_of_day), 0) as totalNumberOfDays'))
                ->where('employee_id', decrypt(session('logged_session_data.employee_id')))
                ->where('status', 2)
                ->whereBetween('approve_date', [date("Y-01-01"), date("Y-12-31")])
                ->first();

            $warning = $this->warning->with(['warningBy'])->where('warning_to', decrypt(session('logged_session_data.employee_id')))->get();

            // date of birth in this month

            $firstDayThisMonth = date('Y-m-d');
            $lastDayThisMonth = date("Y-m-d", strtotime("+1 month", strtotime($firstDayThisMonth)));
            $from_date_explode = explode('-', $firstDayThisMonth);
            $from_day = $from_date_explode[2];
            $from_month = $from_date_explode[1];
            $concatFormDayAndMonth = $from_month . '-' . $from_day;

            $to_date_explode = explode('-', $lastDayThisMonth);
            $to_day = $to_date_explode[2];
            $to_month = $to_date_explode[1];
            $concatToDayAndMonth = $to_month . '-' . $to_day;

            $upcoming_birtday = Employee::orderBy('date_of_birth', 'desc')->whereRaw("DATE_FORMAT(date_of_birth, '%m-%d') >= '" . $concatFormDayAndMonth . "' AND DATE_FORMAT(date_of_birth, '%m-%d') <= '" . $concatToDayAndMonth . "' ")->get();

            $employeeLeave = EmpLeaveBalance::with('leaveType')->where('employee_id', decrypt(session('logged_session_data.employee_id')))->get();

            $data = [
                'attendanceData' => $attendanceData,
                'employeePerformance' => $employeePerformance,
                'employeeTotalAward' => $employeeTotalAward,
                'notice' => $notice,
                'leaveApplication' => $leaveApplication,
                'employeeInfo' => $employeeInfo,
                'employeeTotalLeave' => $employeeTotalLeave,
                'warning' => $warning,
                'terminationData' => $terminationData,
                'upcoming_birtday' => $upcoming_birtday,
                'ip_attendance_status' => $ip_attendance_status,
                'ip_check_status' => $ip_check_status,
                'count_user_login_today' => $count_user_login_today,
                'last_log_date' => $last_log_date,
                'setting_sync_live' => $setting_sync_live,
                'notifications' => $notifications,
                'employeeLeave' => $employeeLeave,
            ];

            return view('admin.generalUserHome', $data);
        }

        // Leave
        $leaveApplication  =  LeaveApplication::with('employee', 'leaveType')->orderBy('leave_application_id', 'desc')->get();

        $leaveApplication = $leaveApplication->filter(function ($q) {
            if (decrypt(session('logged_session_data.role_id')) == 1) {
                return $q;
            } else {
                if ($q->employee->operation_manager_id == decrypt(session('logged_session_data.employee_id')) && $q->manager_status == 1) {
                    return $q;
                } else  if ($q->employee->supervisor_id == decrypt(session('logged_session_data.employee_id')) && ($q->manager_status == 2 && $q->status == 1)) {
                    return $q;
                }
            }
        })->values();

        $leaveApplication = $leaveApplication->transform(function ($q) {
            if ($q->employee->operation_manager_id == decrypt(session('logged_session_data.employee_id')) && $q->manager_status == 1) {
                $q->action = true;
                return $q;
            } else if ($q->employee->supervisor_id == decrypt(session('logged_session_data.employee_id')) && ($q->manager_status == 2 && $q->status == 1)) {
                $q->action = true;
                return $q;
            } else {
                $q->action = false;
                return $q;
            }
        });


        //Permission
        $permissionApplication  =  LeavePermission::with('employee')->orderBy('leave_permission_id', 'desc')->get();

        $permissionApplication = $permissionApplication->filter(function ($q) {
            if (decrypt(session('logged_session_data.role_id')) == 1) {
                return $q;
            } else {
                if ($q->employee->operation_manager_id == decrypt(session('logged_session_data.employee_id')) && $q->manager_status == 1) {
                    return $q;
                } else  if ($q->employee->supervisor_id == decrypt(session('logged_session_data.employee_id')) && ($q->manager_status == 2 && $q->status == 1)) {
                    return $q;
                }
            }
        })->values();

        $permissionApplication = $permissionApplication->transform(function ($q) {
            if ($q->employee->operation_manager_id == decrypt(session('logged_session_data.employee_id')) && $q->manager_status == 1) {
                $q->action = true;
                return $q;
            } else if ($q->employee->supervisor_id == decrypt(session('logged_session_data.employee_id')) && ($q->manager_status == 2 && $q->status == 1)) {
                $q->action = true;
                return $q;
            } else {
                $q->action = false;
                return $q;
            }
        });

        $attendanceData = MsSql::whereDate('datetime',  date('Y-m-d'))
            ->groupBy('ms_sql.ID')->orderBy('ms_sql.datetime')->whereHas('employeeData')->with('employeeData')->get();

        $totalEmployee = $this->employee->where('status', 1)->where('employee_id', '!=', 1)->count();

        // $employeePerformance = $this->employeePerformance->select('employee_performance.*', DB::raw('AVG(employee_performance_details.rating) as rating'))
        //     ->with(['employee' => function ($d) {
        //         $d->with('department');
        //     }])
        //     ->join('employee_performance_details', 'employee_performance_details.employee_performance_id', '=', 'employee_performance.employee_performance_id')
        //     ->where('month', function ($query) {
        //         $query->select(DB::raw('MAX(`month`) AS month'))->from('employee_performance');
        //     })->where('employee_performance.status', 1)->groupBy('employee_id')->get();

        // $employeeAward = $this->employeeAward->with(['employee' => function ($d) {
        //     $d->with('department');
        // }])->limit(10)->orderBy('employee_award_id', 'DESC')->get();

        $notice = $this->notice->with('createdBy')->orderBy('notice_id', 'DESC')->where('status', 'Published')->get();

        // date of birth in this month
        $date = date('Y-m-d');

        $firstDayThisMonth = date('Y-m-d');
        $lastDayThisMonth = date("Y-m-d", strtotime("+1 month", strtotime($firstDayThisMonth)));

        $from_date_explode = explode('-', $firstDayThisMonth);
        $from_day = $from_date_explode[2];
        $from_month = $from_date_explode[1];
        $concatFormDayAndMonth = $from_month . '-' . $from_day;

        $to_date_explode = explode('-', $lastDayThisMonth);
        $to_day = $to_date_explode[2];
        $to_month = $to_date_explode[1];
        $concatToDayAndMonth = $to_month . '-' . $to_day;

        $upcoming_birtday = Employee::orderBy('date_of_birth', 'desc')->whereRaw("DATE_FORMAT(date_of_birth, '%m-%d') >= '" . $concatFormDayAndMonth . "' AND DATE_FORMAT(date_of_birth, '%m-%d') <= '" . $concatToDayAndMonth . "' ")->get();
        $employee_doc_expiry = Employee::where('status', 1)->whereRaw(' ( DATE_SUB(expiry_date8,INTERVAL 6 MONTH)  <= "' . $date . '" AND expiry_date8 IS NOT NULL   AND expiry_date8 >="' . $date . '"  ) or  
        ( DATE_SUB(expiry_date9,INTERVAL 3 MONTH)  <=  "' . $date . '" AND expiry_date9 IS NOT NULL  AND expiry_date9 >="' . $date . '"  ) or  
        ( DATE_SUB(expiry_date10,INTERVAL 3 MONTH) <=  "' . $date . '" AND expiry_date10 IS NOT NULL  AND expiry_date10 >="' . $date . '"  ) or  
        ( DATE_SUB(expiry_date16,INTERVAL 3 MONTH) <=  "' . $date . '" AND expiry_date16 IS NOT NULL  AND expiry_date16 >="' . $date . '"  ) or  
        ( DATE_SUB(expiry_date17,INTERVAL 3 MONTH) <=  "' . $date . '" AND expiry_date17 IS NOT NULL  AND expiry_date17 >="' . $date . '"  ) or  
        ( DATE_SUB(expiry_date18,INTERVAL 3 MONTH) <=  "' . $date . '" AND expiry_date18 IS NOT NULL  AND expiry_date18 >="' . $date . '"  ) or  
        ( DATE_SUB(expiry_date19,INTERVAL 3 MONTH) <=  "' . $date . '" AND expiry_date19 IS NOT NULL  AND expiry_date19 >="' . $date . '"  ) or  
        ( DATE_SUB(expiry_date20,INTERVAL 3 MONTH) <=  "' . $date . '" AND expiry_date20 IS NOT NULL  AND expiry_date20 >="' . $date . '"  ) or  
        ( DATE_SUB(expiry_date21,INTERVAL 3 MONTH) <=  "' . $date . '" AND expiry_date21 IS NOT NULL  AND expiry_date21 >="' . $date . '"  ) or  
        ( DATE_SUB(expiry_date11,INTERVAL 3 MONTH) <=  "' . $date . '" AND expiry_date11 IS NOT NULL   AND expiry_date11 >="' . $date . '" )')->get();

        $employee_doc_expired = Employee::where('status', 1)->whereRaw('
        ( expiry_date8  < "' . $date . '" AND expiry_date8 IS NOT NULL    ) or
        ( expiry_date9  <  "' . $date . '" AND expiry_date9 IS NOT NULL   ) or
        ( expiry_date10 <  "' . $date . '" AND expiry_date10 IS NOT NULL  ) or
        ( expiry_date11 <  "' . $date . '" AND expiry_date11 IS NOT NULL  )')->get();

        $leave = $this->leaveApplication
            ->whereRaw("application_from_date <= '" . date('Y-m-d') . "' AND application_to_date >= '" . date('Y-m-d') . "'")
            ->where('status', 2)
            ->where('manager_status', 2)
            ->count();

        $employeeLeave = EmpLeaveBalance::with('leaveType')->where('employee_id', decrypt(session('logged_session_data.employee_id')))->get();

        $data = [
            'attendanceData' => $attendanceData,
            'totalEmployee' => $totalEmployee,
            'totalAttendance' => count($attendanceData),
            'totalAbsent' => $totalEmployee - count($attendanceData),
            'employeePerformance' => $employeePerformance,
            'employeeAward' => $employeeAward,
            'notice' => $notice,
            'leaveApplication' => $leaveApplication,
            'permissionApplication' => $permissionApplication,
            'upcoming_birtday' => $upcoming_birtday,
            'ip_attendance_status' => $ip_attendance_status,
            'ip_check_status' => $ip_check_status,
            'count_user_login_today' => $count_user_login_today,
            'last_log_date' => $last_log_date,
            'setting_sync_live' => $setting_sync_live,
            'notifications' => $notifications,
            'employeeDocumentExpiry' => $employee_doc_expiry,
            'employeeDocumentExpired' => $employee_doc_expired,
            'totalLeave' => $leave,
            'employeeLeave' => $employeeLeave,
        ];

        return view('admin.adminhome', $data);
    }


    public function profile()
    {
        $employeeInfo = Employee::where('employee.employee_id', decrypt(session('logged_session_data.employee_id')))->first();
        $employeeExperience = EmployeeExperience::where('employee_id', decrypt(session('logged_session_data.employee_id')))->get();
        $employeeEducation = EmployeeEducationQualification::where('employee_id', decrypt(session('logged_session_data.employee_id')))->get();

        return view('admin.user.user.profile', ['employeeInfo' => $employeeInfo, 'employeeExperience' => $employeeExperience, 'employeeEducation' => $employeeEducation]);
    }

    public function mail()
    {

        $user = array(
            'name' => "Learning Laravel",
        );

        Mail::send('emails.mailExample', $user, function ($message) {
            $message->to("hari9578@gmail.com");
            $message->subject('E-Mail Example');
        });

        return "Your email has been sent successfully";
    }

    public function attendanceSummaryReport(Request $request)
    {

        $month = date("Y-m");

        $monthAndYear = explode('-', $month);
        $month_data = $monthAndYear[1];
        $dateObj = DateTime::createFromFormat('!m', $month_data);
        $monthName = $dateObj->format('F');

        $monthToDate = findMonthToAllDate($month);
        $leaveType = LeaveType::where('status', 1)->get();
        $result = $this->attendanceRepository->findAttendanceSummaryReport($month);

        return ['results' => $result, 'monthToDate' => $monthToDate, 'month' => $month, 'leaveTypes' => $leaveType, 'monthName' => $monthName];
    }
}
