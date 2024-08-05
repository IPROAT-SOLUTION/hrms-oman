<?php

namespace App\Http\Controllers\Api;

use App\Model\Otp;
use Carbon\Carbon;
use App\Model\Employee;
use App\Model\Department;
use App\Components\Common;
use App\Model\Designation;
use App\Model\SalaryDetails;
use Illuminate\Http\Request;
use App\Model\PrintHeadSetting;
use Barryvdh\DomPDF\PDF as PDF;
use Illuminate\Support\Facades\DB;
use App\Model\SalaryDetailsToLeave;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Auth;
use App\Repositories\CommonRepository;
use App\Model\SalaryDetailsToAllowance;
use App\Model\SalaryDetailsToDeduction;

class PayslipController extends Controller
{
    protected $commonRepository;
    protected $controller;

    public function __construct(Controller $controller, CommonRepository $commonRepository)
    {
        $this->commonRepository = $commonRepository;
        $this->controller  = $controller;
    }


    public function myPayroll(Request $request)
    {

        $employee_id = $request->employee_id;

        $results = SalaryDetails::with(['employee' => function ($query) {
            $query->with('payGrade');
        }])->where('status', 1)->where('employee_id', $employee_id)->orderBy('salary_details_id', 'DESC')->get();

        return response()->json([
            'message' => "My paroll details received successfully",
            'data' =>  $results,
        ], 200);
    }

    public function payslip(Request $request)
    {

        $employee_id = $request->employee_id;

        $results = SalaryDetails::with(['employee' => function ($query) {
            $query->with(['department', 'payGrade', 'hourlySalaries']);
        }])->where('employee_id', $employee_id)->orderBy('salary_details_id', 'DESC')->get();

        if ($request->month_of_salary) {

            $results = SalaryDetails::with(['employee' => function ($query) {
                $query->with(['department', 'payGrade', 'hourlySalaries']);
            }])->where('employee_id', $request->employee_id)->orderBy('salary_details_id', 'DESC');

            if ($request->month_of_salary != '') {
                $results->where('status', 1)->where('month_of_salary', $request->month_of_salary);
            }

            $results = $results->get();

            if ($results != []) {
                return response()->json([
                    'message' => "My payslip details received successfully.",
                    'data' =>  $results,
                ], 200);
            } else {
                return response()->json([
                    'message' => "No records found.",
                    'data' =>  $results,
                ], 200);
            }
        }

        $departmentList = $this->commonRepository->departmentList();

        if ($results != [] && $departmentList != []) {
            return response()->json([
                'message' => "My payslip details received successfully.",
                'departmentList' =>  $departmentList,
                'data' =>  $results,
            ], 200);
        } else {
            return response()->json([
                'message' => "No records found.",
                'departmentList' =>  $departmentList,
                'data' =>  $results,
            ], 200);
        }
    }

    // public function downloadMyPayroll(Request $request)
    // {

    //     $employee_id = $request->employee_id;
    //     $printHeadSetting = PrintHeadSetting::first();

    //     $results          = SalaryDetails::with(['employee' => function ($query) {
    //         $query->with('payGrade');
    //     }])->where('status', 1)->where('employee_id', $employee_id)->orderBy('salary_details_id', 'DESC')->get();

    //     $data = [
    //         'results'   => $results,
    //         'printHead' => $printHeadSetting,
    //     ];

    //     $pdf = PDF::loadView('admin.payroll.report.pdf.myPayrollPdf', $data);

    //     $pdf->setPaper('A4', 'landscape');
    //     return $pdf->download("my-payroll-Pdf.pdf");
    // }
    public function myPayslipList(Request $request)
    {

        $employee_id = $request->employee_id;

        $payment_list = [];
        $results = SalaryDetails::with(['employee' => function ($query) {
            $query->with(['department', 'designation']);
        }])->where('employee_id', $employee_id)->orderBy('salary_details_id', 'DESC')->get();
        if ($results) {
            foreach ($results as $value) {
                $employee = Employee::with(['designation', 'department'])->where('employee_id', $value->employee_id)->first();
                $payment_list[] = [
                    "user_id"           => $employee->user_id,
                    "user_name"         => $employee->first_name . ' ' . $employee->last_name,
                    "designation"       => $employee->designation->designation_name,
                    "finger_id"         => $employee->finger_id,
                    "department"        => $employee->department->department_name,
                    "branch_id"         => $employee->branch_id,
                    "salary_details_id" => $value->salary_details_id,
                    "month_year"        => date('M-Y', strtotime($value->month_of_salary)),
                    "basic_salary"      => $value->basic_salary,
                    "per_day_salary"    => $value->per_day_salary,
                    "employee_id"       => $value->employee_id,
                    "total_working_days" => round($value->total_working_days, 2),
                    "net_salary"        => $value->net_salary,
                    "gross_salary"      => $value->gross_salary,
                    "total_paid_days"   => round($value->total_paid_days, 1),
                    "excess_days"       => round($value->excess_days, 1),
                    "total_half_days"   => $value->total_half_days,
                    "total_full_days"   => $value->total_full_days,
                    "total_worked_days" => round($value->total_worked_days, 1),
                    "total_leave"       => round($value->total_leave, 1),
                    "total_present"     => round($value->total_present, 1),
                ];
            }
            return response()->json([
                'message' => "My paroll details received successfully",
                'data' =>  $payment_list,
                'status'  =>  true,
            ], 200);
        } else {
            return response()->json([
                'message' => "No Data Found",
                'status'  =>  false,
            ], 200);
        }
    }


    public function downloadPayslip(Request $request)
    {
        try {
            $salaryDetail  = SalaryDetails::with('employee')->where('salary_details_id', $request->id)->firstOrFail();
            
            if ($salaryDetail) {
                $result = $this->paySlipDataFormat($salaryDetail->salary_details_id);
                $pdf = \PDF::loadView('admin.payroll.salarySheet.monthlyPaySlipPdf', $result);
                $pdf->setPaper('A4', 'portrait');
                return $pdf->download("payslip.pdf");
            }

            return $this->controller->custom_error('Payslip Not Found ! try different month');
        } catch (\Throwable $th) {
            return response()->json([
                'status' => false,
                'message' => 'Something went wrong!'
            ]);
        }
    }


    public function PayrollList(Request $request)
    {
        try {
            $employee = Employee::where('employee_id', $request->employee_id)->first();
            $employee_id =  $employee->employee_id;
            $payroll = SalaryDetails::where('employee_id', $employee_id)->orderBy('salary_details_id', 'DESC')->get()->take(3);
            $designation = Designation::find($employee->designation_id);
            // $employee_category=EmployeeCategory::find($employee->category_id);
            $department = Department::find($employee->department_id);


            if ($payroll != '') {
                return response()->json([
                    'message' => "Payroll Data Received Successfully",
                    'payroll' => $payroll,
                    'employee_data' => $employee,
                    'designation' => $designation,
                    // 'employee_category'=>$employee_category,
                    'department' => $department,
                    'status' => true
                ], 200);
            } else {
                return response()->json([
                    'message' => "No Data Found",
                    'status' => false
                ], 200);
            }
        } catch (\Throwable $th) {
            //throw $th;
            info($th);
        }
    }

    public function getOtp(Request $request)
    {
        try {
            //code...

            $salaryDetails = SalaryDetails::where('salary_details_id', $request->id)->first();
            $employee = Employee::where('employee_id', $salaryDetails->employee_id)->first();
            if ($employee->email) {

                $otp = rand(1000, 9999);



                $create = Otp::create([
                    'employee_id' => $employee->employee_id,
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
        } catch (\Throwable $th) {
            //throw $th;
            info($th);
        }
    }
    public function verifyOtp(Request $request)
    {
        $smsCheck = Otp::where('employee_id', $request->employee_id)->where('otp', $request->otp)
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

    public function paySlipDataFormat($salary_details_id)
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
        // $allowances['extra_amount'] = $salaryDetails->extra_amount;

        $deductions['pay_cut'] = $salaryDetails->pay_cut;
        $deductions['gsm'] = $salaryDetails->gsm;
        $deductions['prem_others'] = $salaryDetails->prem_others;
        $deductions['salary_advance'] = $salaryDetails->salary_advance;
        $deductions['social_security'] = $salaryDetails->social_security;

        $data['allowances'] = $allowances;
        $data['deductions'] = $deductions;
        $data['net_salary'] = $salaryDetails->net_salary;
        $data['total_deduction'] = $salaryDetails->total_deductions;
        $data['gross_salary'] = $salaryDetails->gross_salary;
        $data['salary'] = $salaryDetails;
  	$data['arrears_adjustment'] = $salaryDetails->arrears_adjustment;
        $number = $salaryDetails->net_salary;
        return $data;
    }
}
