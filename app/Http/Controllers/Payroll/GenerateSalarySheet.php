<?php

namespace App\Http\Controllers\Payroll;

use App\Model\Otp;
use Carbon\Carbon;
use NumberFormatter;
use Razorpay\Api\Api;
use App\Model\Employee;
use App\Components\Common;
use App\Model\SalaryDetails;
use Illuminate\Http\Request;
use App\Model\SocialSecurity;
use App\Model\ApproveOverTime;
use App\Model\AdvanceDeduction;
use App\Model\LeaveApplication;
use App\Model\PrintHeadSetting;
use App\Model\EmployeeIncrement;
use App\Model\AdvanceDeductionLog;
use Illuminate\Support\Facades\DB;
use App\Exports\SalaryDetailExport;
use App\Imports\SalaryDetailImport;
use App\Model\SalaryDetailsToLeave;
use App\Http\Controllers\Controller;
use App\Http\Requests\FileUploadRequest;
use App\Lib\Enumerations\UserStatus;
use App\Model\CompanyAddressSetting;
use Illuminate\Support\Facades\Auth;
use Maatwebsite\Excel\Facades\Excel;
use App\Lib\Enumerations\AppConstant;
use App\Lib\Enumerations\LeaveStatus;
use Illuminate\Support\Facades\Input;
use App\Repositories\CommonRepository;
use App\Model\SalaryDetailsToAllowance;
use App\Model\SalaryDetailsToDeduction;
use App\Repositories\PayrollRepository;
use Illuminate\Support\Facades\Session;
use App\Model\AdvanceDeductionTransaction;
use App\Repositories\AttendanceRepository;

class GenerateSalarySheet extends Controller
{

    protected $commonRepository;
    protected $payrollRepository;
    protected $attendanceRepository;

    public function __construct(AttendanceRepository $attendanceRepository, CommonRepository $commonRepository, PayrollRepository $payrollRepository)
    {
        $this->commonRepository = $commonRepository;
        $this->payrollRepository = $payrollRepository;
        $this->attendanceRepository = $attendanceRepository;
    }
    public function getEmployeeEmail(Request $request)
    {
        $salaryDetails = SalaryDetails::where('salary_details_id', $request->id)->firstOrFail();

        $employee = Employee::where('employee_id', $salaryDetails->employee_id)->first();
        if ($employee && $employee->email) {

            $otp = rand(100000, 999999);

            // $smsCheck = Otp::where('user_mobile', $request->username)->where('mobile_otp', $request->otp)
            // ->where('created_at', '>=', Carbon::now()->subMinutes(5)->toDateTimeString())->orderByDesc('id')->first();

            $create = Otp::create([
                'employee_id' => $request->id,
                'otp' => $otp,
                'email' => $employee->email,
                'created_at' => Carbon::now(),
            ]);

            $maildata = Common::mail('emails/mail', $employee->email, 'Payslip View OTP', ['head_name' => $employee->first_name . ' ' . $employee->last_name, 'request_info' => 'Your OTP For Payslip Download is : ' . $otp, 'status_info' => '']);
            return response()->json([
                'status' => true,
                'mail' => $employee->email,
            ]);
        } else {
            return response()->json([
                'status' => false,
                'mail' => true,
            ]);
        }
    }

    public function verifyOtp(Request $request)
    {
        $smsCheck = Otp::where('email', $request->mail)->where('otp', $request->otp)
            ->where('created_at', '>=', Carbon::now()->subMinutes(5)->toDateTimeString())->orderByDesc('otp_id')->first();
        if ($smsCheck) {
            return response()->json([
                'status' => true,
                'id' => $request->id,
            ]);
        } else {
            return response()->json([
                'status' => false,
                'message' => "Entered Otp is wrong!",
            ]);
        }
    }
    public function index(Request $request)
    {
        $results = [];

        if ($request->month != '') {
            $results = SalaryDetails::with(['employee' => function ($query) {
                $query->with(['department', 'payGrade', 'hourlySalaries']);
            }])->where('month_of_salary', $request->month)->orderBy('salary_details_id', 'DESC')->get();
        }

        $departmentList = $this->commonRepository->departmentList();

        return view('admin.payroll.salarySheet.salaryDetails', ['results' => $results, 'month' => $request->month ?? date('Y-m', strtotime('- 1 month')), 'departmentList' => $departmentList]);
    }
    public function downloadSalarySheetPdf(Request $request)
    {
        $results = SalaryDetails::with(['employee' => function ($query) {
            $query->with(['department', 'payGrade', 'hourlySalaries']);
        }])->where('month_of_salary', $request->monthField)->orderBy('salary_details_id', 'DESC')->get();

        $departmentList = $this->commonRepository->departmentList();

        $data = ['results' => $results, 'month' => $request->monthField ?? date('Y-m', strtotime('- 1 month')), 'departmentList' => $departmentList];

        $pdf = \PDF::loadView('admin.payroll.salarySheet.pdf.salarySheetpdf', $data);
        $pdf->setPaper('A3', 'landscape');
        $pageName = "salary-sheet-" . dateConvertFormToDB($request->monthField) . ".pdf";
        return $pdf->download($pageName);
    }

    public function monthSalary(Request $request)
    {
        $results = SalaryDetails::with(['employee' => function ($query) {
            $query->with('payGrade');
        }])->where('month_of_salary', $request->month)->get();

        return view('admin.payroll.salarySheet.salaryDetails', ['results' => $results]);
    }

    public function create()
    {
        $employeeList = Employee::where('status', UserStatus::$ACTIVE)->select('first_name', 'last_name', 'finger_id', 'employee_id')->get();
        return view('admin.payroll.salarySheet.generateSalarySheet', ['employeeList' => $employeeList, 'socialSecurity' => null, 'employee_id' => request()->employee_id]);
    }

    public function calculateEmployeeSalary(Request $request)
    {
        if ($request->month > date('Y-m') || $request->month < date('Y-m', strtotime('-2 months'))) {
            return redirect('generateSalarySheet/create')->with('error', 'Backdated / future month cannot be allowed.');
        }

        $employeeDetails = Employee::with('department', 'designation')->where('employee_id', $request->employee_id)->firstOrFail();
        $salaryDetails = SalaryDetails::where('employee_id', $request->employee_id)->where('month_of_salary', $request->month)->first();
        $employeeList = Employee::where('status', UserStatus::$ACTIVE)->select('first_name', 'last_name', 'finger_id', 'employee_id')->get();

        $allowance = [];
        $deduction = [];

        $from_date = $request->month . "-01";
        $to_date = date('Y-m-t', strtotime($from_date));

        // $leaveRecord = LeaveApplication::select('leave_type.leave_type_id', 'leave_type_name', 'number_of_day', 'application_from_date', 'application_to_date')
        //     ->join('leave_type', 'leave_type.leave_type_id', 'leave_application.leave_type_id')
        //     ->where('leave_application.status', LeaveStatus::$APPROVE)
        //     ->where('application_from_date', '>=', $from_date)
        //     ->where('application_to_date', '<=', $to_date)
        //     ->where('employee_id', $request->employee_id)
        //     ->get();

        $monthAndYear = explode('-', $request->month);

        $attendanceInfo = $this->getEmployeeOtmAbsLvLtAndWokDays($request->month, $employeeDetails);

        $allowance = $this->calculateEmployeeAllowance($employeeDetails, $attendanceInfo, $request->month);

        $socialSecurity = $this->calculateSocialSecurity($employeeDetails, $allowance, $request->month);

        $deduction = $this->calculateEmployeeDeduction($employeeDetails, $attendanceInfo, $request->month);

        $deduction['social_security'] = $socialSecurity['social_security'];

        $basic =  $allowance['basic'];
        $increment =  $allowance['increment'];
        $increment_amount =  $allowance['increment_amount'];
        unset($allowance['basic']);
        unset($allowance['increment']);
        unset($allowance['increment_amount']);

        $approve_over_time_amount = ApproveOverTime::whereBetween('date', [$from_date, $to_date])->where('finger_print_id', $employeeDetails->finger_id)->sum('over_time_amount');


        $lop_days = $attendanceInfo['total_absence'];

        $data = [
            'employee_id' => $request->employee_id,
            'month' => $request->month,
            'employeeList' => $employeeList,
            'employeeDetails' => $employeeDetails,
            'allowances' => $allowance,
            'deductions' => $deduction,
            'attendanceInfo' => $attendanceInfo,
            'over_time_amount' => $approve_over_time_amount,
            // 'leaveRecords' => $leaveRecord,
            'salaryDetails' => $salaryDetails,
            'socialSecurity' => $socialSecurity['object'],
            'basic' => number_format($basic, 3, '.', ''),
            'increment' => number_format($increment, 3, '.', ''),
            'increment_amount' => number_format($increment_amount, 3, '.', ''),
            'lop_days' => $lop_days
        ];

        return view('admin.payroll.salarySheet.generateSalarySheet', $data);
    }

    public function uploadSalarySheet(FileUploadRequest $request)
    {
        try {
            $file = $request->file('select_file');
            Excel::import(new SalaryDetailImport($this), $file);
        } catch (\Maatwebsite\Excel\Validators\ValidationException $e) {
            $import = new SalaryDetailImport($this);
            $import->import($file);

            foreach ($import->failures() as $failure) {
                $failure->row(); // row that went wrong
                $failure->attribute(); // either heading key (if using heading row concern) or column index
                $failure->errors(); // Actual error messages from Laravel validator
                $failure->values(); // The values of the row that has failed.
            }
        }
        return back()->with('success', 'Employee information imported successfully.');
    }

    public function salarySheetTemplate(Request $request)
    {
        $employees = Employee::where('status', UserStatus::$ACTIVE)->where('employee_id', '!=', 1)->with('department', 'branch', 'designation')->get();
        $extraData = [];
        $inc = 1;

        $salaryDetails = SalaryDetails::where('month_of_salary', $request->month)->get();


        foreach ($employees as $key => $data) {

            $attendanceInfo = $this->getEmployeeOtmAbsLvLtAndWokDays($request->month, $data);
            $allowance = $this->calculateEmployeeAllowance($data, $attendanceInfo, $request->month);
            unset($allowance['basic'], $allowance['increment'], $allowance['increment_amount']);
            $grossSalary = array_sum($allowance);

            $lopDeduction = ($grossSalary / 30) * $attendanceInfo['total_absence'];

            $mySalaryDetails = $salaryDetails->filter(function ($q) use ($data) {
                return $q->employee_id == $data->employee_id;
            })->values()->first();

            $employeeInfo = [
                $inc,
                $request->month,
                $data->finger_id,
                $data->fullname(),
                $data->department->department_name ?? '',
                $data->designation->designation_name ?? '',
                $data->date_of_joining,
                employee_nationality($data->nationality),
                $data->branch->branch_name ?? '',
                $mySalaryDetails->arrears_adjustment ?? 0,
                $mySalaryDetails->pay_cut ?? 0,
                $mySalaryDetails->gsm ?? 0,
                $mySalaryDetails->lop ?? number_format((float)$lopDeduction, 2, '.', ''),
            ];

            $dataset[] = $employeeInfo;
            $inc++;
        }

        $heading = [

            [
                'Sl.No',
                'Month',
                'Employee Id',
                'Name',
                'Department',
                'Designation',
                'Date of Joining',
                'Nationality',
                'Location',
                // 'Working Days',
                // 'Holiday',
                // 'WeekOff',
                // 'Present',
                // 'Absent',
                // 'Leave',
                // 'Extra Hours',
                // 'Extra Income',
                'Arrears or Adjustment',
                'Pay Cut',
                'Gsm',
                'Loss of Pay',
            ],
        ];

        $extraData['heading'] = $heading;

        $filename = 'SalarySheet-' . DATE('dmYHis') . '.xlsx';

        return Excel::download(new SalaryDetailExport($dataset, $extraData), $filename);
    }

    public function importEmployeeSalary(Request $request)
    {
        if ($request->month > date('Y-m')) {
            return redirect('generateSalarySheet/create')->with('error', 'Month cannot be in the future');
        }
    }

    public function calculateEmployeeAllowance($data, $attendanceInfo, $month, $importData = [])
    {
        $tempArr = [];
        $increment = false;
        $this_month = Carbon::parse($month);
        $year = date('Y', strtotime($month));

        $incrementObject = EmployeeIncrement::where('employee_id', $data->employee_id)->where('year', $year)->first();

        $basic_amount = $incrementObject ? ($this_month->month == 1 ? $incrementObject->basic_amount : ($incrementObject->basic_amount + $incrementObject->increment_amount)) : 0;

        $default_basic_salary = $incrementObject ?  $basic_amount : ($data->basic_salary ?? 0);

        $increment = (($data->increment ?? 0) / 100) * $default_basic_salary;
        $increment_amount = 0;

        $sickLeave = LeaveApplication::where('employee_id', $data->employee_id)
            ->where('leave_type_id', AppConstant::$SICK_LEAVE_ID)
            ->whereYear('application_from_date', date('Y', strtotime($month)))
            // ->whereYear('application_to_date', date('Y', strtotime($month)))
            ->where('status', LeaveStatus::$APPROVE)
            ->where('manager_status', LeaveStatus::$APPROVE)
            ->sum('number_of_day');

        // dd($sickLeave);

        if ($sickLeave >= LeaveStatus::$SICK_LEAVE_D7 && $sickLeave <= LeaveStatus::$SICK_LEAVE_D8) {
            $basic_salary = $default_basic_salary * LeaveStatus::$SICK_LEAVE_D;
        } elseif ($sickLeave >= LeaveStatus::$SICK_LEAVE_D5 && $sickLeave <= LeaveStatus::$SICK_LEAVE_D6) {
            $basic_salary = $default_basic_salary * LeaveStatus::$SICK_LEAVE_C;
        } elseif ($sickLeave >= LeaveStatus::$SICK_LEAVE_D3 && $sickLeave <= LeaveStatus::$SICK_LEAVE_D4) {
            $basic_salary = $default_basic_salary * LeaveStatus::$SICK_LEAVE_B;
        } else {
            $basic_salary = $default_basic_salary * LeaveStatus::$SICK_LEAVE_A;
        }

        // dd($sickLeave);

        // check omanis & month is jan // && $data->nationality == AppConstant::$OMANIS // Employee Increment
        if ($this_month->month == AppConstant::$INCREMENT_MONTH) {
            $start_month = Carbon::parse($data->date_of_joining);
            $month_diff = $start_month->diffInMonths($this_month);
            $increment = $month_diff > AppConstant::$PROBATION_LIMIT;

            // if ($increment) {
            // new Employee Increment Model Create/Update (Need to check)
            $increment_amount = $increment ? round((($data->increment ?? 0) * $default_basic_salary) / 100, 3) : 0;

            if ($incrementObject) {
                $incrementObject->update([
                    'employee_id' => $data->employee_id,
                    'year' => $year,
                    'basic_salary' => $basic_salary + $increment_amount,
                    'increment_percentage' => $data->increment ?? 0,
                    'increment_amount' => $increment_amount,
                ]);
            } else {
                EmployeeIncrement::create([
                    'employee_id' => $data->employee_id,
                    'year' => $year,
                    'basic_salary' => $basic_salary + $increment_amount,
                    'basic_amount' => $default_basic_salary,
                    'increment_percentage' => $data->increment ?? 0,
                    'increment_amount' => $increment_amount,
                ]);
            }
            // }
        }

        $tempArr['basic'] = $default_basic_salary;
        $tempArr['increment'] =  $data->increment ?? 0;
        $tempArr['increment_amount'] =  $increment_amount;

        $tempArr['basic_salary'] = $basic_salary + $increment_amount;
        $tempArr['housing_allowance'] = $data->housing_allowance ?? 0;
        $tempArr['utility_allowance'] = $data->utility_allowance ?? 0;
        $tempArr['transport_allowance'] = $data->transport_allowance ?? 0;
        $tempArr['living_allowance'] = $data->living_allowance ?? 0;
        $tempArr['mobile_allowance'] = $data->mobile_allowance ?? 0;
        $tempArr['special_allowance'] = $data->special_allowance ?? 0;
        $tempArr['membership_allowance'] = $data->membership_allowance ?? 0;
        $tempArr['education_and_club_allowance'] = $data->education_and_club_allowance ?? 0;

        foreach ($importData as $key => $value) {
            $tempArr[$key] = $value ?? 0;
        }
        return $tempArr;
    }

    public function calculateEmployeeDeduction($data, $attendanceInfo, $month, $importData = [])
    {
        $tempArr = [];
        $tempArr['prem_others'] = $data->prem_others ?? 0;
        $advanceDeduction = $this->calculateEmployeeAdvanceDeduction($data->employee_id, $month);
        $tempArr['salary_advance'] = $advanceDeduction['deduction_amouth_per_month'];

        foreach ($importData as $key => $value) {
            $tempArr[$key] = $value ?? 0;
        }

        return $tempArr;
    }

    public function calculateSocialSecurity($data, $allowances, $month)
    {
        $tempArr = [];
        unset($allowances['basic']);
        // unset($allowances['increment']);
        // unset($allowances['increment_amount']);
        $salaryDetails = SalaryDetails::with('employee')->where('employee_id', $data->employee_id)->where('month_of_salary', $month)->first();
        $socialSecurity = SocialSecurity::where('year', date('Y', strtotime($month)))->where('nationality', $data->nationality)->first();

        if ($socialSecurity && $data->basic_salary <= $socialSecurity->gross_salary) {
            if ($salaryDetails && $salaryDetails->arrears_adjustment > 0) {
                $tempArr['social_security'] = $socialSecurity ? ((array_sum($allowances) + $salaryDetails->arrears_adjustment) / 100) * $socialSecurity->percentage : 0;
                $tempArr['employer_contribution'] = $socialSecurity ? ((array_sum($allowances) + $salaryDetails->arrears_adjustment) / 100) * $socialSecurity->employer_contribution : 0;
            } else {
                $tempArr['social_security'] = $socialSecurity ? (array_sum($allowances) / 100) * $socialSecurity->percentage : 0;
                $tempArr['employer_contribution'] = $socialSecurity ? (array_sum($allowances)  / 100) * $socialSecurity->employer_contribution : 0;
            }
        } else {
            $tempArr['social_security'] = 0.000;
        }

        $tempArr['object'] = $socialSecurity;

        return $tempArr;
    }

    public function calculateEmployeeAdvanceDeduction($employee_id, $month)
    {

        $advanceDeductions = AdvanceDeduction::where('advance_deduction.employee_id', $employee_id)->where('status', 0)->where('payment_type', 0)->orderByDesc('advance_deduction_id')->get();

        $tempArr = [];
        $amount = 0;
        $tempArr['deduction_amouth_per_month'] = 0;
        // if advance deduction there deduct amount
        foreach ($advanceDeductions as $advanceDeduction) {
            if ($advanceDeduction && $month >= date('Y-m', strtotime($advanceDeduction->date_of_advance_given))) {
                $expiry = date('Y-m-d', strtotime('+1 months', strtotime($advanceDeduction->date_of_advance_given)));

                // if advance deduction completed skip deduction and update status as no due else deduct amount
                if ($month > date('Y-m', strtotime($expiry))) {

                    $advanceDeduction->status = 2;
                    $advanceDeduction->save();
                    $tempArr['deduction_amouth_per_month'] = $amount;
                } else {
                    $advanceDeduction->status = 0;
                    // $advanceDeduction->remaining_month = $advanceDeduction->no_of_month_to_be_deducted - 1;
                    $advanceDeduction->save();
                    $amount += $advanceDeduction->deduction_amouth_per_month;
                    $tempArr['deduction_amouth_per_month'] = $amount;
                }
            } else {
                $tempArr['deduction_amouth_per_month'] = $amount;
            }
        }
        // dd($tempArr);
        return $tempArr;
    }

    public function getEmployeeOtmAbsLvLtAndWokDays($month, $employeeDetails)
    {

        $getDate = $this->getMonthToStartDateAndEndDate($month);
        $queryResult = $this->attendanceRepository->getEmployeeMonthlyAttendance($getDate['firstDate'], $getDate['lastDate'], $employeeDetails->employee_id);
        // dd($queryResult);
        $totalPresent = 0;
        $totalAbsence = 0;
        $totalLeave = 0;
        $totalPublicHoliday = 0;
        $totalWeeklyHoliday = 0;
        $totalExtraHoursArr = [];
        $totalWorkingDays = date('t', strtotime($month));

        $sickLeave = LeaveApplication::where('employee_id', $employeeDetails->employee_id)
            ->where('leave_type_id', AppConstant::$SICK_LEAVE_ID)
            ->whereYear('application_from_date', date('Y', strtotime($month)))
            // ->whereYear('application_to_date', date('Y', strtotime($month)))
            ->where('status', LeaveStatus::$APPROVE)
            ->where('manager_status', LeaveStatus::$APPROVE)
            ->sum('number_of_day');

        // attendance information for payroll statement
        foreach ($queryResult as $value) {

            if ($value['approved_over_time'] && $value['approved_over_time'] != '') {
                $totalExtraHoursArr[] = $value['approved_over_time'];
            }

            if ($value['action'] == 'WeeklyHoliday') {
                $totalWeeklyHoliday += 1;
            } elseif ($value['action'] == 'PublicHoliday') {
                $totalPublicHoliday += 1;
            } elseif ($value['action'] == 'Absence') {
                $totalAbsence += 1;
            } elseif ($value['action'] == 'FullDayLeave') {
                $totalLeave += 1;
            } elseif ($value['action'] == 'HalfDayLeave') {
                $totalLeave += 0.5;
            } else {
                $totalPresent += 1;
            }
        }

        $data = [
            'total_working_days' => $totalWorkingDays,
            'public_holiday' => $totalPublicHoliday,
            'weekly_holiday' => $totalWeeklyHoliday,
            'total_present' => $totalPresent,
            'total_absence' => $totalAbsence,
            'total_leave' => $totalLeave,
            'sick_leave' => $sickLeave,
            'extra_hours' => number_format(decimalHours(sumTimeArr($totalExtraHoursArr)), 2, '.', ''),
        ];

        return $data;
    }

    public function getMonthToStartDateAndEndDate($month)
    {
        $month = explode('-', $month);
        $current_year = $month[0];
        $lastMonth = $month[1];

        $firstDate = $current_year . "-" . $lastMonth . "-01";
        $lastDateOfMonth = date('t', strtotime($firstDate));
        $lastDate = $current_year . "-" . $lastMonth . "-" . $lastDateOfMonth;

        return ['firstDate' => $firstDate, 'lastDate' => $lastDate];
    }

    public function store(Request $request)
    {
        $input = $request->all();
        unset($input['_token']);
        $employee = Employee::where('employee_id', $input['employee_id'])->first();
        $input['account_number'] = $employee->account_number ?? '-';
        $input['branch_id'] = $employee->branch_id ?? '-';
        $input['ifsc_number'] = $employee->ifsc_number ?? '-';
        $input['name_of_the_bank'] = $employee->name_of_the_bank ?? '-';
        $input['account_holder'] = $employee->account_holder ?? '-';
        $input['gross_salary'] = $input['total_allowances'];
        $input['created_by'] = Auth::user()->user_id;
        $input['updated_by'] = Auth::user()->user_id;
        // dd($input);

        try {
            DB::beginTransaction();

            // update basic into employee table if increment is avaliable
            $increment = EmployeeIncrement::where('employee_id', $input['employee_id'])->where('year', date('Y', strtotime($input['month_of_salary'])))->first();
            if ($increment) {
                Employee::find($input['employee_id'])->update(['basic_salary' => $increment->basic_amount + $increment->increment_amount]);
            }
            $advanceDeductions = AdvanceDeduction::where('advance_deduction.employee_id',  $input['employee_id'])->where('status', 0)->where('payment_type', 0)->orderByDesc('advance_deduction_id')->get();

            $parentData = SalaryDetails::where('month_of_salary', $input['month_of_salary'])->where('employee_id', $input['employee_id'])->first();

            if (!$parentData) {
                foreach ($advanceDeductions as $advance) {
                    $log =  AdvanceDeductionLog::create([
                        'employee_id'                => $input['employee_id'],
                        'advance_deduction_id'       => $advance->advance_deduction_id,
                        'advance_amount'             => $advance->advance_amount,
                        'advancededuction_name'      => $advance->advancededuction_name,
                        'date_of_advance_given'      => dateConvertFormtoDB($advance->date_of_advance_given),
                        'deduction_amouth_per_month' => $advance->deduction_amouth_per_month,
                        'payment_type'               => $advance->payment_type,
                        'paid_amount'                => $advance->deduction_amouth_per_month,
                        'no_of_month_to_be_deducted' => $advance->no_of_month_to_be_deducted,
                        'remaining_month'            => $advance->no_of_month_to_be_deducted,
                        'reason'                     => $advance->deduction_amouth_per_month . " " . "Advance Deduction Amount Debited By Payroll",
                        'created_by'                 => Auth::user()->user_name,
                    ]);
                    AdvanceDeductionTransaction::create([
                        'advance_deduction_log_id'  => $log->advance_deduction_log_id,
                        'advance_deduction_id'      => $advance->advance_deduction_id,
                        'employee_id'               => $advance->employee_id,
                        'transaction_date'          => dateConvertFormtoDB(Carbon::today()),
                        'payment_type'              => 0,
                        'cash_received'             => $advance->deduction_amouth_per_month,
                        'created_by'                => Auth::user()->id,
                    ]);
                    $this->pendingAmountCalculation($advance->advance_deduction_id, 1);
                }
            }

            $parentData = SalaryDetails::updateOrCreate(['month_of_salary' => $input['month_of_salary'], 'employee_id' => $input['employee_id']], $input);

            DB::commit();
            $bug = 0;
        } catch (\Exception $e) {
            DB::rollback();
            dd($e);
            $bug = 1;
        }

        if ($bug == 0) {
            return back()->with('success', 'Salary Generate successfully.');
        } else {
            return back()->with('error', 'Something Error Found !, Please try again.');
        }
    }

    public function pendingAmountCalculation($id, $flag)
    {
        try {
            $deduction = AdvanceDeduction::where('advance_deduction_id', $id)->firstOrFail();
            $transaction = AdvanceDeductionTransaction::where('advance_deduction_id', $id)->sum('cash_received');
            // dd($transaction->all());
            $update = AdvanceDeduction::where('advance_deduction_id', $id)->update(['paid_amount' => $transaction, 'pending_amount' => ($deduction->advance_amount - $transaction)]);


            if ($deduction->advance_amount - $transaction <= 0) {
                $deduction = AdvanceDeduction::where('advance_deduction_id', $id)->update(['status' => 2]);
            }
            if ($flag == 3) {
                $deduction_month = AdvanceDeduction::where('advance_deduction_id', $id)->increment('remaining_month');
                info($deduction->status);
                if ($deduction->status == 2) {
                    $deduction->status = 0;
                    $deduction->update();
                }
            } elseif ($flag == 1) {
                $deduction = AdvanceDeduction::where('advance_deduction_id', $id)->decrement('remaining_month');
            }
            // 1 create, 2 update, 3 delete
        } catch (\Throwable $th) {
            info($th);
            return false;
        }
    }

    public function makeEmployeeSalaryDetailsToAllowanceDataFormat($data, $salary_details_id)
    {
        $allowanceData = [];
        if (isset($data['allowance_id'])) {
            for ($i = 0; $i < count($data['allowance_id']); $i++) {
                $allowanceData[$i] = [
                    'salary_details_id' => $salary_details_id,
                    'allowance_id' => $data['allowance_id'][$i],
                    'amount_of_allowance' => $data['amount_of_allowance'][$i],
                    'created_at' => Carbon::now(),
                    'updated_at' => Carbon::now(),
                ];
            }
        }
        return $allowanceData;
    }

    public function makeEmployeeSalaryDetailsToDeductionDataFormat($data, $salary_details_id)
    {
        $deductionData = [];
        if (isset($data['deduction_id'])) {
            for ($i = 0; $i < count($data['deduction_id']); $i++) {
                $deductionData[$i] = [
                    'salary_details_id' => $salary_details_id,
                    'deduction_id' => $data['deduction_id'][$i],
                    'amount_of_deduction' => $data['amount_of_deduction'][$i],
                    'created_at' => Carbon::now(),
                    'updated_at' => Carbon::now(),
                ];
            }
        }
        return $deductionData;
    }

    public function makeEmployeeSalaryDetailsToLeaveDataFormat($data, $salary_details_id)
    {
        $leaveData = [];
        if (isset($data['num_of_day'])) {
            for ($i = 0; $i < count($data['num_of_day']); $i++) {
                $leaveData[$i] = [
                    'salary_details_id' => $salary_details_id,
                    'num_of_day' => $data['num_of_day'][$i],
                    'leave_type_id' => $data['leave_type_id'][$i],
                    'created_at' => Carbon::now(),
                    'updated_at' => Carbon::now(),
                ];
            }
        }
        return $leaveData;
    }

    public function makePayment(Request $request)
    {
        $data['status'] = 1;
        $data['comment'] = $request->comment;
        $data['payment_method'] = $request->payment_method;
        $data['created_by'] = Auth::user()->user_id;
        $data['updated_by'] = Auth::user()->user_id;
        $data['created_at'] = Carbon::now();
        $data['updated_at'] = Carbon::now();
        try {
            SalaryDetails::where('salary_details_id', $request->salary_details_id)->update($data);
            $bug = 0;
        } catch (\Exception $e) {
            $bug = 1;
        }

        if ($bug == 0) {
            echo "success";
        } else {
            echo "error";
        }
    }

    public function generatePayslip($id)
    {
        $paySlipId = $id;
        $ifHourly = SalaryDetails::with(['employee' => function ($q) {
            $q->with(['hourlySalaries', 'department', 'designation']);
        }])->where('salary_details_id', $paySlipId)->first();

        if ($ifHourly->action == 'monthlySalary') {
            $paySlipDataFormat = $this->paySlipDataFormat($paySlipId);
        } else {
            $companyAddress = CompanyAddressSetting::first();
            $data = [
                'salaryDetails' => $ifHourly,
                'companyAddress' => $companyAddress,
                'paySlipId' => $id,
            ];
            return view('admin.payroll.salarySheet.hourlyPaySlip', $data);
        }

        return view('admin.payroll.salarySheet.monthlyPaySlip', $paySlipDataFormat);
    }

    public function paySlipDataFormat($id)
    {
        $printHeadSetting = PrintHeadSetting::first();
        $salaryDetails = SalaryDetails::select('salary_details.*', 'employee.employee_id', 'employee.department_id', 'employee.designation_id', 'department.department_name', 'designation.designation_name', 'employee.first_name', 'employee.last_name', 'pay_grade.pay_grade_name', 'employee.date_of_joining')
            ->join('employee', 'employee.employee_id', 'salary_details.employee_id')
            ->join('department', 'department.department_id', 'employee.department_id')
            ->join('designation', 'designation.designation_id', 'employee.designation_id')
            ->join('pay_grade', 'pay_grade.pay_grade_id', 'employee.pay_grade_id')
            ->where('salary_details_id', $id)->first();

        $salaryDetailsToAllowance = SalaryDetailsToAllowance::join('allowance', 'allowance.allowance_id', 'salary_details_to_allowance.allowance_id')
            ->where('salary_details_id', $id)->get();

        $salaryDetailsToDeduction = SalaryDetailsToDeduction::join('deduction', 'deduction.deduction_id', 'salary_details_to_deduction.deduction_id')
            ->where('salary_details_id', $id)->get();

        $salaryDetailsToLeave = SalaryDetailsToLeave::select('salary_details_to_leave.*', 'leave_type.leave_type_name')
            ->join('leave_type', 'leave_type.leave_type_id', 'salary_details_to_leave.leave_type_id')
            ->where('salary_details_id', $id)->get();

        $monthAndYear = explode('-', $salaryDetails->month_of_salary);
        $start_year = $monthAndYear[0] . '-01';
        $end_year = $salaryDetails->month_of_salary;

        $financialYearTax = SalaryDetails::select(DB::raw('SUM(tax) as totalTax'))
            ->where('status', 1)
            ->where('employee_id', $salaryDetails->employee_id)
            ->whereBetween('month_of_salary', [$start_year, $end_year])
            ->first();

        return $data = [
            'salaryDetails' => $salaryDetails,
            'salaryDetailsToAllowance' => $salaryDetailsToAllowance,
            'salaryDetailsToDeduction' => $salaryDetailsToDeduction,
            'paySlipId' => $id,
            'financialYearTax' => $financialYearTax,
            'salaryDetailsToLeave' => $salaryDetailsToLeave,
            'printHeadSetting' => $printHeadSetting,
        ];
    }

    public function downloadPayslip($id)
    {
        $payslipId = $id;
        $ifHourly = SalaryDetails::with(['employee' => function ($q) {
            $q->with(['hourlySalaries', 'department', 'designation']);
        }])->where('salary_details_id', $payslipId)->first();

        if ($ifHourly->action == 'monthlySalary') {
            $result = $this->paySlipDataFormat($payslipId);
        } else {
            $printHeadSetting = PrintHeadSetting::first();
            $data = [
                'salaryDetails' => $ifHourly,
                'printHeadSetting' => $printHeadSetting,
            ];
            //          return view('admin.payroll.salarySheet.hourlyPaySlipPdf',$data);
            $pdf = \PDF::loadView('admin.payroll.salarySheet.hourlyPaySlipPdf', $data);
            $pdf->setPaper('A4', 'portrait');
            return $pdf->download("payslip.pdf");
        }

        $pdf = \PDF::loadView('admin.payroll.salarySheet.monthlyPaySlipPdf', $result);
        $pdf->setPaper('A4', 'portrait');
        return $pdf->download("payslip.pdf");
    }

    public function downloadMyPayroll()
    {
        $printHeadSetting = PrintHeadSetting::first();
        $results = SalaryDetails::with(['employee' => function ($query) {
            $query->with('payGrade');
        }])->where('status', 1)->where('employee_id', decrypt(session('logged_session_data.employee_id')))->orderBy('salary_details_id', 'DESC')->get();

        $data = [
            'printHead' => $printHeadSetting,
            'results' => $results,
        ];

        $pdf = \PDF::loadView('admin.payroll.report.pdf.myPayrollPdf', $data);

        $pdf->setPaper('A4', 'landscape');
        return $pdf->download("my-payroll-Pdf.pdf");
    }

    public function paymentHistory(Request $request)
    {
        $results = '';
        if ($request->month) {
            $results = SalaryDetails::select(
                'salary_details.basic_salary',
                'salary_details.gross_salary',
                'salary_details.month_of_salary',
                DB::raw('CONCAT(COALESCE(employee.first_name,\'\'),\' \',COALESCE(employee.last_name,\'\')) AS fullName'),
                'employee.photo',
                'pay_grade.pay_grade_name',
                'hourly_salaries.hourly_grade',
                'department.department_name'
            )
                ->join('employee', 'employee.employee_id', 'salary_details.employee_id')
                ->join('department', 'department.department_id', 'employee.department_id')
                ->leftJoin('pay_grade', 'pay_grade.pay_grade_id', 'employee.pay_grade_id')
                ->leftJoin('hourly_salaries', 'hourly_salaries.hourly_salaries_id', 'employee.hourly_salaries_id')
                ->where('salary_details.status', 1)
                ->where('salary_details.month_of_salary', $request->month)
                ->orderBy('salary_details_id', 'DESC')
                ->get();
        }

        return view('admin.payroll.report.paymentHistory', ['results' => $results, 'month' => $request->month]);
    }

    public function myPayroll()
    {
        $currentMonth = date('Y-m');
        $lastThreeMonth = date('Y-m', strtotime('-2 months'));
        $results = SalaryDetails::with('employee')
            ->whereBetween('month_of_salary', [$lastThreeMonth, $currentMonth])
            ->where('employee_id', decrypt(session('logged_session_data.employee_id')))
            ->orderBy('salary_details_id', 'DESC')
            ->get();

        return view('admin.payroll.report.myPayroll', ['results' => $results]);
    }


    public function payslip(Request $request)
    {
        $results = SalaryDetails::with(['employee' => function ($query) {
            $query->with(['department', 'payGrade', 'hourlySalaries']);
        }])->orderBy('salary_details_id', 'DESC')->paginate(10);

        if (request()->ajax()) {

            $results = SalaryDetails::with(['employee' => function ($query) {
                $query->with(['department', 'payGrade', 'hourlySalaries']);
            }])->orderBy('salary_details_id', 'DESC');

            if ($request->monthField != '') {
                $results->where('status', 1)->where('month_of_salary', $request->monthField);
            }

            $results = $results->paginate(10);

            return View('admin.payroll.salarySheet.pagination', compact('results'))->render();
        }

        $departmentList = $this->commonRepository->departmentList();
        return view('admin.payroll.salarySheet.downloadPayslip', ['results' => $results, 'departmentList' => $departmentList]);
    }

    public function payment(Request $request)
    {
        //Input items of form
        $input = Input::all();
        //get API Configuration
        $api = new Api("rzp_test_EzVAI0XNlc8bPq", "bxvBaGVNPKuCj4qmJuiTJyoK");
        //Fetch payment information by razorpay_payment_id
        $payment = $api->payment->fetch($input['razorpay_payment_id']);

        if (count($input) && !empty($input['razorpay_payment_id'])) {
            try {
                $response = $api->payment->fetch($input['razorpay_payment_id'])->capture(array('amount' => $payment['amount']));

                $data['status'] = 1;
                $data['comment'] = $request->comment;
                $data['payment_method'] = "RazorPay";
                $data['created_by'] = Auth::user()->user_id;
                $data['updated_by'] = Auth::user()->user_id;
                $data['created_at'] = Carbon::now();
                $data['updated_at'] = Carbon::now();

                if ($response) {
                    $store = SalaryDetails::where('salary_details_id', $request->salary_details_id)->update($data);
                }
            } catch (\Exception $e) {
                return $e->getMessage();
                Session::put('error', $e->getMessage());
                return redirect()->back();
            }

            // Do something here for store payment details in database...
        }

        Session::put('success', 'Payment successful, your order will be despatched in the next 48 hours.');
        return redirect()->back();
    }

    public function calculateEmployeeSalaryDetails($month)
    {
        $employeeIds = Employee::where('status', UserStatus::$ACTIVE)->orderBy('employee_id', 'asc')->pluck('employee_id');
        // $month_of_salary = $request->month;
        $month = monthConvertFormtoDB(\Carbon\Carbon::createFromFormat('Y-m', $month));
        // $month = monthConvertFormtoDB(Carbon::now()->subMonth(2)->format('Y-m'));
        // dd($month);
        foreach ($employeeIds as $key => $id) {
            // dd($employeeIds);

            // $employeeList = $this->employeeList($id);
            // // dd($employeeList);

            $employeeDetails = Employee::with('payGrade', 'hourlySalaries', 'department', 'designation')->where('employee_id', $id)->first();
            // dd($employeeDetails);

            if ($employeeDetails->pay_grade_id != 0) {
                $employeeAllInfo = [];
                $allowance = [];
                $deduction = [];
                $tax = 0;

                $from_date = $month . "-01";
                $to_date = date('Y-m-t', strtotime($from_date));

                $employeeList = $this->employeeList($id, $from_date, $to_date);

                $leaveRecord = LeaveApplication::select('leave_type.leave_type_id', 'leave_type_name', 'number_of_day', 'application_from_date', 'application_to_date')
                    ->join('leave_type', 'leave_type.leave_type_id', 'leave_application.leave_type_id')
                    ->where('status', LeaveStatus::$APPROVE)
                    ->where('application_from_date', '>=', $from_date)
                    ->where('application_to_date', '<=', $to_date)
                    ->where('employee_id', $id)
                    ->get();
                // dd($leaveRecord);

                $monthAndYear = explode('-', $month);
                $start_year = $monthAndYear[0] . '-01';
                $end_year = $monthAndYear[0] . '-12';

                $financialYearTax = SalaryDetails::select(DB::raw('SUM(tax) as totalTax'))
                    ->where('status', 1)
                    ->where('employee_id', $id)
                    ->whereBetween('month_of_salary', [$start_year, $end_year])
                    ->first();

                $allowance = $this->payrollRepository->calculateEmployeeAllowance($employeeDetails->payGrade->basic_salary, $employeeDetails->pay_grade_id);

                $deduction = $this->payrollRepository->calculateEmployeeDeduction($employeeDetails->payGrade->basic_salary, $employeeDetails->pay_grade_id);

                $advanceDeduction = $this->payrollRepository->calculateEmployeeAdvanceDeduction($id);

                $monthlyDeduction = $this->payrollRepository->calculateEmployeeMonthlyDeduction($id, $month);

                $tax = $this->payrollRepository->calculateEmployeeTax(
                    $employeeDetails->payGrade->gross_salary,
                    $employeeDetails->payGrade->basic_salary,
                    $employeeDetails->date_of_birth,
                    $employeeDetails->gender,
                    $employeeDetails->pay_grade_id
                );
                $employeeAllInfo = $this->payrollRepository->getEmployeeOtmAbsLvLtAndWokDays(
                    $id,
                    $month,
                    $employeeDetails->payGrade->overtime_rate,
                    $employeeDetails->payGrade->basic_salary
                );

                $input = [
                    'employeeList' => $employeeList,
                    'allowances' => $allowance,
                    'deductions' => $deduction,
                    'advanceDeduction' => $advanceDeduction,
                    'monthlyDeduction' => $monthlyDeduction,
                    'tax' => $tax['monthlyTax'],
                    'taxAbleSalary' => $tax['taxAbleSalary'],
                    'employee_id' => $id,
                    'month' => $month,
                    'employeeAllInfo' => $employeeAllInfo,
                    'employeeDetails' => $employeeDetails,
                    'leaveRecords' => $leaveRecord,
                    'financialYearTax' => $financialYearTax,
                    'employeeGrossSalary' => $employeeDetails->payGrade->gross_salary,
                ];
                // dd($input);

            } else {
                $employeeHourlySalary = $this->payrollRepository->getEmployeeHourlySalary($id, $month, $employeeDetails->hourlySalaries->hourly_rate);
                // dd($employeeHourlySalary);
                // dd($employeeDetails->hourlySalaries->hourly_rate);
                $from_date = $month . "-01";
                $to_date = date('Y-m-t', strtotime($from_date));

                $employeeList = $this->employeeList($id, $from_date, $to_date);

                $input = [
                    'employeeList' => $employeeList,
                    'hourly_rate' => $employeeDetails->hourlySalaries->hourly_rate,
                    'employee_id' => $id,
                    'month' => $month,
                    'totalWorkingHour' => $employeeHourlySalary['totalWorkingHour'],
                    'totalSalary' => $employeeHourlySalary['totalSalary'],
                    'employeeDetails' => $employeeDetails,
                ];
                $data[$key] = $input;
            }
            $data[$key] = $input;
        }
        // dd($data);
        // return $employeeDetails->payGrade->basic_salary;
        return $data;
    }

    public function employeeList($id, $from_date, $to_date)
    {
        $results = Employee::where('employee_id', $id)->where('status', 1)->join('view_employee_in_out_data', 'view_employee_in_out_data.finger_print_id', '=', 'employee.finger_id')->whereBetween('date', [$from_date, $to_date])->get();
        foreach ($results as $key => $value) {
            $options[$value->employee_id] = $value->first_name . ' ' . $value->last_name;
        }
        // dd($options);
        return $options;
    }
    public function employeeIdList()
    {
        $results = Employee::where('status', 1)->orderBy('first_name', 'asc')->get();
        foreach ($results as $key => $value) {
            $options[$value->employee_id] = $value->first_name . ' ' . $value->last_name;
        }
        return $options;
    }

    public function generateSalarySheetToAllEmployees(Request $request)
    {
        try {
            $current_month = Carbon::today()->format('Y-m');
            $past_month = Carbon::today()->subMonth(1)->format('Y-m');
            if (isset($request->month) && $current_month != $request->month && $past_month == $request->month) {
                // dump(date('Y-m'));
                // dump($request->month);
                // dd(Carbon::today()->subMonth(1)->format('Y-m'));

                $month = monthConvertFormtoDB(\Carbon\Carbon::createFromFormat('Y-m', $request->month));
                $from_date = $month . "-01";
                $to_date = date('Y-m-t', strtotime($from_date));
                // dd($month);
                $bug = \null;
                DB::beginTransaction();
                $employeeResults = $this->calculateEmployeeSalaryDetails($month);
                // dd($employeeResults);

                foreach ($employeeResults as $key => $value) {
                    // dd($employeeResults);
                    $time = date('Y-m-d H:i:s');
                    $employee_id = $value['employee_id'];
                    $finger_id = Employee::where('employee_id', $employee_id)->select('finger_id')->first();
                    $branch_id = Employee::where('employee_id', $employee_id)->select('branch_id')->first();
                    $finger_id = $finger_id['finger_id'];
                    $branch_id = $branch_id['branch_id'];
                    $advanceDeduction = isset($value['advanceDeduction']['totalDeduction']) ? $value['advanceDeduction']['totalDeduction'] : 0;
                    $monthlyDeduction = isset($value['monthlyDeduction']['totalMonthlyDeduction']) ? $value['monthlyDeduction']['totalMonthlyDeduction'] : 0;
                    $tax = $value['tax'] ?? 0;
                    $basicSalary = isset($value['employeeDetails']->payGrade->basic_salary) ? $value['employeeDetails']->payGrade->basic_salary : 0;
                    $allowances = isset($value['allowances']['totalAllowance']) ? $value['allowances']['totalAllowance'] : 0;
                    $deductions = isset($value['deductions']['totalDeduction']) ? $value['deductions']['totalDeduction'] : 0;
                    $totalOvertimeAmount = isset($value['employeeAllInfo']['totalOvertimeAmount']) ? $value['employeeAllInfo']['totalOvertimeAmount'] : 0;
                    $totalAbsenceAmount = isset($value['employeeAllInfo']['totalAbsenceAmount']) ? $value['employeeAllInfo']['totalAbsenceAmount'] : 0;
                    $totalLateAmount = isset($value['employeeAllInfo']['totalLateAmount']) ? $value['employeeAllInfo']['totalLateAmount'] : 0;
                    $netSalaryMonthly = (($basicSalary + $totalOvertimeAmount) - ($allowances + $deductions + $totalAbsenceAmount + $totalLateAmount + $advanceDeduction + $monthlyDeduction));
                    $totalWorkHour = isset($value['totalSalary']['totalWorkingHour']) ? $value['totalSalary']['totalWorkingHour'] : 0;
                    $netHourlySalary = isset($value['totalSalary']['totalSalary']) ? $value['totalSalary']['totalSalary'] : 0;
                    $hourlyRate = isset($value['employeeDetails']->hourlySalaries->hourly_rate) ? $value['employeeDetails']->hourlySalaries->hourly_rate : 0;
                    $totalLate = isset($value['employeeAllInfo']['totalLate']) ? $value['employeeAllInfo']['totalLate'] : 0;
                    $totalAbsence = isset($value['employeeAllInfo']['totalAbsence']) ? $value['employeeAllInfo']['totalAbsence'] : 0;
                    $overtimeRate = isset($value['employeeAllInfo']['overtime_rate']) ? $value['employeeAllInfo']['overtime_rate'] : 0;
                    $oneDaysSalary = isset($value['employeeAllInfo']['oneDaysSalary']) ? $value['employeeAllInfo']['oneDaysSalary'] : 0;
                    $totalOverTimeHour = isset($value['employeeAllInfo']['totalOverTimeHour']) ? $value['employeeAllInfo']['totalOverTimeHour'] : 0;
                    $totalPresent = isset($value['employeeAllInfo']['totalPresent']) ? $value['employeeAllInfo']['totalPresent'] : 0;
                    $totalLeave = isset($value['employeeAllInfo']['totalLeave']) ? $value['employeeAllInfo']['totalLeave'] : 0;
                    $totalWorkingDays = isset($value['employeeAllInfo']['totalWorkingDays']) ? $value['employeeAllInfo']['totalWorkingDays'] : 0;
                    $taxAbleSalary = isset($value['taxAbleSalary']) ? $value['taxAbleSalary'] : 0;
                    // dd($netHourlySalary);

                    $query = DB::table('view_employee_in_out_data')->whereBetween('date', [$from_date, $to_date])
                        ->where('finger_print_id', $finger_id)->first();
                    // dd($query);

                    $queryResult = SalaryDetails::where('employee_id', $employee_id)->where('month_of_salary', $month)->first();
                    // return($queryResult);

                    // $ifExists    = json_decode(DB::table(DB::raw("(SELECT salary_details.*"))
                    //         ->select('salary_details.salary_detail_id')
                    //         ->where('salary_details.employee_id', $employee_id)
                    //         ->get()->toJson(), true);
                    // dd($employee_id);
                    // DB::table('salary_details')->whereIn('salary_detail_id', array_values($ifExists))->delete();

                    if ($queryResult) {
                        $bug = 1;
                    } elseif (!$query) {
                        $bug = 2;
                    } else {
                        $inputData = [
                            'employee_id' => $employee_id,
                            'branch_id' => $branch_id,
                            'month_of_salary' => monthConvertFormtoDB($month),
                            'basic_salary' => $basicSalary,
                            'total_allowance' => $allowances,
                            'total_deductions' => $deductions,
                            'total_late' => $totalLate,
                            'total_late_amount' => $totalLateAmount,
                            'total_absence' => $totalAbsence,
                            'total_absence_amount' => $totalAbsenceAmount,
                            'overtime_rate' => $overtimeRate,
                            'per_day_salary' => $oneDaysSalary,
                            'total_over_time_hour' => $totalOverTimeHour,
                            'total_overtime_amount' => $totalOvertimeAmount,
                            'total_present' => $totalPresent,
                            'total_leave' => $totalLeave,
                            'total_working_days' => $totalWorkingDays,
                            'net_salary' => $netSalaryMonthly != 0 ? $netSalaryMonthly : $netHourlySalary,
                            'hourly_rate' => $hourlyRate,
                            'tax' => $tax,
                            'taxable_salary' => $taxAbleSalary,
                            'gross_salary' => $netSalaryMonthly != 0 ? $netSalaryMonthly : $netHourlySalary,
                            'created_by' => Auth::user()->user_id,
                            'updated_by' => Auth::user()->user_id,
                            'status' => 0,
                            'comment' => \null,
                            'payment_method' => \null,
                            'action' => 'monthlySalary',
                            'created_at' => $time,
                            'updated_at' => $time,
                        ];
                        DB::table('salary_details')->insert([$inputData]);
                        // dd($inputData);
                        DB::commit();
                        $bug = 0;
                    }
                }
            } elseif ($current_month == $request->month) {
                $bug = 4;
            } elseif (!isset($request->month)) {
                $bug = 3;
            } else {
                $bug = 5;
            }
        } catch (\Exception $e) {
            DB::rollback();
            $bug = 3;
            $message = $e->getMessage();
        }

        switch ($bug) {
            case 0:
                return redirect('generateSalarySheet')->with('success', 'Salary Generate successfully.');
                break;
            case 1:
                return redirect('generateSalarySheet')->with('error', 'Salary already generated for this month.');
                break;
            case 2:
                return redirect('generateSalarySheet/create')->with('error', 'No attendance found.');
                break;
            case 3:
                return redirect('generateSalarySheet/create')->with('error', 'Please select month and try again.');
                break;
            case 4:
                return redirect('generateSalarySheet/create')->with('error', 'Salary cannot be generated for ongoing month ! , Please select correct month.');
                break;
            case 5:
                return redirect('generateSalarySheet/create')->with('error', 'Salary cannot be generated for earlier months ! , Please select correct month.');
                break;
            default:
                return redirect('generateSalarySheet')->with('error', 'Something Error Found !, Please try again.');
        }
    }

    public function makePaymentToAllEmployees(Request $request)
    {

        try {
            $current_month = Carbon::today()->format('Y-m');
            $past_month = Carbon::today()->subMonth(1)->format('Y-m');
            if (isset($request->month) && $current_month != $request->month && $past_month == $request->month) {
                // dd($current_month != $request->month,$past_month == $request->month);
                $month = monthConvertFormtoDB(\Carbon\Carbon::createFromFormat('Y-m', $request->month));
                $salaryDetails = Employee::Join('salary_details', 'salary_details.employee_id', '=', 'employee.employee_id')
                    ->where('salary_details.month_of_salary', $month)
                    ->select('salary_details.employee_id', 'salary_details.salary_details_id')
                    ->orderBy('salary_details.salary_details_id')->get();
                // dd($salaryDetails);
                $bug = 2;
                $message = 'No Data Found';
                DB::beginTransaction();
                if (!empty($salaryDetails)) {
                    foreach ($salaryDetails as $key => $value) {
                        // dd($value);
                        // dd($value['employee_id']);
                        $queryResult = SalaryDetails::where('employee_id', $value['employee_id'])->where('month_of_salary', $month)->first();
                        // dd($queryResult);
                        if ($queryResult) {
                            $inputData = [
                                'status' => 1,
                                'comment' => "None",
                                'payment_method' => "RazorPay",
                                'created_by' => Auth::user()->user_id,
                                'updated_by' => Auth::user()->user_id,
                                'created_at' => Carbon::now(),
                                'updated_at' => Carbon::now(),
                            ];
                            SalaryDetails::where('salary_details_id', $value['salary_details_id'])->where('employee_id', $value['employee_id'])->update($inputData);
                            DB::commit();
                            $bug = 0;
                        }
                    }
                }
            } else {
                $bug = 3;
            }
        } catch (\Exception $e) {
            DB::rollback();
            $bug = 4;
            $message = $e->getMessage();
        }

        switch ($bug) {
            case 0:
                return redirect('generateSalarySheet')->with('success', 'Payment successfully.');
                break;
            case 1:
                return redirect('generateSalarySheet/create')->with('error', 'Please select month and try again.');
                break;
            case 2:
                return redirect('generateSalarySheet/create')->with('error', $message . ' !, Please try again.');
                break;
            case 3:
                return redirect('generateSalarySheet/create')->with('error', 'Salary detials not avaliable for this month.');
                break;
            case 4:
                return redirect('generateSalarySheet/create')->with('error', $message);
                break;
            default:
                return redirect('generateSalarySheet')->with('error', 'Something Error Found !, Please try again.');
        }
    }

    public function downloadPayslipForAllEmployee()
    {
        $salaryDetails = Employee::Join('salary_details', 'salary_details.employee_id', '=', 'employee.employee_id')
            ->where('salary_details.month_of_salary', '2022-01')
            ->orderBy('salary_details.salary_details_id')->pluck('salary_details.salary_details_id');

        foreach ($salaryDetails as $key => $payslipId) {
            $ifHourly = SalaryDetails::with(['employee' => function ($q) {
                $q->with(['hourlySalaries', 'department', 'designation']);
            }])->where('salary_details_id', $payslipId)->first();

            if ($ifHourly->action == 'monthlySalary') {
                $result = $this->paySlipDataFormat($payslipId);
            } else {
                $printHeadSetting = PrintHeadSetting::first();
                $data = [
                    'salaryDetails' => $ifHourly,
                    'printHeadSetting' => $printHeadSetting,
                ];

                $pdf = \PDF::loadView('admin.payroll.salarySheet.hourlyPaySlipPdf', $data);
                $pdf->setPaper('A4', 'portrait');
                return $pdf->download("payslip.pdf");
            }

            $pdf = \PDF::loadView('admin.payroll.salarySheet.monthlyPaySlipPdf', $result);
            $pdf->setPaper('A4', 'portrait');
            return $pdf->download("payslip.pdf");
        }
    }

    public function detail($month)
    {
        $employeeResults = $this->calculateEmployeeSalaryDetails($month);
        return ($employeeResults);
    }

    public function downloadSalarySheet(Request $request)
    {

        $data =  [];

        $results = SalaryDetails::with(['employee' => function ($query) {
            $query->with(['department']);
        }])->orderBy('salary_details_id', 'DESC');

        if ($request->monthField != '') {
            $results->where('month_of_salary', $request->monthField);
        }

        $results = $results->get();

        foreach ($results as $key => $value) {
            $data[] = $this->salaryDataFormat($value);
        }

        $extraData['heading'] = [
            'month',
            'name',
            'employee_id',
            'deparmtent',
            'designation',
            'nationality',
            'branch',
            'name_of_the_bank',
            'basic',
            'increment',
            'basic_salary',
            'housing_allowance',
            'utility_allowance',
            'transport_allowance',
            'living_allowance',
            'mobile_allowance',
            'special_allowance',
            'membership_allowance',
            'education_and_club_allowance',
            'arrears_adjustment',
            'gross_salary',
            'lop',
            'pay_cut',
            'gsm',
            'prem_others',
            'salary_advance',
            'social_security',
            'total_deductions',
            'net_salary',
        ];

        // Step 1: flip array key => value
        $extraData['heading'] = array_flip($extraData['heading']);

        // Step 2: change case of new keys to upper
        $extraData['heading'] = array_change_key_case($extraData['heading'], CASE_UPPER);

        // Step 3: reverse the flip process to
        // regain strings as value
        $extraData['heading'] = array_flip($extraData['heading']);

        $filename = 'SalarySheet-' . DATE('Y-m', strtotime($request->monthField)) . '.xlsx';

        return Excel::download(new SalaryDetailExport($data, $extraData), $filename);
    }

    public function salaryDataFormat($value)
    {
        $data['month'] = date('F Y', strtotime($value->month_of_salary));
        $data['name'] = $value->employee->fullname();
        $data['employee_id'] = $value->employee->finger_id;
        $data['department'] = $value->employee->departmentName();
        $data['designation'] = $value->employee->designationName();
        $data['nationality'] = $value->employee->nationality == 0 ? 'Omani' : 'Expatriate';
        $data['branch'] = $value->employee->branchName();
        $data['name_of_the_bank'] = $value->name_of_the_bank;
        $data['basic'] = $value->basic_salary;
        $data['increment'] = $value->increment_amount;
        $data['basic_salary'] = $value->basic_salary + $value->increment_amount;
        $data['housing_allowance'] = $value->housing_allowance;
        $data['utility_allowance'] = $value->utility_allowance;
        $data['transport_allowance'] = $value->transport_allowance;
        $data['living_allowance'] = $value->living_allowance;
        $data['mobile_allowance'] = $value->mobile_allowance;
        $data['special_allowance'] = $value->special_allowance;
        $data['membership_allowance'] = $value->membership_allowance;
        $data['education_and_club_allowance'] = $value->education_and_club_allowance;
        $data['arrears_adjustment'] = $value->arrears_adjustment;

        $data['gross_salary'] = $value->gross_salary;
        $data['lop'] = $value->lop;
        $data['pay_cut'] = $value->pay_cut;
        $data['gsm'] = $value->gsm;
        $data['prem_others'] = $value->prem_others;
        $data['salary_advance'] = $value->salary_advance;
        $data['social_security'] = $value->social_security;
        $data['total_deductions'] = $value->total_deductions;
        $data['net_salary'] = $value->net_salary;

        return $data;
    }

    public function downloadPayslipPdf($salary_details_id)
    {
        $salaryDetails = SalaryDetails::findOrFail($salary_details_id);

        $allowances = $deductions = [];

        $allowances['basic_salary'] = $salaryDetails->basic_salary + $salaryDetails->increment_amount;
        $allowances['housing_allowance'] = $salaryDetails->housing_allowance;
        $allowances['utility_allowance'] = $salaryDetails->utility_allowance;
        $allowances['transport_allowance'] = $salaryDetails->transport_allowance;
        $allowances['living_allowance'] = $salaryDetails->living_allowance;
        $allowances['mobile_allowance'] = $salaryDetails->mobile_allowance;
        $allowances['special_allowance'] = $salaryDetails->special_allowance;
        $allowances['membership_allowance'] = $salaryDetails->membership_allowance;
        $allowances['extra_amount'] = $salaryDetails->extra_amount;
        $allowances['education_and_club_allowance'] = $salaryDetails->education_and_club_allowance;

        $deductions['lop'] = $salaryDetails->lop;
        $deductions['pay_cut'] = $salaryDetails->pay_cut;
        $deductions['gsm'] = $salaryDetails->gsm;
        $deductions['prem_others'] = $salaryDetails->prem_others;
        $deductions['salary_advance'] = $salaryDetails->salary_advance;
        $deductions['social_security'] = $salaryDetails->social_security;

        $data['allowances'] = $allowances;
        $data['deductions'] = $deductions;
        $data['arrears_adjustment'] = $salaryDetails->arrears_adjustment;
        $data['net_salary'] = $salaryDetails->net_salary;
        $data['total_deduction'] = $salaryDetails->total_deductions;
        $data['gross_salary'] = $salaryDetails->gross_salary;
        $data['salary'] = $salaryDetails;

        // $digit = new NumberFormatter("en_US", NumberFormatter::SPELLOUT);
        // $explode_net = explode('.', $salaryDetails->net_salary);
        // $data['net_salary_in_words'] =  ucwords($digit->format($explode_net[0]) . ' OMR and ' .   $digit->format($explode_net[1]) . ' BZ');
        // $data['net_salary_in_words'] =  $digit->format($salaryDetails->net_salary);

        $pdf = \PDF::loadView('admin.payroll.salarySheet.monthlyPaySlipPdf', $data);
        $pdf->setPaper('A4', 'portrait');
        return $pdf->stream("payslip.pdf");
    }

    public function check()
    {
        return 'Hi';
    }
}
