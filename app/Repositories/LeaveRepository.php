<?php

namespace App\Repositories;

use App\Lib\Enumerations\LeaveStatus;
use Carbon\Carbon;
use App\Model\Employee;
use App\Model\LeaveType;
use App\Model\EarnLeaveRule;
use App\Model\PaidLeaveRule;
use App\Model\EmpLeaveBalance;
use App\Model\LeaveApplication;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class LeaveRepository
{

    public function calculateTotalNumberOfLeaveDaysIncludingHolidays($application_from_date, $application_to_date, $employee_id)
    {
        $target = strtotime($application_from_date);
        $date_range = $holiday_dates = $public_holidays = $leave_days = $weeklyHolidayArray = [];

        while ($target <= strtotime(date("Y-m-d", strtotime($application_to_date)))) {
            $value = date("Y-m-d", $target);
            $target += (60 * 60 * 24);
            $date_range[] = $value;
        }

        $countDay = count($date_range);

        $data = [
            'public_holidays' => $public_holidays,
            'weekly_holidays' => $weeklyHolidayArray,
            'date_range' => $date_range,
            'holiday_dates' => $holiday_dates,
            'countDay' => $countDay,
            'leave_days' => $leave_days,
        ];
        return $data;
    }

    public function calculateTotalNumberOfLeaveDaysAnnualLeave($application_from_date, $application_to_date, $employee_id)
    {
        $target = strtotime($application_from_date);

        $countDay = 0;
        $date_range = $holiday_dates = $public_holidays = $leave_days = $weeklyHolidayArray = [];

        while ($target <= strtotime(date("Y-m-d", strtotime($application_to_date)))) {
            $value = date("Y-m-d", $target);
            $target += (60 * 60 * 24);

            //get weekly  holiday name
            $timestamp = strtotime($value);
            $dayName = date("l", $timestamp);
            $date_range[] = $value;
        }

        $countDay = count($date_range);

        $data = [
            'public_holidays' => $public_holidays,
            'weekly_holidays' => $weeklyHolidayArray,
            'date_range' => $date_range,
            'holiday_dates' => $holiday_dates,
            'countDay' => $countDay,
            'leave_days' => $leave_days,
        ];

        return $data;
    }

    public function calculateTotalNumberOfLeaveDays($application_from_date, $application_to_date, $employee_id)
    {

        $holidays = DB::select(DB::raw('call SP_getHoliday("' . date('Y-m-d', strtotime('- 30 days', strtotime($application_from_date))) . '","' . date('Y-m-d', strtotime('+ 30 days', strtotime($application_to_date))) . '")'));
        $public_holidays = [];

        foreach ($holidays as $holidays) {
            $start_date = $holidays->from_date;
            $end_date = $holidays->to_date;
            while (strtotime($start_date) <= strtotime($end_date)) {
                $public_holidays[] = $start_date;
                $start_date = date("Y-m-d", strtotime("+1 day", strtotime($start_date)));
            }
        }

        $weeklyHolidays = DB::select(DB::raw('call SP_getWeeklyHoliday("' . $employee_id . '","' . date('Y-m', strtotime($application_from_date)) . '")'));
        $weeklyHolidayArray = [];

        foreach ($weeklyHolidays as $weeklyHoliday) {
            $dateArray = json_decode($weeklyHoliday->weekoff_days, true);
            foreach ($dateArray as $key => $value) {
                $weeklyHolidayArray[] = $value;
            }
        }

        $leaveApplications = LeaveApplication::where('employee_id', $employee_id)
            // ->where('status', LeaveStatus::$APPROVE)
            ->get();

        $leave_days = [];
        foreach ($leaveApplications as $leaveApplication) {


            $start_date = $leaveApplication->application_from_date;
            $end_date = $leaveApplication->application_to_date;
            while (strtotime($start_date) <= strtotime($end_date)) {
                $leave_days[] = $start_date;
                $start_date = date("Y-m-d", strtotime("+1 day", strtotime($start_date)));
            }
        }

        $target = strtotime($application_from_date);

        $countDay = 0;
        $date_range = [];
        $holiday_dates = [];

        while ($target <= strtotime(date("Y-m-d", strtotime($application_to_date)))) {

            $value = date("Y-m-d", $target);
            $target += (60 * 60 * 24);

            //get weekly  holiday name
            $timestamp = strtotime($value);
            $dayName = date("l", $timestamp);

            $date_range[] = $value;

            //if not in holidays and not in weekly  holidays
            if (!in_array($value, $public_holidays) && !in_array($value, $weeklyHolidayArray)) {
            } elseif (in_array($value, $public_holidays) || in_array($value, $weeklyHolidayArray)) {
                $holiday_dates[] = $value;
            }
        }

        if (count($holiday_dates) <= 0) {
            $countDay = count($date_range);
        } elseif (count($holiday_dates) > 0) {
            // $holiday_count = 0;
            // foreach ($holiday_dates as $h_date) {
            //     $previous_date = date("Y-m-d", strtotime("+1 day", strtotime($h_date)));
            //     $next_date = date("Y-m-d", strtotime("+1 day", strtotime($h_date)));

            //     if ((in_array($previous_date, $leave_days) || in_array($previous_date, $date_range)) && (in_array($next_date, $leave_days) || in_array($next_date, $date_range))) {
            //         $holiday_count += 1;
            //     }
            // }
            $countDay = (count($date_range) - count($holiday_dates));
        }

        $data = [
            'public_holidays' => $public_holidays,
            'weekly_holidays' => $weeklyHolidayArray,
            'date_range' => $date_range,
            'holiday_dates' => $holiday_dates,
            'countDay' => $countDay,
            'leave_days' => $leave_days,
        ];

        return $data;
    }

    public function calculateTotalNumberOfLeaveDays2($application_from_date, $application_to_date)
    {
        $holidays =  DB::table('holiday_details')->get();
        $leave_count = 0;
        $govt_holidays = [];
        foreach ($holidays as $holiday) {
            $start_date = $holiday->from_date;
            $end_date = $holiday->to_date;
            while (strtotime($start_date) <= strtotime($end_date)) {
                $govt_holidays[] = $start_date;
                $start_date = date("Y-m-d", strtotime("+1 day", strtotime($start_date)));
            }
        }

        $leave_days = [];
        $format = 'Y-m-d';
        $current = strtotime($application_from_date);
        $date2 = strtotime($application_to_date);
        $stepVal = '+1 day';
        while ($current <= $date2) {
            if (!in_array(date('Y-m-d', $current), $govt_holidays)) {
                $dt1 = strtotime(date('Y-m-d', $current));
                $dt2 = date("l", $dt1);
                $dt3 = strtolower($dt2);
                if ($dt3 != "sunday") {
                    $leave_days[] = date($format, $current);
                }
            }
            $current = strtotime($stepVal, $current);
        }
        // info('application_from_date='. $application_from_date.', application_to_date='.$application_to_date);
        // info('count($govt_holidays)='. count($govt_holidays).', count($leave_days)='.count($leave_days));
        $leave_count = count($leave_days);
        return $leave_count;
    }
    public function nationality()
    {
        $results = ['Omanis', 'Expats', 'Both'];
        $options = ['' => '---- Please select ----'];
        foreach ($results as $key => $value) {
            $options[$key] = $value;
        }
        return $options;
    }
    public function religion()
    {
        $results = ['Muslim', 'Non-Muslim', 'Both'];
        $options = ['' => '---- Please select ----'];
        foreach ($results as $key => $value) {
            $options[$key] = $value;
        }
        return $options;
    }
    public function gender()
    {
        $results = ['Male', 'Female', 'Both'];
        $options = ['' => '---- Please select ----'];
        foreach ($results as $key => $value) {
            $options[$key] = $value;
        }
        return $options;
    }
    public function calculateTotalNumberOfLeaveDays1($application_from_date, $application_to_date, $employee_id)
    {

        $holidays = DB::select(DB::raw('call SP_getHoliday("' . $application_from_date . '","' . $application_to_date . '")'));
        $public_holidays = [];

        foreach ($holidays as $holidays) {
            $start_date = $holidays->from_date;
            $end_date = $holidays->to_date;
            while (strtotime($start_date) <= strtotime($end_date)) {
                $public_holidays[] = $start_date;
                $start_date = date("Y-m-d", strtotime("+1 day", strtotime($start_date)));
            }
        }

        $weeklyHolidays = DB::select(DB::raw('call SP_getWeeklyHoliday("' . $employee_id . '","' . date('Y-m', strtotime($application_from_date)) . '")'));
        $weeklyHolidayArray = [];

        foreach ($weeklyHolidays as $weeklyHoliday) {
            $dateArray = json_decode($weeklyHoliday->weekoff_days, true);
            foreach ($dateArray as $key => $value) {
                $weeklyHolidayArray[] = $value;
            }
        }

        $target = strtotime($application_from_date);

        $countDay = 0;
        $date_range = [];

        while ($target <= strtotime(date("Y-m-d", strtotime($application_to_date)))) {

            $value = date("Y-m-d", $target);
            $target += (60 * 60 * 24);

            //get weekly  holiday name
            $timestamp = strtotime($value);
            $dayName = date("l", $timestamp);

            $date_range[] = $value;

            //if not in holidays and not in weekly  holidays
            if (!in_array($value, $public_holidays) && !in_array($value, $weeklyHolidayArray)) {
                $countDay++;
            }
        }

        $data = [
            'public_holidays' => $public_holidays,
            'weekly_holidays' => $weeklyHolidayArray,
            'date_range' => $date_range,
            'countDay' => $countDay,
        ];

        return $data;
    }

    public function calculateEmployeeLeaveBalanceArray($leave_type_id, $employee_id)
    {

        $leaveTypes = LeaveType::where('status', 1)->get();
        $leaveArr = [];
        foreach ($leaveTypes as $key => $ltype) {

            $leaveArr[$key]['leaveType'] = $ltype->leave_type_name;
            $leaveBalance = DB::select(DB::raw('call SP_calculateEmployeeLeaveBalance(' . $employee_id . ',' . $ltype->leave_type_id . ')'));
            $leaveArr[$key]['totalDays'] = $ltype->num_of_day;
            $leaveArr[$key]['leaveTaken'] = $leaveBalance[0]->totalNumberOfDays;
            $leaveArr[$key]['leaveBalance'] = $ltype->num_of_day - $leaveBalance[0]->totalNumberOfDays;
        }

        return $leaveArr;
    }

    public function calculateEmployeeLeaveBalance($leave_type_id, $employee_id)
    {


        $leave = EmpLeaveBalance::where('employee_id', $employee_id)->where('leave_type_id', $leave_type_id)->first();
        $leavebalance = $leave->leave_balance;

        $leave_application = LeaveApplication::where('employee_id', $employee_id)->where('leave_type_id', $leave_type_id)->where('status', 2)->where('manager_status', 2)->sum('number_of_day');
        return response()->json([
            'status' => true,
            'leave_balance' => $leavebalance,
            'leave_taken' => $leave_application,
        ]);
    }
    public function calculateEmployeeLeaveBalance1($leave_type_id, $employee_id)
    {
        $leave = EmpLeaveBalance::where('employee_id', $employee_id)->where('leave_type_id', $leave_type_id)->first();
        $leavebalance = $leave->leave_balance;

        return $leavebalance;
    }

    public function getEmployeeTotalLeaveBalancePerYear($leave_type_id, $employee_id)
    {

        $paidleaveType = LeaveType::where('status', 1)->where('leave_type_id', 2)->sum('num_of_day');
        $paidLeaveRule = PaidLeaveRule::where('day_of_paid_leave')->first();
        $totalLeaveTaken = LeaveApplication::where('employee_id', $employee_id)->whereBetween('created_at', [Carbon::now()->subYear(), Carbon::now()])->where('status', 2)->pluck('number_of_day');
        $sumOfLeaveTaken = $totalLeaveTaken->sum();
        $checkLeaveEligibility = $sumOfLeaveTaken <= 15;
        $sumOfPaidLeaveTaken = $paidLeaveRule->day_of_paid_leave->sum();
        $results = $checkLeaveEligibility == true ? ($paidleaveType - $sumOfLeaveTaken) : 0;

        if ($employee_id != '' && $leave_type_id != '') {
            return $results;
        }
    }

    public function calculateEmployeeEarnLeave($leave_type_id, $employee_id, $action = false)
    {

        $employeeInfo = Employee::where('employee_id', $employee_id)->first();
        $joiningYearAndMonth = explode('-', $employeeInfo->date_of_joining);

        $joiningYear = $joiningYearAndMonth[0];
        $joiningMonth = (int) $joiningYearAndMonth[1];

        $currentYear = date("Y");
        $currentMonth = (int) date("m");

        $totalMonth = 0;

        if ($joiningYear == $currentYear) {
            for ($i = $joiningMonth; $i <= $currentMonth; $i++) {
                $totalMonth += 1;
            }
        } else {
            for ($i = 1; $i <= $currentMonth; $i++) {
                $totalMonth += 1;
            }
        }

        $ifExpenseLeave = LeaveApplication::select(DB::raw('IFNULL(SUM(leave_application.number_of_day), 0) as number_of_day'))
            ->where('employee_id', $employee_id)
            ->where('leave_type_id', $leave_type_id)
            ->where('status', 2)
            ->whereBetween('approve_date', [date("Y-01-01"), date("Y-12-31")])
            ->first();

        $earnLeaveRule = EarnLeaveRule::first();

        if ($action == 'getEarnLeaveBalanceAndExpenseBalance') {
            $totalNumberOfDays = $totalMonth * $earnLeaveRule->day_of_earn_leave;
            $data = [
                'totalEarnLeave' => round($totalMonth * $earnLeaveRule->day_of_earn_leave),
                'leaveConsume' => $ifExpenseLeave->number_of_day,
                'currentBalance' => round($totalNumberOfDays - $ifExpenseLeave->number_of_day),
            ];
            return $data;
        }

        $totalNumberOfDays = $totalMonth * $earnLeaveRule->day_of_earn_leave;
        return round($totalNumberOfDays - $ifExpenseLeave->number_of_day);
    }

    public function calculateEmployeePaidLeave($leave_type_id, $employee_id, $action = false)
    {

        $employeeInfo = Employee::where('employee_id', $employee_id)->first();
        $joiningYearAndMonth = explode('-', $employeeInfo->date_of_joining);

        $joiningYear = $joiningYearAndMonth[0];
        $joiningMonth = (int) $joiningYearAndMonth[1];

        $currentYear = date("Y");
        $currentYearInt = (int) date("Y");
        $currentMonth = (int) date("m");

        $totalMonth = 0;
        $totalYear = 0;

        if ($joiningYear == $currentYear) {
            for ($i = $joiningMonth; $i <= $currentMonth; $i++) {
                $totalMonth += 1;
            }
        } else {
            for ($i = 1; $i <= $currentMonth; $i++) {
                $totalMonth += 1;
            }
            for ($y = 1; $y <= $currentYearInt; $y++) {
                $totalYear += 1;
            }
        }

        $ifExpenseLeave = LeaveApplication::select(DB::raw('IFNULL(SUM(leave_application.number_of_day), 0) as number_of_day'))
            ->where('employee_id', $employee_id)
            ->where('leave_type_id', $leave_type_id)
            ->where('status', 2)
            ->whereBetween('approve_date', [date("Y-01-01"), date("Y-12-31")])
            ->first();
        $totalLeavePerYear = LeaveApplication::where('employee_id', $employee_id)->whereBetween('created_at', [Carbon::now()->subYear(), Carbon::now()])->where('status', 2)->sum('number_of_day');
        $expectedPaidleave = LeaveType::where('status', 1)->where('leave_type_id', 2)->sum('num_of_day');
        $paidLeaveRule = PaidLeaveRule::first();

        // $totalYear         = $totalMonth >= 12 ? $totalMonth / 12 : $totalMonth;
        // $seperateTotalYear = explode('.', $totalMonth / 12);
        // $getTotalYear = $seperateTotalYear[0];
        // $getTotalMonth = $seperateTotalYear[1];

        if ($action == 'getEarnLeaveBalanceAndExpenseBalance') {
            $totalNumberOfDays = 1 * $paidLeaveRule->day_of_paid_leave;
            $data = [
                'totalPaidLeave' => round(1 * $paidLeaveRule->day_of_paid_leave),
                'leaveConsume' => $ifExpenseLeave->number_of_day,
                'currentBalance' => round($totalNumberOfDays - $ifExpenseLeave->number_of_day),
            ];
            return $data;
        }

        $totalNumberOfDays = 1 * $paidLeaveRule->day_of_paid_leave;
        // $leaveBalance      = round($totalNumberOfDays - $ifExpenseLeave->number_of_day);
        $results = ($totalLeavePerYear <= $paidLeaveRule->day_of_paid_leave ? $totalNumberOfDays : 0);
        return $results;
    }
}
