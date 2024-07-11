<?php

namespace App\Http\Controllers\Attendance;

use App\Model\Branch;
use App\Model\Employee;
use Illuminate\Http\Request;
use App\Model\ApproveOverTime;
use App\Model\EmployeeInOutData;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Auth;
use Maatwebsite\Excel\Facades\Excel;
use App\Lib\Enumerations\AppConstant;
use App\Exports\ApproveOvertimeReport;
use App\Repositories\CommonRepository;
use App\Imports\ApprovedOvertimeImport;
use App\Http\Requests\ApproveOverTimeRequest;

class ApproveOverTimeController extends Controller
{

    protected $commonRepository;

    public function __construct(CommonRepository $commonRepository)
    {
        $this->commonRepository = $commonRepository;
    }
    public function index(Request $request)
    {

        $results = [];
        if ($_POST) {
            $employee_id = decrypt(session('logged_session_data.employee_id'));
            $role_id = decrypt(session('logged_session_data.role_id'));
            $start_date = $request->month . '-01';
            $end_date = date('Y-m-t', strtotime($start_date));
            // dd($request->all());
            $results = EmployeeInOutData::where('finger_print_id', $request->finger_print_id)->with(['employee'])->whereBetween('date', [$start_date, $end_date])->whereNotNull('over_time')->orderBy('date')->get();
            // dd($results);
            // $results = ApproveOverTime::with('employee')->where('employee_id', $request->employee_id)
            //     ->where('date', dateConvertFormtoDB($request->date))
            //     ->with('branch', 'employee')
            //     ->when($role_id  != 1 && $role_id  != 2, function ($q) use ($employee_id) {
            //         $q->whereHas('employee', function ($query) use ($employee_id) {
            //             $query->where("operation_manager_id", $employee_id);
            //         });
            //     })
            //     ->get();

            // if ($request->department_id != '') {
            //     $results = ApproveOverTime::with('employee')->where('branch_id', $request->branch_id)->where('date', dateConvertFormtoDB($request->date))
            //         ->with('branch', 'employee')->whereHas('employee', function ($q) use ($request) {
            //             $q->where('department_id', $request->department_id);
            //         })
            //         ->when($role_id  != 1 && $role_id  != 2, function ($q) use ($employee_id) {
            //             $q->whereHas('employee', function ($query) use ($employee_id) {
            //                 $query->whereRaw("operation_manager_id", $employee_id);
            //             });
            //         })
            //         ->get();
            // }
        }

        // $departmentList = $this->commonRepository->departmentList();
        // $branchList = $this->commonRepository->branchList();
        $employeeList = $this->commonRepository->employeeFingerList();

        return view('admin.attendance.approveOvertime.index', ['results' => $results, 'month' => $request->month, 'finger_print_id' => $request->finger_print_id, 'department_id' => $request->department_id, 'employeeList' => $employeeList]);
    }

    public function create(Request $request)
    {
        $qry = '1 ';
        if ($request->date) {
            $qry = 'date=' . dateConvertFormtoDB($request->date);
        }
        $employeeList = $this->commonRepository->employeeFingerListOT();

        $employee = EmployeeInOutData::whereRaw($qry)->groupBy('finger_print_id')->get('finger_print_id');
        return view('admin.attendance.approveOvertime.form', ['employeeList' => $employeeList]);
    }

    public function store(ApproveOverTimeRequest $request)
    {
        // dd($request->all());
        $employee = Employee::where('finger_id', $request->finger_print_id)->first();
        $employee_id = decrypt(session('logged_session_data.employee_id'));
        $input = $request->all();

        $input['branch_id'] = $employee->branch_id;
        $input['date'] = dateConvertFormtoDB($input['date']);
        $input['approved_overtime'] = date("H:i:s", strtotime($_POST['approved_overtime']));
        $input['created_by'] = $employee_id;
        $input['updated_by'] = $employee_id;

        try {
            $overtime = ApproveOverTime::updateOrCreate(['date' => $input['date'], 'finger_print_id' => $input['finger_print_id']], $input);
            $employeeInOutData = EmployeeInOutData::where('finger_print_id', $overtime->finger_print_id)->where('date', $overtime->date)->update(['approved_over_time' => date("H:i:s", strtotime($_POST['approved_overtime']))]);
            $bug = 0;
        } catch (\Exception $e) {
            $bug = 1;
        }

        if ($bug == 0) {
            return redirect('approveOvertime')->with('success', 'Overtime successfully saved.');
        } else {
            return redirect('approveOvertime')->with('error', 'Something Error Found !, Please try again.');
        }
    }

    public function edit($id)
    {
        $employeeList = $this->commonRepository->employeeFingerListOT();

        $editModeData = EmployeeInOutData::findOrFail($id);
        // select(DB::raw('DATE_FORMAT(approve_over_time.date, "%d/%m/%Y") as date'))->where('approve_over_time_id', $id)->first();

        return view('admin.attendance.approveOvertime.form', ['editModeData' => $editModeData]);
    }

    public function update(ApproveOverTimeRequest $request, $id)
    {

        // dd($request->all());

        $employee_id = decrypt(session('logged_session_data.employee_id'));
        $overtime = EmployeeInOutData::where('employee_attendance_id', $id)->with('employee')->first();
        $input = $request->all();
        $input['date'] = dateConvertFormtoDB($request->date);
        unset($input['_token']);
        unset($input['_method']);
        $input['updated_by'] = $employee_id;

        try {

            $gross_salary =
                $overtime->employee->basic_salary +
                $overtime->employee->housing_allowance +
                $overtime->employee->utility_allowance +
                $overtime->employee->transport_allowance +
                $overtime->employee->living_allowance +
                $overtime->employee->mobile_allowance +
                $overtime->employee->special_allowance +
                $overtime->employee->education_and_club_allowance +
                $overtime->employee->membership_allowance;
            $per_day_amount = $gross_salary / 31 / 9;

            $over_time_amount = round($per_day_amount * decimalHours(date("H:i:s", strtotime($request['approved_over_time']))), 6);

            $approve_overtime = ApproveOverTime::create([
                'finger_print_id' => $overtime->finger_print_id,
                'actual_overtime' => $overtime->over_time,
                'approved_overtime' => $request->approved_over_time,
                'date' => $overtime->date,
                'created_by' => Auth::user()->user_id,
                'updated_by' => Auth::user()->user_id,
                'per_hour_salary' => round($per_day_amount, 3),
                'over_time_amount' => $over_time_amount,
                'gross_salary' => $gross_salary,

            ]);
            $overtime->update(['approved_over_time' => date("H:i:s", strtotime($request['approved_over_time'])), 'approve_over_time_id' => $approve_overtime->approve_over_time_id]);

            $bug = 0;
        } catch (\Exception $e) {
            $bug = 1;
        }

        if ($bug == 0) {
            return redirect()->back()->with('success', 'Overtime successfully updated ');
        } else {
            return redirect()->back()->with('error', 'Something Error Found !, Please try again.');
        }
    }

    public function destroy($id)
    {

        $count = EmployeeInOutData::where('approve_over_time_id', '=', $id)->count();

        if ($count > 0) {
            return 'hasForeignKey';
        }

        try {
            $overtime = ApproveOverTime::FindOrFail($id);
            $employeeInOutData = EmployeeInOutData::where('finger_print_id', $overtime->finger_print_id)->where('date', $overtime->date)->update(['approve_over_time_id' => null]);

            $overtime->delete();

            $bug = 0;
        } catch (\Exception $e) {
            $bug = 1;
        }

        if ($bug == 0) {
            echo "success";
        } else {
            echo 'error';
        }
    }

    public function reportDetails(Request $request)
    {
        $approval = ApproveOverTime::where('finger_print_id', $request->finger_print_id)->where('date', dateConvertFormtoDB($request->date))->exists();
        $reportDetails = EmployeeInOutData::where('finger_print_id', '=', $request->finger_print_id)->where('date', dateConvertFormtoDB($request->date))->first();
        $overtime = isset($reportDetails->over_time) ? $reportDetails->over_time : 'notFound';
        $overtime = $overtime != null ? $overtime : "00:00:00";
        return $overtime;
    }

    // public function approveOvertimeTemplate()
    // {
    //     $file_name = 'templates/approveovertime_detail.xlsx';
    //     $file = Storage::disk('public')->get($file_name);
    //     return (new Response($file, 200))
    //         ->header('Content-Type', 'application/vnd.openxmlformats-officedocument.spreadsheetml.sheet');
    // }

    public function approveOvertimeTemplate(Request $request)
    {
        $date = dateConvertFormtoDB($request->date);
        $inc = 1;
        $dataSet = [];
        $Data = EmployeeInOutData::where('date', $date)->where('over_time', '>=', AppConstant::$MINIMUM_OT_HOUR)->orderBy('finger_print_id', 'ASC')->get();

        foreach ($Data as $key => $data) {

            $dataSet[] = [
                $inc,
                $data->finger_print_id,
                $data->date,
                $data->over_time,
                $data->over_time,
                'Simple Approval',
            ];

            $inc++;
        }

        $primaryHead = ['SL.NO', 'EMPLOYEE ID', 'DATE', 'ACTUAL OT', 'APPROVED OT', 'REMARK'];
        $heading = [$primaryHead];

        $extraData['heading'] = $heading;
        $filename = 'Employee Overtime Information-' . DATE('d-m-Y His') . '.xlsx';

        return Excel::download(new ApproveOvertimeReport($dataSet, $extraData), $filename);
    }

    public function export(Request $request)
    {
        $date = dateConvertFormtoDB($request->date);
        $Data = ApproveOverTime::where('date', $date)->orderBy('approve_over_time_id', 'ASC')->get();
        $inc = 1;
        foreach ($Data as $key => $data) {
            if (isset($data->branch_id)) {
                $branch = Branch::find($data->branch_id);
                $branch_name = $branch->branch_name;
            }

            // if (isset($data->finger_print_id)) {
            //     $employee = Employee::where('finger_id', $data->finger_print_id)->first();
            //     $employee_name = $employee->first_name . ' ' . $employee->last_name;
            // }

            $dataSet[] = [
                $inc,
                $data->finger_print_id,
                $data->date,
                $data->actual_overtime,
                $data->approved_overtime,
                $data->remark,
            ];
            $inc++;
        }

        $primaryHead = ['Sl.No', 'Branch', 'EmployeeID', 'Date', 'Actual OT', 'Approval OT', 'Remark', 'Status'];
        $heading = [$primaryHead];

        $extraData['heading'] = $heading;
        $filename = 'Employee Overtime Information-' . DATE('d-m-Y His') . '.xlsx';

        return Excel::download(new ApproveOvertimeReport($dataSet, $extraData), $filename);
    }

    public function import(Request $request)
    {
        try {

            $date = dateConvertFormtoDB($request->date);
            $file = $request->file('select_file');
            Excel::import(new ApprovedOvertimeImport($request->all()), $file);
        } catch (\Maatwebsite\Excel\Validators\ValidationException $e) {
            $import = new ApprovedOvertimeImport();
            $import->import($file);

            foreach ($import->failures() as $failure) {
                $failure->row(); // row that went wrong
                $failure->attribute(); // either heading key (if using heading row concern) or column index
                $failure->errors(); // Actual error messages from Laravel validator
                $failure->values(); // The values of the row that has failed.
            }
        }
        return back()->with('success', 'Approve Overtime information imported successfully.');
    }
}
