<?php

namespace App\Http\Controllers\Leave;

use Carbon\Carbon;
use App\Model\Employee;
use App\Components\Common;
use Illuminate\Http\Request;
use App\Model\LeavePermission;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Auth;
use App\Repositories\LeaveRepository;
use App\Repositories\CommonRepository;
use App\Http\Requests\ApplyForPermissionRequest;
use App\Lib\Enumerations\LeaveStatus;

class ApplyForPermissionController extends Controller
{
    protected $commonRepository;
    protected $leaveRepository;

    public function __construct(CommonRepository $commonRepository, LeaveRepository $leaveRepository)
    {
        $this->commonRepository = $commonRepository;
        $this->leaveRepository  = $leaveRepository;
    }

    public function index()
    {
        $results = LeavePermission::with(['employee', 'approveBy'])
            ->where('employee_id', decrypt(session('logged_session_data.employee_id')))
            ->orderBy('leave_permission_date', 'desc')
            ->paginate(10);

        return view('admin.leave.applyForPermission.index', ['results' => $results]);
    }

    public function create()
    {
        $getEmployeeInfo = $this->commonRepository->employeeInfo();

        $Year  = Carbon::now()->year;
        $Month = DATE('m');

        $results = Employee::where('status', 1)->where('employee_id', decrypt(session('logged_session_data.employee_id')))->first();

        $takenpermissions = LeavePermission::whereMonth('leave_permission_date', '=', $Month)->whereYear('leave_permission_date', '=', $Year)
            ->where('employee_id', $results->employee_id)
            ->where('status', 1)->count();

        $appliedpermissions = LeavePermission::whereMonth('leave_permission_date', '=', $Month)->whereYear('leave_permission_date', '=', $Year)
            ->where('employee_id', $results->employee_id)->count();

        return view('admin.leave.applyForPermission.leave_permission_form', [
            'getEmployeeInfo' => $getEmployeeInfo,
            'takenPermissions' => $takenpermissions, 'appliedpermissions' => $appliedpermissions
        ]);
    }

    public function applyForTotalNumberOfPermissions(Request $request)
    {

        $permission_date = dateConvertFormtoDB($request->permission_date);
        $employee_id = $request->employee_id;
        $Year  = date("Y", strtotime($permission_date));
        $Month = (int)date("m", strtotime($permission_date));

        $takenpermissions = LeavePermission::whereMonth('leave_permission_date', '=', $Month)->whereYear('leave_permission_date', '=', $Year)
            ->where('employee_id', $request->employee_id)
            ->where('status', LeaveStatus::$PENDING)
            ->count();

        $appliedpermissions = LeavePermission::whereMonth('leave_permission_date', '=', $Month)
            ->whereYear('leave_permission_date', '=', $Year)
            ->where('employee_id', $request->employee_id)
            ->count();

        $checkpermissions = LeavePermission::whereMonth('leave_permission_date', '=', $Month)
            ->whereYear('leave_permission_date', '=', $Year)
            ->where('employee_id', $employee_id)
            ->where('status', '!=', LeaveStatus::$REJECT)
            ->count();

        return response()->json([
            'status' => true,
            'takenpermissions' => $takenpermissions,
            'appliedpermissions' => $appliedpermissions,
            'checkpermissions' => $checkpermissions,
        ]);
    }

    public function store(ApplyForPermissionRequest $request)
    {

        $input                             = $request->all();
        $input['leave_permission_date']    = dateConvertFormtoDB($request->permission_date);
        $input['permission_duration']      = $request->permission_duration;
        $input['leave_permission_purpose'] = $request->purpose;
        $input['from_time']                = $request->from_time;
        $input['to_time']                  = $request->to_time;
        $input['created_at']               = date('Y-m-d H:i:s');
        $input['updated_at']               = date('Y-m-d H:i:s');
        $input['status'] = 1;

        $if_exists = LeavePermission::where('employee_id', $request->employee_id)
            // ->where('status', '!=', LeaveStatus::$REJECT)
            ->where('leave_permission_date', dateConvertFormtoDB($request->permission_date))->first();

        if ($if_exists) {
            return redirect('applyForPermission')->with('error', 'Duplicate request detected for the same date. Please check previous submissions.');
        }

        $emp = Employee::find($request->employee_id);
        $operationManager = Employee::where('employee_id', $emp->operation_manager_id)->first();

        if ($operationManager && $operationManager->email) {
            $maildata = Common::mail('emails/mail', $operationManager->email, 'Permission Request Notification', ['head_name' => $operationManager->first_name . ' ' . $operationManager->last_name, 'request_info' => $emp->first_name . ' ' . $emp->last_name . ' have requested for Permission (for ' . $request->purpose . ')Application Date ' . ' ' . dateConvertFormtoDB($request->permission_date) . ' fromTime ' . ' ' . $request->from_time . ' toTime ' . $request->to_time, 'status_info' => '']);
        }

        try {
            LeavePermission::create($input);
            $bug = 0;
        } catch (\Exception $e) {
            $bug = 1;
        }

        if ($bug == 0) {
            return redirect('applyForPermission')->with('success', 'Permission Request successfully sent.');
        } else {
            return redirect('applyForPermission')->with('error', 'Something error found !, Please try again.');
        }
    }

    public function permissionrequest()
    {
        $permissionresults = LeavePermission::where('status', 1)->paginate(10);

        return view('admin.leave.applyForPermission.permission_requests', ['permissionresults' => $permissionresults]);
    }
}
