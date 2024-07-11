<?php

namespace App\Http\Controllers\Leave;

use App\Model\Employee;
use App\Components\Common;
use Illuminate\Http\Request;
use App\Model\EmpLeaveBalance;
use App\Model\LeaveApplication;
use Illuminate\Support\Facades\DB;
use App\Http\Controllers\Controller;
use App\Repositories\LeaveRepository;

class RequestedApplicationController extends Controller
{

    protected $leaveRepository;

    public function __construct(LeaveRepository $leaveRepository)
    {
        $this->leaveRepository = $leaveRepository;
    }

    public function index()
    {
        $results  =  LeaveApplication::with('employee', 'leaveType')->orderBy('leave_application_id', 'desc')->get();
        $employee_id =   decrypt(session('logged_session_data.employee_id'));
        $results = $results->filter(function ($q) use ($employee_id) {
            if (decrypt(session('logged_session_data.role_id')) == 1) {
                return $q;
            } else {
                if ($q->employee->operation_manager_id == decrypt(session('logged_session_data.employee_id'))) {
                    return $q;
                } else  if ($q->employee->supervisor_id == decrypt(session('logged_session_data.employee_id'))) {
                    return $q;
                }
            }
        })->values();

        $results = $results->transform(function ($q) use ($employee_id) {
            if ($q->employee->operation_manager_id == $employee_id && $q->manager_status == 1) {
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


        return view('admin.leave.leaveApplication.leaveApplicationList', ['adminresults' => $results]);
    }

    public function viewDetails($id)
    {
        // dd($id);
        $leaveApplicationData = LeaveApplication::with(['employee' => function ($q) {
            $q->with(['designation']);
        }])->with('leaveType')->where('leave_application_id', $id)->where('status', 1)->first();

        if (!$leaveApplicationData) {
            return response()->view('errors.404', [], 404);
        }


        $leaveBalanceArr = $this->leaveRepository->calculateEmployeeLeaveBalanceArray($leaveApplicationData->leave_type_id, $leaveApplicationData->employee_id);

        return view('admin.leave.leaveApplication.leaveDetails', ['leaveApplicationData' => $leaveApplicationData, 'leaveBalanceArr' => $leaveBalanceArr]);
    }
    public function viewManagerDetails($id)
    {
        $leaveApplicationData = LeaveApplication::with(['employee' => function ($q) {
            $q->with(['designation']);
        }])->with('leaveType')->where('leave_application_id', $id)->where('status', 1)->first();

        if (!$leaveApplicationData) {
            return response()->view('errors.404', [], 404);
        }


        $leaveBalanceArr = $this->leaveRepository->calculateEmployeeLeaveBalanceArray($leaveApplicationData->leave_type_id, $leaveApplicationData->employee_id);

        return view('admin.leave.leaveApplication.leaveManagerDetails', ['leaveApplicationData' => $leaveApplicationData, 'leaveBalanceArr' => $leaveBalanceArr]);
    }

    public function update(Request $request, $id)
    {
        // dd($request->all());
        $data = LeaveApplication::findOrFail($id);
        $input = $request->all();
        if ($request->status == 2) {
            $input['approve_date'] = date('Y-m-d');
            $input['approve_by'] = decrypt(session('logged_session_data.employee_id'));
        } else {
            $input['reject_date'] = date('Y-m-d');
            $input['reject_by'] = decrypt(session('logged_session_data.employee_id'));
        }

        try {
            $data->update($input);
            $bug = 0;
        } catch (\Exception $e) {
            $bug = 1;
        }
        if ($bug == 0) {
            if ($request->status == 2) {
                return redirect('requestedApplication')->with('success', 'Leave application approved successfully. ');
            } else {
                return redirect('requestedApplication')->with('success', 'Leave application reject successfully. ');
            }
        } else {
            return redirect()->back()->with('error', 'Something Error Found !, Please try again.');
        }
    }

    public function approveOrRejectLeaveApplication(Request $request, $id)
    {
        try {
            DB::beginTransaction();

            $data = LeaveApplication::findOrFail($id);
            $input = $request->all();

            if ($request->status == 2) {
                $input['approve_date'] = date('Y-m-d');
                $input['approve_by'] = decrypt(session('logged_session_data.employee_id'));
            } else {
                $input['status'] = 3;
                $input['reject_date'] = date('Y-m-d');
                $input['reject_by'] = decrypt(session('logged_session_data.employee_id'));
            }

            $data->update($input);

            DB::commit();

            if ($request->status == 2) {
                return redirect('requestedApplication')->with('success', 'Leave application approved successfully. ');
            } else {
                return redirect('requestedApplication')->with('success', 'Leave application reject successfully. ');
            }
        } catch (\Exception $e) {
            info($e);
            DB::rollback();
            return redirect()->back()->with('error', 'Something Error Found !, Please try again.');
        }
    }
    public function approveOrRejectManagerLeaveApplication(Request $request, $id)
    {
        $data = LeaveApplication::findOrFail($id);
        $input = [];

        if ($request->status == 2) {
            $input['manager_status'] = 2;
            $input['manager_remarks'] = $request->remarks;
        } else {
            $input['manager_status'] = 3;
            $input['status'] = 3;
            $input['manager_remarks'] = $request->remarks;
            $input['reject_date'] = date('Y-m-d');
            $input['reject_by'] = decrypt(session('logged_session_data.employee_id'));
        }

        try {
            $data->update($input);
            if ($request->status == 2) {
                $employee = Employee::where('employee_id', $data->employee_id)->first();
                $second_level = Employee::where('employee_id', $employee->supervisor_id)->first();

                if ($second_level && $second_level->email) {
                    $maildata = Common::mail('emails/mail', $second_level->email, 'Leave Request Notification', ['head_name' => $second_level->first_name . ' ' . $second_level->last_name, 'request_info' => $employee->first_name . ' ' . $employee->last_name . ', have requested for Leave (Purpose: ' . $data->purpose . ') from ' . ' ' . dateConvertFormtoDB($data->application_from_date) . ' to ' . dateConvertFormtoDB($data->application_to_date), 'status_info' => '']);
                }

                return redirect('requestedApplication')->with('success', 'Leave application approved successfully. ');
            } else {
                return redirect('requestedApplication')->with('success', 'Leave application reject successfully. ');
            }
        } catch (\Exception $e) {
            return redirect()->back()->with('error', 'Something Error Found !, Please try again.');
        }
    }

    public function ajaxapproveOrRejectLeaveApplication(Request $request)
    {
        // info($request->all());
        DB::beginTransaction();
        $data = LeaveApplication::findOrFail($request->leave_application_id);
        $input = $request->all();

        if ($request->status == 2) {
            $input['approve_date'] = date('Y-m-d');
            $input['approve_by'] = decrypt(session('logged_session_data.employee_id'));
        } else {
            $input['status'] = 3;
            $input['reject_date'] = date('Y-m-d');
            $input['reject_by'] = decrypt(session('logged_session_data.employee_id'));
        }

        if ($request->status == 2) {
            $latest_leave = EmpLeaveBalance::where('employee_id', $data->employee_id)->where('leave_type_id', $data->leave_type_id)->first();
            $leave = $latest_leave->leave_balance - $data->number_of_day;
            $employee = EmpLeaveBalance::where('employee_id', $data->employee_id)->where('leave_type_id', $data->leave_type_id)->update(['leave_balance' => $leave]);
        }

        try {
            $data->update($input);
            $bug = 0;
            DB::commit();
        } catch (\Exception $e) {
            $bug = 1;
            DB::rollback();
        }
        if ($bug == 0) {
            if ($request->status == 2) {
                echo "approve";
            } else {
                echo "reject";
            }
        } else {
            echo "error";
        }
    }
    public function ajaxapproveOrRejectManagerLeaveApplication(Request $request)
    {
        $data = LeaveApplication::findOrFail($request->leave_application_id);
        $input = $request->all();
        $input = [];

        if ($request->status == 2) {
            $input['manager_status'] = 2;
            $input['manager_remarks'] = $request->remarks;
        } else {
            $input['manager_status'] = 3;
            $input['status'] = 3;
            $input['manager_remarks'] = $request->remarks;
            $input['reject_date'] = date('Y-m-d');
            $input['reject_by'] = decrypt(session('logged_session_data.employee_id'));
        }

        try {
            $data->update($input);
            $bug = 0;
        } catch (\Exception $e) {
            $bug = 1;
        }

        if ($bug == 0) {
            if ($request->status == 2) {
                $data = LeaveApplication::findOrFail($request->leave_application_id);
                $employee = Employee::where('employee_id', $data->employee_id)->first();
                $hod = Employee::where('employee_id', $employee->supervisor_id)->first();
                if ($hod != '') {
                    if ($hod->email) {
                        $maildata = Common::mail('emails/mail', $hod->email, 'Leave Request Notification', ['head_name' => $hod->first_name . ' ' . $hod->last_name, 'request_info' => $employee->first_name . ' ' . $employee->last_name . ', have requested for Leave (Purpose: ' . $data->purpose . ') from ' . ' ' . dateConvertFormtoDB($data->application_from_date) . ' to ' . dateConvertFormtoDB($data->application_to_date), 'status_info' => '']);
                    }
                }
                echo "approve";
            } else {
                echo "reject";
            }
        } else {
            echo "error";
        }
    }
}
