<?php

namespace App\Http\Controllers\Payroll;

use Carbon\Carbon;
use App\Model\Branch;
use App\Model\Employee;
use Carbon\CarbonPeriod;
use App\Model\Department;
use App\Model\Designation;
use App\Model\SalaryDetails;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Lib\Enumerations\UserStatus;
use Maatwebsite\Excel\Facades\Excel;
use App\Exports\SocialSecurityReportExport;
use App\Exports\SocialSecuritySummaryExport;

class SocialSecurityReportController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index(Request $request)
    {

        $branchList = Branch::get();
        $departmentList = Department::get();
        $designationList = Designation::get();
        $results = [];
        if ($_POST) {
            $results = SalaryDetails::query();
            if ($request->branch_id) {

                $results = $results->whereHas('employee', function ($query) use ($request) {
                    $query->where('branch_id', $request->branch_id);
                });
            }
            if ($request->department_id) {

                $results = $results->whereHas('employee', function ($query) use ($request) {
                    $query->where('department_id', $request->department_id);
                });
            }
            if ($request->designation_id) {

                $results = $results->whereHas('employee', function ($query) use ($request) {
                    $query->where('designation_id', $request->designation_id);
                });
            }


            if ($request->month) {

                $results = $results->where('month_of_salary', $request->month);
            }
            $results = $results->get();
        }
        return view('admin.payroll.socialSecurity.report', ['results' => $results, 'branchList' => $branchList, 'departmentList' => $departmentList, 'designationList' => $designationList]);
    }


    public function summary(Request $request)
    {
        $branchList = Branch::get();
        $departmentList = Department::get();
        $designationList = Designation::get();

        if ($request->year) {
            $year = $request->year;
        } else {
            $year = date('Y');
        }
        // dd($year);
        $start = Carbon::create($year, 1, 1); // Start from January 1st of the given year
        $end = Carbon::create($year, 12, 31);
        $period = CarbonPeriod::create($start, '1 month', $end);
        $months = [];
        foreach ($period as $date) {
            $months[] = date('Y-m', strtotime($date));
        }
        $emp = [];

        if ($_POST) {
            // dd(gettype($months));
            $results = Employee::where('status', UserStatus::$ACTIVE);
            if ($request->branch_id) {
                $results = $results->where('branch_id', $request->branch_id);
            }
            if ($request->department_id) {
                $results = $results->where('department_id', $request->department_id);
            }
            if ($request->designation_id) {
                $results = $results->where('designation_id', $request->designation_id);
            }
            $results = $results->get();

            foreach ($period as $date) {
                foreach ($results as $data) {
                    $check = SalaryDetails::where('employee_id', $data->employee_id)->where('month_of_salary', date('Y-m', strtotime($date)))->first();
                    if ($check) {
                        $emp['salary_details'][$data->employee_id][] = $check;
                    } else {
                        $emp['salary_details'][$data->employee_id][] = null;
                    }
                }
            }
        } else {
            $results = [];
        }

        return view('admin.payroll.socialSecurity.summary', ['results' => $results, 'salary' => $emp, 'branchList' => $branchList, 'departmentList' => $departmentList, 'designationList' => $designationList, 'year' => $year, 'yearToMonth' => $period]);
    }


    public function reportExcel($month, $branch_id = null, $department_id = null, $designation_id = null)
    {

        $results = SalaryDetails::query();
        if ($branch_id) {

            $results = $results->whereHas('employee', function ($query) use ($branch_id) {
                $query->where('branch_id', $branch_id);
            });
        }
        if ($department_id) {

            $results = $results->whereHas('employee', function ($query) use ($department_id) {
                $query->where('department_id', $department_id);
            });
        }
        if ($designation_id) {

            $results = $results->whereHas('employee', function ($query) use ($designation_id) {
                $query->where('designation_id', $designation_id);
            });
        }


        if ($month) {

            $results = $results->where('month_of_salary', $month);
        }
        $results = $results->get();
        $heading = [];
        $column = ["SERIAL NO", "MONTH", "NAME", "FINGER ID", "BRANCH", "DEPARTMENT", "DESIGNATION", "EMPLOYEE CONTRIBUTION", "EMPLOYER CONTRIBUTION"];
        $heading[] = $column;

        foreach ($results as $key => $value) {
            $dataSet[] = [
                $key + 1,
                $value->month_of_salary,
                $value->employee->fullName(),
                $value->employee->finger_id,
                $value->employee->branchName(),
                $value->employee->departmentName(),
                $value->employee->designationName(),
                $value->social_security,
                $value->employer_contribution,

            ];
        }
        return Excel::download(new SocialSecurityReportExport($dataSet, $heading), 'SocialSecurityReport.xlsx');
    }


    public function excel($year, $branch_id = null, $department_id = null, $designation_id = null)
    {
        $start = Carbon::create($year, 1, 1); // Start from January 1st of the given year
        $end = Carbon::create($year, 12, 31);
        $period = CarbonPeriod::create($start, '1 month', $end);
        $results = Employee::where('status', UserStatus::$ACTIVE);
        if ($branch_id) {
            $results = $results->where('branch_id', $branch_id);
        }
        if ($department_id) {
            $results = $results->where('department_id', $department_id);
        }
        if ($designation_id) {
            $results = $results->where('designation_id', $designation_id);
        }
        $results = $results->get();


        $dataSet = [];
        $social = [];
        foreach ($results as $key => $value) {
            $dataSet[$value->employee_id] = [
                $key + 1,
                $value->fullName(),
                $value->finger_id,
                $value->branchName(),
                $value->departmentName(),
                $value->designationName(),
            ];

            $socialSecurity = [];
            $employerContribution = [];
            foreach ($period as $date) {

                $check = SalaryDetails::where('employee_id', $value->employee_id)
                    ->where('month_of_salary', date('Y-m', strtotime($date)))
                    ->first();
                if ($check) {
                    $socialSecurity[] = $check->social_security;
                    $employerContribution[] = $check->employer_contribution;
                } else {
                    $socialSecurity[] = "--";
                    $employerContribution[] = "--";
                }
            }
            $array = [];
            foreach ($socialSecurity as $key1 => $social) {
                $array[] = [
                    $social,
                    $employerContribution[$key1],
                ];
            }
            foreach ($array as $dat) {
                foreach ($dat as $emp) {
                    $dataSet[$value->employee_id][] = $emp;
                }
            }
        }
        // dd($dataSet);


        $merge = ["G1:H1", "I1:J1", "K1:L1", "M1:N1", "O1:P1", "Q1:R1", "S1:T1", "U1:V1", "W1:X1", "Y1:Z1", "AA1:AB1", "AC1:AD1"];
        $heading = [];
        $column = ["SERIAL NO", "NAME", "FINGER ID", "BRANCH", "DEPARTMENT", "DESIGNATION"];

        foreach ($period as $date) {
            $column[] =  date('Y-m', strtotime($date));
            $column[] =  "";
        }
        $column2 = [];

        $column2 = ["#", "#", "#", "#", "#", "#"];
        foreach ($period as $date) {
            $column2[] =  "Employee";
            $column2[] =  "Employer";
        }

        $heading[] = $column;
        $heading[] = $column2;

        return Excel::download(new SocialSecuritySummaryExport($dataSet, $heading, $merge), 'SocialSecuritySummary.xlsx');
    }

    public function create()
    {
        //
    }


    public function store(Request $request)
    {
        //
    }


    public function show($id)
    {
        //
    }

    public function edit($id)
    {
        //
    }


    public function update(Request $request, $id)
    {
        //
    }


    public function destroy($id)
    {
        //
    }
}
