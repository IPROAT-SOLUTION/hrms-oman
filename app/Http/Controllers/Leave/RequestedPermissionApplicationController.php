<?php

namespace App\Http\Controllers\Leave;

use App\Model\Employee;
use App\Components\Common;
use Illuminate\Http\Request;
use App\Model\LeavePermission;
use App\Http\Controllers\Controller;
use App\Repositories\LeaveRepository;

class RequestedPermissionApplicationController extends Controller
{

    protected $leaveRepository;

    public function __construct(LeaveRepository $leaveRepository)
    {
        $this->leaveRepository = $leaveRepository;
    }

    public function index()
    {
        $results  =  LeavePermission::with('employee')->orderBy('leave_permission_id', 'desc')->get();

        $results = $results->filter(function ($q) {
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

        $results = $results->transform(function ($q) {
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

        return view('admin.leave.permissionApplication.permissionApplicationList', ['adminResults' => $results]);
    }

    public function viewDetails($id)
    {
        $leaveApplicationData = LeavePermission::with(['employee' => function ($q) {
            $q->with(['designation']);
        }])->where('leave_permission_id', $id)->where('status', 1)->first();

        if (!$leaveApplicationData) {
            return response()->view('errors.404', [], 404);
        }

        return view('admin.leave.permissionApplication.permissionDetails', ['leaveApplicationData' => $leaveApplicationData]);
    }
    public function viewManagerDetails($id)
    {
        $leaveApplicationData = LeavePermission::with(['employee' => function ($q) {
            $q->with(['designation']);
        }])->where('leave_permission_id', $id)->where('status', 1)->first();

        if (!$leaveApplicationData) {
            return response()->view('errors.404', [], 404);
        }

        return view('admin.leave.permissionApplication.permissionManagerDetails', ['leaveApplicationData' => $leaveApplicationData]);
    }

    public function update(Request $request, $id)
    {

        $data = LeavePermission::findOrFail($id);
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
                return redirect('requestedPermissionApplication')->with('success', 'Permission application approved successfully. ');
            } else {
                return redirect('requestedPermissionApplication')->with('error', 'Permission application reject successfully. ');
            }
        } else {
            return redirect()->back()->with('error', 'Something Error Found !, Please try again.');
        }
    }

    public function ajaxapproveOrRejectPermissionApplication(Request $request)
    {
        // info($request->all());
        $data = LeavePermission::findOrFail($request->leave_permission_id);
        $input = $request->all();

        if ($request->status == 2) {
            $input['approve_date'] = date('Y-m-d');
            $input['approved_by'] = decrypt(session('logged_session_data.employee_id'));
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
                echo "approve";
            } else {
                echo "reject";
            }
        } else {
            echo "error";
        }
    }
    public function approveOrRejectPermissionApplication(Request $request, $id)
    {
        // dd($request->all());
        $data = LeavePermission::findOrFail($id);
        $input = $request->all();

        if ($request->status == 2) {
            $input['approve_date'] = date('Y-m-d');
            $input['approved_by'] = decrypt(session('logged_session_data.employee_id'));
        } else {
            $input['status'] = 3;
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
                return redirect('requestedPermissionApplication')->with('success', 'Permission application approved successfully. ');
            } else {
                return redirect('requestedPermissionApplication')->with('error', 'Permission application reject successfully. ');
            }
        } else {
            return redirect()->back()->with('error', 'Something Error Found !, Please try again.');
        }
    }
    public function ajaxapproveOrRejectManagerPermissionApplication(Request $request)
    {
        // info($request->all());
        $data = LeavePermission::findOrFail($request->leave_permission_id);
        $input = $request->all();

        $manager_status = [];

        if ($request->status == 2) {
            $manager_status['manager_status'] = 2;
            $manager_status['manager_remarks'] = $request->remarks;
        } else {
            $manager_status['manager_status'] = 3;
            $manager_status['status'] = 3;
            $manager_status['reject_date'] = date('Y-m-d');
            $manager_status['reject_by'] = decrypt(session('logged_session_data.employee_id'));
        }

        try {
            $data->update($manager_status);
            $bug = 0;
        } catch (\Exception $e) {
            $bug = 1;
        }
        if ($bug == 0) {
            if ($request->status == 2) {
                $employee = Employee::where('employee_id', $data->employee_id)->first();
                $hod = Employee::where('employee_id', $employee->supervisor_id)->first();
                if ($hod) {
                    if ($hod->email) {
                        $maildata = Common::mail('emails/mail', $hod->email, 'Permission Request Notification', ['head_name' => $hod->first_name . ' ' . $hod->last_name, 'request_info' => $employee->first_name . ' ' . $employee->last_name . ' have requested for Permission (for ' . $data->leave_permission_purpose . ')Application Date ' . ' ' . dateConvertFormtoDB($data->leave_permission_date) . ' fromTime ' . ' ' . $data->from_time . ' toTime ' . $data->to_time, 'status_info' => '']);
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
    public function approveOrRejectManagerPermissionApplication(Request $request, $id)
    {
        $data = LeavePermission::findOrFail($id);
        $input = $request->all();

        $manager_status = [];
        if ($request->status == 2) {
            $manager_status['manager_status'] = 2;
            $manager_status['manager_remarks'] = $request->remarks;
        } else {
            $manager_status['manager_status'] = 3;
            $manager_status['status'] = 3;
            $manager_status['manager_remarks'] = $request->remarks;
            $manager_status['reject_date'] = date('Y-m-d');
            $manager_status['reject_by'] = decrypt(session('logged_session_data.employee_id'));
        }

        try {
            $data->update($manager_status);
            $bug = 0;
        } catch (\Exception $e) {
            $bug = 1;
        }

        if ($bug == 0) {
            if ($request->status == 2) {
                $data = LeavePermission::findOrFail($id);
                $employee = Employee::where('employee_id', $data->employee_id)->first();
                $hod = Employee::where('employee_id', $employee->supervisor_id)->first();
                if ($hod) {
                    if ($hod->email) {
                        $maildata = Common::mail('emails/mail', $hod->email, 'Permission Request Notification', ['head_name' => $hod->first_name . ' ' . $hod->last_name, 'request_info' => $employee->first_name . ' ' . $employee->last_name . ' have requested for Permission (for ' . $data->leave_permission_purpose . ')Application Date ' . ' ' . dateConvertFormtoDB($data->leave_permission_date) . ' fromTime ' . ' ' . $data->from_time . ' toTime ' . $data->to_time, 'status_info' => '']);
                    }
                }
                return redirect('requestedPermissionApplication')->with('success', 'Permission application approved successfully. ');
            } else {
                return redirect('requestedPermissionApplication')->with('error', 'Permission application reject successfully. ');
            }
        } else {
            return redirect()->back()->with('error', 'Something Error Found !, Please try again.');
        }
    }
}
