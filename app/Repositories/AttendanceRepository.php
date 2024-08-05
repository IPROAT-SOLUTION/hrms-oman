<?php

namespace App\Repositories;

use App\User;
use App\Model\Employee;
use App\Model\WeeklyHoliday;
use App\Model\HolidayDetails;
use App\Model\LeaveApplication;
use Illuminate\Support\Facades\DB;
use App\Lib\Enumerations\UserStatus;
use App\Lib\Enumerations\LeaveStatus;
use App\Model\LeavePermission;

class AttendanceRepository
{

    public function getEmployeeDailyAttendance($date = false, $department_id, $attendance_status)
    {
        if ($date) {
            $data = dateConvertFormtoDB($date);
        } else {
            $data = date("Y-m-d");
        }

        $queryResults = DB::select("call `SP_DepartmentDailyAttendance`('" . $data . "', '" . $department_id . "','" . $attendance_status . "')");

        $results = [];

        $admins = User::whereIn('role_id', [1, 2])->pluck('user_id')->toArray();

        foreach ($queryResults as $value) {

            $tempArr = [];

            if (!in_array($value->user_id, $admins)) {
                $employeePublicHolidayRecords = $this->getEmployeePublicHolidayRecord($data, $data, $value->employee_id);
                $employeeWeeklyHolidayRecords = $this->getEmployeeWeeklyHolidayRecord($data, $data, $value->employee_id);
                $employeeLeaveRecords = $this->getEmployeeLeaveRecord($data, $data, $value->employee_id, $employeeWeeklyHolidayRecords, $employeePublicHolidayRecords);

                if (in_array($data, $employeeLeaveRecords['leave'])) {
                    if ($employeeLeaveRecords['number_of_day'][$data] == 1) {
                        $value->attendance_status = 15;
                    } else {
                        if ($value->attendance_status == 1) {
                            $value->attendance_status = 16;
                        } else {
                            $value->attendance_status = 14;
                        }
                    }
                } elseif (in_array($data, $employeeWeeklyHolidayRecords)) {
                    $value->attendance_status = 13;
                } elseif (in_array($data, $employeePublicHolidayRecords)) {
                    $value->attendance_status = 4;
                }

                $value->permission_duration = $this->getEmployeePermissionRecord($data, $value->employee_id);

                $tempArr = $value;

                $results[$value->department_name][] = $tempArr;
            }
        }

        return $results;
    }

    public function getEmployeeMonthlyAttendance($from_date, $to_date, $employee_id)
    {
        $monthlyAttendanceData = DB::select("CALL `SP_monthlyAttendance`('" . $employee_id . "','" . $from_date . "','" . $to_date . "')");
        $workingDates = findAllDates($from_date, $to_date);
        $employeeWeeklyHolidayRecords = $this->getEmployeeWeeklyHolidayRecord($from_date, $to_date, $employee_id);
        $employeePublicHolidayRecords = $this->getEmployeePublicHolidayRecord($from_date, $to_date, $employee_id);
        $employeeLeaveRecords = $this->getEmployeeLeaveRecord($from_date, $to_date, $employee_id, $employeeWeeklyHolidayRecords, $employeePublicHolidayRecords);

        $dataFormat = [];
        $tempArray = [];
        $present = null;

        if ($workingDates && $monthlyAttendanceData) {
            foreach ($workingDates as $data) {

                $flag = 0;

                foreach ($monthlyAttendanceData as $value) {
                    if ($data == $value->date && ($value->in_time != null || $value->out_time != null)) {
                        $flag = 1;
                        break;
                    }
                }

                $tempArray['total_present'] = null;

                if ($flag == 0) {

                    $tempArray['employee_id'] = $value->employee_id;
                    $tempArray['fullName'] = $value->fullName ?? '';
                    $tempArray['department_name'] = $value->department_name ?? '';
                    $tempArray['designation_name'] = $value->designation_name ?? '';
                    $tempArray['finger_print_id'] = $value->finger_print_id ?? '';
                    $tempArray['date'] = $data;
                    $tempArray['shift_name'] = $value->shift_name ?? '';
                    $tempArray['working_time'] = '';
                    $tempArray['over_time'] = '';
                    $tempArray['in_time'] = '';
                    $tempArray['out_time'] = '';
                    $tempArray['lateCountTime'] = '';
                    $tempArray['ifLate'] = '';
                    $tempArray['totalLateTime'] = '';
                    $tempArray['workingHour'] = '';
                    $tempArray['approved_over_time'] = '';
                    $tempArray['total_present'] = $present;
                    $tempArray['approved_over_time'] = '';
                    $tempArray['permission_duration'] = $this->getEmployeePermissionRecord($data, $value->employee_id);

                    if (in_array($data, $employeeLeaveRecords['leave'])) {
                        if ($employeeLeaveRecords['number_of_day'][$data] == 1) {
                            $tempArray['action'] = 'FullDayLeave';
                        } else {
                            $tempArray['action'] = 'HalfDayLeave';
                        }
                    } elseif (in_array($data, $employeeWeeklyHolidayRecords)) {
                        $tempArray['action'] = 'WeeklyHoliday';
                    } elseif (in_array($data, $employeePublicHolidayRecords)) {
                        $tempArray['action'] = 'PublicHoliday';
                    } else {
                        $tempArray['action'] = 'Absence';
                    }
                    $dataFormat[] = $tempArray;
                } else {
                    $tempArray['total_present'] = $present += 1;
                    $tempArray['employee_id'] = $value->employee_id;
                    $tempArray['fullName'] = $value->fullName ?? '';
                    $tempArray['department_name'] = $value->department_name ?? '';
                    $tempArray['designation_name'] = $value->designation_name ?? '';
                    $tempArray['finger_print_id'] = $value->finger_print_id ?? '';
                    $tempArray['shift_name'] = $value->shift_name;
                    $tempArray['date'] = $value->date;
                    $tempArray['working_time'] = $value->working_time;
                    $tempArray['over_time'] = $value->over_time;
                    $tempArray['in_time'] = $value->in_time;
                    $tempArray['out_time'] = $value->out_time;
                    $tempArray['lateCountTime'] = $value->lateCountTime;
                    $tempArray['ifLate'] = $value->ifLate;
                    $tempArray['totalLateTime'] = $value->totalLateTime;
                    $tempArray['workingHour'] = $value->workingHour;
                    $tempArray['approved_over_time'] = $value->approved_over_time;
                    $tempArray['permission_duration'] = $this->getEmployeePermissionRecord($value->date, $value->employee_id);

                    if (in_array($data, $employeeLeaveRecords['leave']) && $employeeLeaveRecords['number_of_day'][$data] == 0.5) {
                        $tempArray['action'] = 'HalfDayPresent';
                    } else {
                        $tempArray['action'] = 'Present';
                    }
                    $dataFormat[] = $tempArray;
                }
            }
        }
        return $dataFormat;
    }


    public function findAttendanceSummaryReport($month, $start_date, $end_date)
    {
        $data = findFromDateToDateToAllDate($start_date, $end_date);

        $attendance = DB::table('view_employee_in_out_data')->select('finger_print_id', 'date', 'in_time', 'shift_name', 'inout_status', 'out_time', 'working_time', 'attendance_status')->whereBetween('date', [$start_date, $end_date])->get();

        $employees = Employee::whereHas('userName', function ($q) {
            $q->where('role_id', '>=', 3);
        })->select('first_name', 'last_name', 'employee.updated_at', 'gender', 'status', 'department_name', 'branch_name', 'designation_name', 'finger_id', 'employee_id')
            ->join('designation', 'designation.designation_id', 'employee.designation_id')
            ->join('department', 'department.department_id', 'employee.department_id')
            ->join('branch', 'branch.branch_id', 'employee.branch_id')
            ->orderBy('branch.branch_name', 'ASC')->where('employee.status', UserStatus::$ACTIVE)->get();

        $leave = LeaveApplication::with('leaveType')
            // ->whereRaw("application_from_date >= '" . $start_date . "' and application_to_date <=  '" . $end_date . "'")
            ->where('status', LeaveStatus::$APPROVE)
            ->get();

        $govtHolidays = DB::select(DB::raw('call SP_getHoliday("' . $start_date . '","' . $end_date . '")'));
        $dataFormat = [];
        $tempArray = [];

        foreach ($employees as $employee) {

            // $weeklyHolidaysDates = WeeklyHoliday::where('employee_id', $employee->employee_id)->where('month', date('Y-m', strtotime($start_date)))->first();
            $weeklyHolidays = DB::select(DB::raw('call SP_getWeeklyHoliday("' . $employee->employee_id . '","' . date('Y-m', strtotime($start_date)) . '")'));

            foreach ($data as $key => $value) {
                $tempArray['employee_id'] = $employee->employee_id;
                $tempArray['finger_id'] = $employee->finger_id;
                $tempArray['fullName'] = $employee->first_name . "" . $employee->last_name;
                $tempArray['designation_name'] = $employee->designation_name;
                $tempArray['department_name'] = $employee->department_name;
                $tempArray['gender'] = $employee->gender;
                $tempArray['status'] = $employee->status;
                $tempArray['date'] = $value['date'];
                $tempArray['day'] = $value['day'];
                $tempArray['day_name'] = $value['day_name'];

                $hasAttendance = $this->hasEmployeeAttendance($attendance, $employee->finger_id, $value['date']);


                if ($hasAttendance['status'] == true) {
                    $hasLeave = $this->ifEmployeeWasLeave($leave, $employee->employee_id, $value['date']);
                    $tempArray['attendance_status'] = 'present';
                    $tempArray['leave_type'] = '';
                    $tempArray['day'] = '';
                    $tempArray['gov_day_worked'] = 'no';
                    $tempArray['shift_name'] = $hasAttendance['shift_name'];
                    $tempArray['inout_status'] = $hasAttendance['inout_status'];
                } else {
                    $ifHoliday = $this->ifHoliday($govtHolidays, $value['date'], $employee->employee_id, $weeklyHolidays, []);
                    $hasLeave = $this->ifEmployeeWasLeave($leave, $employee->employee_id, $value['date']);
                    if ($hasLeave) {
                        $day = $hasLeave['day'];
                        $type = $hasLeave['leaveType'];
                        // if ($ifHoliday['weekly_holiday'] == true) {
                        //     $tempArray['attendance_status'] = 'holiday';
                        //     $tempArray['gov_day_worked'] = 'no';
                        //     $tempArray['leave_type'] = '';
                        //     $tempArray['day'] = '';
                        //     $tempArray['inout_status'] = '';
                        // } elseif ($ifHoliday['govt_holiday'] == true) {
                        //     $tempArray['attendance_status'] = 'publicHoliday';
                        //     $tempArray['gov_day_worked'] = 'no';
                        //     $tempArray['leave_type'] = '';
                        //     $tempArray['shift_name'] = '';
                        //     $tempArray['day'] = '';
                        //     $tempArray['inout_status'] = '';
                        // } else {
                        $tempArray['inout_status'] = '';
                        $tempArray['attendance_status'] = 'leave';
                        $tempArray['gov_day_worked'] = 'no';
                        $tempArray['leave_type'] = "{$type}";
                        $tempArray['day'] = $day < 1 ? "HL" : "FL";
                        $tempArray['shift_name'] = '';
                        // }
                    } else {
                        if ($ifHoliday['weekly_holiday'] == true) {
                            $tempArray['attendance_status'] = 'holiday';
                            $tempArray['gov_day_worked'] = 'no';
                            $tempArray['leave_type'] = '';
                            $tempArray['shift_name'] = '';
                            $tempArray['day'] = '';
                            $tempArray['inout_status'] = '';
                        } elseif ($ifHoliday['govt_holiday'] == true) {
                            $tempArray['attendance_status'] = 'publicHoliday';
                            $tempArray['gov_day_worked'] = 'no';
                            $tempArray['leave_type'] = '';
                            $tempArray['shift_name'] = '';
                            $tempArray['day'] = '';
                            $tempArray['inout_status'] = '';
                        } else {
                            $tempArray['attendance_status'] = 'absence';
                            $tempArray['gov_day_worked'] = 'no';
                            $tempArray['leave_type'] = '';
                            $tempArray['shift_name'] = '';
                            $tempArray['day'] = '';
                            $tempArray['inout_status'] = '';
                        }
                    }
                }

                $dataFormat[$employee->finger_id][] = $tempArray;
            }
        }

        return $dataFormat;
    }


    public function findAttendanceMusterReport($start_date, $end_date, $employee_id = '', $department_id = '', $branch_id = '')
    {
        $data = findMonthFromToDate($start_date, $end_date);

        $qry = '1 ';

        if ($employee_id != '') {
            $qry .= ' AND employee.employee_id=' . $employee_id;
        }
        if ($department_id != '') {
            $qry .= ' AND employee.department_id=' . $department_id;
        }
        if ($branch_id != '') {
            $qry .= ' AND employee.branch_id=' . $branch_id;
        }

        $employees = Employee::whereHas('userName', function ($q) {
            $q->where('role_id', '>=', 3);
        })->select(DB::raw('CONCAT(COALESCE(employee.first_name,\'\'),\' \',COALESCE(employee.last_name,\'\')) AS fullName'), 'designation_name', 'department_name', 'branch_name', 'finger_id', 'employee_id')
            ->join('designation', 'designation.designation_id', 'employee.designation_id')
            ->join('department', 'department.department_id', 'employee.department_id')
            ->join('branch', 'branch.branch_id', 'employee.branch_id')->orderBy('branch.branch_name', 'ASC')->whereRaw($qry)
            ->where('status', UserStatus::$ACTIVE)->get();

        $attendance = DB::table('view_employee_in_out_data')->whereBetween('date', [$start_date, $end_date])->get();

        $dataFormat = [];
        $tempArray = [];

        foreach ($employees as $employee) {

            $employeeWeeklyHolidayRecords = $this->getEmployeeWeeklyHolidayRecord($start_date, $end_date, $employee->employee_id);
            $employeePublicHolidayRecords = $this->getEmployeePublicHolidayRecord($start_date, $end_date, $employee->employee_id);
            // $employeeLeaveRecords = $this->getEmployeeLeaveRecord($start_date, $end_date, $employee->employee_id, $employeeWeeklyHolidayRecords, $employeePublicHolidayRecords);

            $leave = LeaveApplication::with('leaveType')
                // ->whereRaw("application_from_date >= '" . $start_date . "' and application_to_date <=  '" . $end_date . "'")
                ->where('status', LeaveStatus::$APPROVE)
                ->where('employee_id', $employee->employee_id)
                ->get();

            foreach ($data as $key => $value) {

                $tempArray['employee_id'] = $employee->employee_id;
                $tempArray['finger_id'] = $employee->finger_id;
                $tempArray['fullName'] = $employee->fullName;
                $tempArray['designation_name'] = $employee->designation_name;
                $tempArray['department_name'] = $employee->department_name;
                $tempArray['branch_name'] = $employee->branch_name;
                $tempArray['date'] = $value['date'];
                $tempArray['day'] = $value['day'];
                $tempArray['day_name'] = $value['day_name'];

                $hasAttendance = $this->hasEmployeeMusterAttendance($attendance, $employee->finger_id, $value['date']);
                $hasLeave = $this->ifEmployeeWasLeave($leave, $employee->employee_id, $value['date']);

                if ($hasAttendance['in_time']) {
                    $tempArray['attendance_status'] = 'P';
                    $tempArray['shift_name'] = $hasAttendance['shift_name'];
                    $tempArray['in_time'] = $hasAttendance['in_time'];
                    $tempArray['out_time'] = $hasAttendance['out_time'];
                    $tempArray['working_time'] = $hasAttendance['working_time'];
                    $tempArray['over_time'] = $hasAttendance['over_time'];
                    $tempArray['approved_over_time'] = $hasAttendance['approved_over_time'];
                    $tempArray['employee_attendance_id'] = $hasAttendance['employee_attendance_id'];
                    $tempArray['leave_type'] = '';
                } else if ($hasLeave) {
                    $day = $hasLeave['day'];
                    $type = acronym($hasLeave['leaveType']);
                    $tempArray['attendance_status'] = $day < 1 ? "HL" : "FL";
                    $tempArray['leave_type'] = "{$type}";
                    $tempArray['shift_name'] = $hasAttendance['shift_name'];
                    $tempArray['in_time'] = $hasAttendance['in_time'];
                    $tempArray['out_time'] = $hasAttendance['out_time'];
                    $tempArray['working_time'] = $hasAttendance['working_time'];
                    $tempArray['over_time'] = $hasAttendance['over_time'];
                    $tempArray['approved_over_time'] = $hasAttendance['approved_over_time'];
                    $tempArray['employee_attendance_id'] = $hasAttendance['employee_attendance_id'];
                } elseif (in_array($value['date'], $employeeWeeklyHolidayRecords)) {
                    $tempArray['attendance_status'] = 'WH';
                    $tempArray['shift_name'] = $hasAttendance['shift_name'];
                    $tempArray['in_time'] = $hasAttendance['in_time'];
                    $tempArray['out_time'] = $hasAttendance['out_time'];
                    $tempArray['working_time'] = $hasAttendance['working_time'];
                    $tempArray['over_time'] = $hasAttendance['over_time'];
                    $tempArray['approved_over_time'] = $hasAttendance['approved_over_time'];
                    $tempArray['employee_attendance_id'] = $hasAttendance['employee_attendance_id'];
                    $tempArray['leave_type'] = '';
                } elseif (in_array($value['date'], $employeePublicHolidayRecords)) {
                    $tempArray['attendance_status'] = 'PH';
                    $tempArray['shift_name'] = $hasAttendance['shift_name'];
                    $tempArray['in_time'] = $hasAttendance['in_time'];
                    $tempArray['out_time'] = $hasAttendance['out_time'];
                    $tempArray['working_time'] = $hasAttendance['working_time'];
                    $tempArray['over_time'] = $hasAttendance['over_time'];
                    $tempArray['approved_over_time'] = $hasAttendance['approved_over_time'];
                    $tempArray['employee_attendance_id'] = $hasAttendance['employee_attendance_id'];
                    $tempArray['leave_type'] = '';
                } else {
                    $tempArray['attendance_status'] = 'AA';
                    $tempArray['shift_name'] = '';
                    $tempArray['in_time'] = '';
                    $tempArray['out_time'] = '';
                    $tempArray['over_time'] = '';
                    $tempArray['approved_over_time'] = '';
                    $tempArray['working_time'] = '';
                    $tempArray['employee_attendance_id'] = '';
                    $tempArray['leave_type'] = '';
                }

                $dataFormat[$employee->finger_id][] = $tempArray;
            }
        }

        return $dataFormat;
    }

    public function findAttendanceMusterReportExcelDump($start_date, $end_date, $employee_id, $department_id, $branch_id)
    {
        $data = findMonthFromToDate($start_date, $end_date);

        $qry = '1 ';

        if ($employee_id != '') {
            $qry .= ' AND employee.employee_id=' . $employee_id;
        }
        if ($department_id != '') {
            $qry .= ' AND employee.department_id=' . $department_id;
        }
        if ($branch_id != '') {
            $qry .= ' AND employee.branch_id=' . $branch_id;
        }

        $employees = Employee::whereHas('userName', function ($q) {
            $q->where('role_id', '>=', 3);
        })->select(DB::raw('CONCAT(COALESCE(employee.first_name,\'\'),\' \',COALESCE(employee.last_name,\'\')) AS fullName'), 'designation_name', 'department_name', 'branch_name', 'finger_id', 'employee_id')
            ->join('designation', 'designation.designation_id', 'employee.designation_id')
            ->join('department', 'department.department_id', 'employee.department_id')
            ->join('branch', 'branch.branch_id', 'employee.branch_id')->orderBy('branch.branch_name', 'ASC')->whereRaw($qry)
            ->where('status', UserStatus::$ACTIVE)->get();

        $attendance = DB::table('view_employee_in_out_data')->whereBetween('date', [$start_date, $end_date])->get();

        $dataFormat = [];
        $tempArray = [];

        foreach ($employees as $employee) {

            $employeeWeeklyHolidayRecords = $this->getEmployeeWeeklyHolidayRecord($start_date, $end_date, $employee->employee_id);
            $employeePublicHolidayRecords = $this->getEmployeePublicHolidayRecord($start_date, $end_date, $employee->employee_id);
            // $employeeLeaveRecords = $this->getEmployeeLeaveRecord($start_date, $end_date, $employee->employee_id, $employeeWeeklyHolidayRecords, $employeePublicHolidayRecords);

            $leave = LeaveApplication::with('leaveType')
                // ->whereRaw("application_from_date >= '" . $start_date . "' and application_to_date <=  '" . $end_date . "'")
                ->where('status', LeaveStatus::$APPROVE)
                ->where('employee_id', $employee->employee_id)
                ->get();

            foreach ($data as $key => $value) {

                $tempArray['employee_id'] = $employee->employee_id;
                $tempArray['finger_id'] = $employee->finger_id;
                $tempArray['fullName'] = $employee->fullName;
                $tempArray['designation_name'] = $employee->designation_name;
                $tempArray['department_name'] = $employee->department_name;
                $tempArray['branch_name'] = $employee->branch_name;
                $tempArray['date'] = $value['date'];
                $tempArray['day'] = $value['day'];
                $tempArray['day_name'] = $value['day_name'];

                $hasAttendance = $this->hasEmployeeMusterAttendance($attendance, $employee->finger_id, $value['date']);
                $hasLeave = $this->ifEmployeeWasLeave($leave, $employee->employee_id, $value['date']);

                if ($hasAttendance['in_time']) {
                    $tempArray['attendance_status'] = 'P';
                    $tempArray['shift_name'] = $hasAttendance['shift_name'];
                    $tempArray['in_time'] = $hasAttendance['in_time'];
                    $tempArray['out_time'] = $hasAttendance['out_time'];
                    $tempArray['working_time'] = $hasAttendance['working_time'];
                    $tempArray['over_time'] = $hasAttendance['over_time'];
                    $tempArray['approved_over_time'] = $hasAttendance['approved_over_time'];
                    $tempArray['employee_attendance_id'] = $hasAttendance['employee_attendance_id'];
                    $tempArray['leave_type'] = '';
                } else if ($hasLeave) {
                    $day = $hasLeave['day'];
                    $type = acronym($hasLeave['leaveType']);
                    $tempArray['attendance_status'] = $day < 1 ? "HL" : "FL";
                    $tempArray['leave_type'] = "{$type}";
                    $tempArray['shift_name'] = $hasAttendance['shift_name'];
                    $tempArray['in_time'] = $hasAttendance['in_time'];
                    $tempArray['out_time'] = $hasAttendance['out_time'];
                    $tempArray['working_time'] = $hasAttendance['working_time'];
                    $tempArray['over_time'] = $hasAttendance['over_time'];
                    $tempArray['approved_over_time'] = $hasAttendance['approved_over_time'];
                    $tempArray['employee_attendance_id'] = $hasAttendance['employee_attendance_id'];
                } elseif (in_array($value['date'], $employeeWeeklyHolidayRecords)) {
                    $tempArray['attendance_status'] = 'WH';
                    $tempArray['shift_name'] = $hasAttendance['shift_name'];
                    $tempArray['in_time'] = $hasAttendance['in_time'];
                    $tempArray['out_time'] = $hasAttendance['out_time'];
                    $tempArray['working_time'] = $hasAttendance['working_time'];
                    $tempArray['over_time'] = $hasAttendance['over_time'];
                    $tempArray['approved_over_time'] = $hasAttendance['approved_over_time'];
                    $tempArray['employee_attendance_id'] = $hasAttendance['employee_attendance_id'];
                    $tempArray['leave_type'] = '';
                } elseif (in_array($value['date'], $employeePublicHolidayRecords)) {
                    $tempArray['attendance_status'] = 'PH';
                    $tempArray['shift_name'] = $hasAttendance['shift_name'];
                    $tempArray['in_time'] = $hasAttendance['in_time'];
                    $tempArray['out_time'] = $hasAttendance['out_time'];
                    $tempArray['working_time'] = $hasAttendance['working_time'];
                    $tempArray['over_time'] = $hasAttendance['over_time'];
                    $tempArray['approved_over_time'] = $hasAttendance['approved_over_time'];
                    $tempArray['employee_attendance_id'] = $hasAttendance['employee_attendance_id'];
                    $tempArray['leave_type'] = '';
                } else {
                    $tempArray['attendance_status'] = 'AA';
                    $tempArray['shift_name'] = '';
                    $tempArray['in_time'] = '';
                    $tempArray['out_time'] = '';
                    $tempArray['over_time'] = '';
                    $tempArray['approved_over_time'] = '';
                    $tempArray['working_time'] = '';
                    $tempArray['employee_attendance_id'] = '';
                    $tempArray['leave_type'] = '';
                }

                $dataFormat[$employee->finger_id][] = $tempArray;
            }
        }

        $excelFormat = [];
        $days = [];
        $sl = 1;
        $dataset = [];

        $sl = 0;
        $emptyArr = ['', '', '', '', ''];

        foreach ($dataFormat as $key => $data) {
            $sl++;

            $shiftInfo = ['SHIFT NAME'];
            $inTimeInfo = ['IN TIME'];
            $outTimeInfo = ['OUT TIME'];
            $workingTimeInfo = ['WORKING TIME'];
            $overTimeInfo = ['OVER TIME'];
            for ($i = 0; $i < count($data); $i++) {
                $employeeData = [$sl, $data[0]['branch_name'], $data[0]['finger_id'], $data[0]['fullName'], $data[0]['department_name']];
                $shiftInfo[] = $data[$i]['shift_name'] != null ? $data[$i]['shift_name'] : $data[$i]['attendance_status'];
                $inTimeInfo[] = $data[$i]['in_time'] != null ? date('H:i', strtotime($data[$i]['in_time'])) : '00:00';
                $outTimeInfo[] = $data[$i]['out_time'] != null ? date('H:i', strtotime($data[$i]['out_time'])) : '00:00';
                $workingTimeInfo[] = $data[$i]['working_time'] != null ? date('H:i', strtotime($data[$i]['working_time'])) : '00:00';
                $overTimeInfo[] = $data[$i]['over_time'] != null ? date('H:i', strtotime($data[$i]['over_time'])) : '00:00';
            }

            $excelFormat[] = array_merge($employeeData, $shiftInfo);
            $excelFormat[] = array_merge($emptyArr, $inTimeInfo);
            $excelFormat[] = array_merge($emptyArr, $outTimeInfo);
            $excelFormat[] = array_merge($emptyArr, $workingTimeInfo);
            $excelFormat[] = array_merge($emptyArr, $overTimeInfo);
        }
        // dd($excelFormat);
        return $excelFormat;
    }

    public function hasEmployeeMusterAttendance($attendance, $finger_print_id, $date)
    {
        $dataFormat = [];
        $dataFormat['in_time'] = '';
        $dataFormat['out_time'] = '';
        $dataFormat['over_time'] = '';
        $dataFormat['working_time'] = '';
        // $dataFormat['over_time_status'] = '';
        $dataFormat['shift_name'] = '';
        $dataFormat['approved_over_time'] = "";
        $dataFormat['employee_attendance_id'] = '';

        foreach ($attendance as $key => $val) {
            // dd($val);
            if (($val->finger_print_id == $finger_print_id && $val->date == $date && $val->in_time != null)) {
                $dataFormat['shift_name'] = $val->shift_name;
                $dataFormat['in_time'] = $val->in_time;
                $dataFormat['out_time'] = $val->out_time;
                $dataFormat['over_time'] = $val->over_time;
                $dataFormat['working_time'] = $val->working_time;
                $dataFormat['approved_over_time'] = $val->approved_over_time;
                $dataFormat['employee_attendance_id'] = $val->employee_attendance_id;
                return $dataFormat;
            }
        }
        return $dataFormat;
    }

    public function ifPublicHoliday($govtHolidays, $date)
    {
        $govt_holidays = [];

        foreach ($govtHolidays as $holidays) {
            $start_date = $holidays->from_date;
            $end_date = $holidays->to_date;
            while (strtotime($start_date) <= strtotime($end_date)) {
                $govt_holidays[] = $start_date;
                $start_date = date("Y-m-d", strtotime("+1 day", strtotime($start_date)));
            }
        }

        foreach ($govt_holidays as $val) {
            if ($val == $date) {
                return true;
            }
        }
        return false;
    }

    public function number_of_working_days_date($from_date, $to_date, $employee_id)
    {
        $holidays = DB::select(DB::raw('call SP_getHoliday("' . $from_date . '","' . $to_date . '")'));
        $public_holidays = [];
        foreach ($holidays as $holidays) {
            $start_date = $holidays->from_date;
            $end_date = $holidays->to_date;
            while (strtotime($start_date) <= strtotime($end_date)) {
                $public_holidays[] = $start_date;
                $start_date = date("Y-m-d", strtotime("+1 day", strtotime($start_date)));
            }
        }

        // $weeklyHolidays     = DB::select(DB::raw('call SP_getWeeklyHoliday()'));
        // $weeklyHolidayArray = [];
        // foreach ($weeklyHolidays as $weeklyHoliday) {
        //     $weeklyHolidayArray[] = $weeklyHoliday->day_name;
        // }

        $weeklyHolidayArray = WeeklyHoliday::select('day_name')
            ->where('employee_id', $employee_id)
            ->where('month', date('Y-m', strtotime($from_date)))
            ->orWhere('month', date('Y-m', strtotime($to_date)))
            ->first();

        $target = strtotime($from_date);
        $workingDate = [];

        while ($target <= strtotime(date("Y-m-d", strtotime($to_date)))) {

            //get weekly  holiday name
            $timestamp = strtotime(date('Y-m-d', $target));
            $dayName = date("l", $timestamp);

            // if (!in_array(date('Y-m-d', $target), $public_holidays) && !in_array($dayName, $weeklyHolidayArray->toArray())) {
            //     array_push($workingDate, date('Y-m-d', $target));
            // }

            // if (!in_array(date('Y-m-d', $target), $public_holidays)) {
            //     array_push($workingDate, date('Y-m-d', $target));
            // }

            \array_push($workingDate, date('Y-m-d', $target));

            if (date('Y-m-d') <= date('Y-m-d', $target)) {
                break;
            }
            $target += (60 * 60 * 24);
        }
        return $workingDate;
    }

    public function hasEmployeeAttendance($attendance, $finger_print_id, $date)
    {
        $temp = [];
        $temp['status'] = false;
        $temp['shift_name'] = '';
        $temp['inout_status'] = '';
        $temp['attendance_status'] = '';
        // dump($attendance, $finger_print_id, $date);
        foreach ($attendance as $key => $val) {
            if (($val->finger_print_id == $finger_print_id && $val->date == $date && $val->in_time != null)) {
                $temp['status'] = true;
                $temp['shift_name'] = $val->shift_name;
                $temp['inout_status'] = $val->inout_status;
                $temp['attendance_status'] = $val->attendance_status;
                return $temp;
            }
        }
        return $temp;
    }

    public function ifEmployeeWasLeave($leave, $employee_id, $date)
    {
        $leaveRecord =  $leavetype = $data = [];
        foreach ($leave as $value) {
            if ($employee_id == $value->employee_id) {
                $start_date = $value->application_from_date;
                $end_date = $value->application_to_date;
                while (strtotime($start_date) <= strtotime($end_date)) {
                    $leavetype[$employee_id][$start_date] =  $value->leaveType->leave_type_name;
                    $leaveRecord[$employee_id][$start_date] = $value->number_of_day < 1 ? $value->number_of_day : 1.0;
                    $start_date = date("Y-m-d", strtotime("+1 day", strtotime($start_date)));
                }
            }
        }

        if (isset($leaveRecord[$employee_id][$date])) {
            $data['day'] = $leaveRecord[$employee_id][$date];
            $data['leaveType'] = $leavetype[$employee_id][$date];
            return $data;
        }

        return false;
    }

    public function ifHoliday($govtHolidays, $date, $employee_id, $weeklyHolidays, $weeklyHolidaysDates)
    {

        $govt_holidays = [];
        $result = [];
        $result['govt_holiday'] = false;
        $result['weekly_holiday'] = false;
        foreach ($govtHolidays as $holidays) {
            $start_date = $holidays->from_date;
            $end_date = $holidays->to_date;
            while (strtotime($start_date) <= strtotime($end_date)) {
                $govt_holidays[] = $start_date;
                $start_date = date("Y-m-d", strtotime("+1 day", strtotime($start_date)));
            }
        }

        foreach ($govt_holidays as $val) {
            if ($val == $date) {
                $result['govt_holiday'] = true;
            }
        }

        $timestamp = strtotime($date);
        $dayName = date("l", $timestamp);

        foreach ($weeklyHolidays as $v) {
            // dump(($v->employee_id));
            // dump(($v->weekoff_days));
            if (in_array($date, json_decode($v->weekoff_days) ?? []) && $v->employee_id == $employee_id) {
                $result['weekly_holiday'] = true;
                return $result;
            }
        }
        return $result;
    }


    public function getEmployeeLeaveRecord($from_date, $to_date, $employee_id, $wh, $ph)
    {
        $queryResult = LeaveApplication::select('application_from_date', 'application_to_date', 'number_of_day', 'employee_id')
            ->where('status', LeaveStatus::$APPROVE)
            ->where('employee_id', $employee_id)
            ->get();

        $leaveRecord = [];

        foreach ($queryResult as $key => $value) {
            $start_date = $value->application_from_date;
            $end_date = $value->application_to_date;
            while (strtotime($start_date) <= strtotime($end_date)) {
                $leaveRecord['date'][] = $start_date;
                $leaveRecord['number_of_day'][$start_date] = $value->number_of_day < 1 ? $value->number_of_day : 1.0;
                $start_date = date("Y-m-d", strtotime("+1 day", strtotime($start_date)));
            }
        }

        // $leaveRecord['leave'] = array_diff($leaveRecord['date'] ?? [], array_merge($ph, $wh));
        $leaveRecord['leave'] = $leaveRecord['date'] ?? [];
        return $leaveRecord;
    }

    public function getEmployeeWeeklyHolidayRecord($from_date, $to_date, $employee_id)
    {
        $queryResult = WeeklyHoliday::select('weekoff_days')
            ->where('employee_id', $employee_id)
            ->whereBetween('month', [date('Y-m', strtotime($from_date)), date('Y-m', strtotime($to_date))])
            ->get();

        $holidayRecord = [];

        foreach ($queryResult as $key => $value) {
            foreach (\json_decode($value['weekoff_days']) ?? [] as $value) {
                $holidayRecord[] = $value;
            }
        }

        return $holidayRecord;
    }

    public function getEmployeePublicHolidayRecord($from_date, $to_date, $employee_id)
    {
        $holidays = DB::select(DB::raw('call SP_getHoliday("' . $from_date . '","' . $to_date . '")'));

        $public_holidays = [];
        foreach ($holidays as $holidays) {
            $start_date = $holidays->from_date;
            $end_date = $holidays->to_date;
            while (strtotime($start_date) <= strtotime($end_date)) {
                $public_holidays[] = $start_date;
                $start_date = date("Y-m-d", strtotime("+1 day", strtotime($start_date)));
            }
        }

        return $public_holidays;
    }

    public function getPublicHolidays($fromDate, $toDate, $branchId)
    {
        // dd([$fromDate,$toDate]);
        $public_holidays = [];
        $queryResult = HolidayDetails::select('from_date', 'to_date')
            ->where('from_date', '>=', $fromDate)
            ->where('to_date', '<=', $toDate)
            ->get();
        // dd($queryResult);
        foreach ($queryResult as $holidays) {
            $start_date = $holidays->from_date;
            $end_date = $holidays->to_date;
            while (strtotime($start_date) <= strtotime($end_date)) {
                $public_holidays[] = $start_date;
                $start_date = date("Y-m-d", strtotime("+1 day", strtotime($start_date)));
            }
        }
        return $public_holidays;
    }

    public function getEmployeePermissionRecord($date, $employee_id)
    {
        $queryResult = LeavePermission::select('permission_duration', 'leave_permission_date', 'employee_id')
            ->where('employee_id', $employee_id)
            ->where('leave_permission_date',  $date)
            ->first();

        return $queryResult->permission_duration ?? null;
    }
}
