<?php

namespace App\Http\Controllers\Leave;

use App\Model\Employee;
use App\Model\LeaveType;
use App\Model\EmpLeaveBalance;
use App\Model\LeaveApplication;
use Illuminate\Support\Facades\DB;
use App\Http\Controllers\Controller;
use App\Repositories\LeaveRepository;
use App\Http\Requests\LeaveTypeRequest;
use App\Repositories\EmployeeRepository;


class LeaveTypeController extends Controller
{

    protected $employeeRepositories;
    protected $leaveRepository;

    public function __construct(EmployeeRepository $employeeRepositories, LeaveRepository $leaveRepository)
    {
        $this->employeeRepositories = $employeeRepositories;
        $this->leaveRepository = $leaveRepository;
    }
    public function index()
    {
        $results = LeaveType::where('status', 1)->OrderBy('leave_type_id', 'desc')->get();
        return view('admin.leave.leaveType.index', ['results' => $results]);
    }


    public function create()
    {
        $nationality = $this->leaveRepository->nationality();
        $religion = $this->leaveRepository->religion();
        $gender = $this->leaveRepository->gender();
        return view('admin.leave.leaveType.form', ['nationality' => $nationality, 'religion' => $religion, 'gender' => $gender]);
    }


    public function store(LeaveTypeRequest $request)
    {
        $input = $request->all();
        try {
            DB::begintransaction();
            $leave =  LeaveType::create($input);
            $employee = Employee::get();
            foreach ($employee as $emp) {




                $religionStatus = 0;
                $status = 0;
                $genderStatus = 0;
                $nationalityStatus = 0;
                //Both...
                if ($request->nationality == 2 && $request->religion == 2 && $request->gender == 2) {
                    $status = true;
                    $nationalityStatus =  $religionStatus = $genderStatus = 1;
                } elseif ($request->nationality == 2  && $request->religion != 2 && $request->gender != 2) {
                    $nationalityStatus = 1;
                    if ($request->religion != 2) {
                        if ($request->religion == $emp->religion) {
                            $religionStatus = 1;
                            $status = 1;
                        }
                    }

                    if ($request->gender != 2) {
                        if ($request->gender == $emp->gender) {
                            $status = 1;
                            $genderStatus = 1;
                        }
                    }
                    if ($religionStatus == 1 && $genderStatus == 1 && $nationalityStatus == 1) {
                        $status = 1;
                    } else {
                        $status = 0;
                    }
                } elseif ($request->religion == 2 && $request->nationality != 2 &&  $request->gender != 2) {
                    $religionStatus = 1;

                    if ($request->nationality != 2) {
                        if ($request->nationality == $emp->nationality) {
                            $nationalityStatus = 1;
                            $status = 1;
                        }
                    } else {
                        $nationalityStatus = 0;
                    }
                    if ($request->gender != 2) {
                        if ($request->gender == $emp->gender) {
                            $status = 1;
                            $genderStatus = 1;
                        }
                    } else {
                        $nationalityStatus = 0;
                    }
                    if ($religionStatus == 1 && $genderStatus == 1 && $nationalityStatus == 1) {
                        $status = 1;
                    } else {
                        $status = 0;
                    }
                } elseif ($request->gender == 2 && $request->religion != 2 && $request->nationality != 2) {

                    $genderStatus = 1;
                    if ($request->religion != 2) {
                        if ($request->religion == $emp->religion) {
                            $religionStatus = 1;
                            $status = 1;
                        }
                    }
                    if ($request->nationality != 2) {
                        if ($request->nationality == $emp->nationality) {
                            $nationalityStatus = 1;
                            $status = 1;
                        }
                    }
                    if ($religionStatus == 1 && $genderStatus == 1 && $nationalityStatus == 1) {
                        $status = 1;
                    } else {
                        $status = 0;
                    }
                } elseif ($request->nationality == 2 && $request->religion == 2 && $request->gender != 2) {

                    $nationalityStatus = $religionStatus = 1;
                    if ($request->gender == $emp->gender) {
                        $genderStatus = 1;
                    } else {
                        $genderStatus = 0;
                    }
                } elseif ($request->nationality == 2 && $request->gender == 2 && $request->religion != 2) {
                    $nationalityStatus = $genderStatus = 1;
                    if ($request->religion == $emp->religion) {
                        $religionStatus = 1;
                    } else {
                        $religionStatus = 0;
                    }
                } elseif ($request->religion == 2 && $request->gender == 2 && $request->nationality != 2) {
                    $religionStatus = $genderStatus = 1;
                    if ($request->nationality == $emp->nationality) {
                        $nationalityStatus = 1;
                    } else {
                        $nationalityStatus = 0;
                    }
                } elseif ($request->nationality != 2 && $request->religion != 2 && $request->gender != 2) {
                    if ($emp->nationality == $request->nationality) {
                        $nationalityStatus = 1;
                        $status = 1;
                    } else {
                        $nationalityStatus = 0;
                    }
                    if ($emp->religion == $request->religion) {
                        $religionStatus = 1;
                        $status = 1;
                    } else {
                        $religionStatus = 0;
                    }
                    if ($emp->gender == $request->gender) {
                        $genderStatus = 1;
                        $status = 1;
                    } else {
                        $genderStatus = 0;
                    }
                    if ($religionStatus == 1 && $genderStatus == 1 && $nationalityStatus == 1) {
                        $status = 1;
                    } else {
                        $status = 0;
                    }
                }

                if ($religionStatus == 1 && $nationalityStatus == 1 && $genderStatus == 1) {

                    $status = 1;
                } else {
                    $status = 0;
                }
                info($emp->finger_id);

                if ($status) {
                    $leave_type_id = $request->leave_type_id;
                    $employee_id = $request->employee_id;

                    $leaveBalance = EmpLeaveBalance::create([
                        'employee_id' => $emp->employee_id,
                        'finger_id' => $emp->finger_id,
                        'branch_id' => $emp->branch_id,
                        'department_id' => $emp->department_id,
                        'designation_id' => $emp->designation_id,
                        'leave_type_id' => $leave->leave_type_id,
                        'leave_balance' => $leave->num_of_day,

                    ]);
                } else {
                    $leaveBalance = EmpLeaveBalance::create([
                        'employee_id' => $emp->employee_id,
                        'finger_id' => $emp->finger_id,
                        'branch_id' => $emp->branch_id,
                        'department_id' => $emp->department_id,
                        'designation_id' => $emp->designation_id,
                        'leave_type_id' => $leave->leave_type_id,
                        'leave_balance' => 0,

                    ]);
                }



                // $leaveBalance = EmpLeaveBalance::create([
                //     'employee_id' => $emp->employee_id,
                //     'finger_id' => $emp->finger_id,
                //     'branch_id' => $emp->branch_id,
                //     'department_id' => $emp->department_id,
                //     'designation_id' => $emp->designation_id,
                //     'leave_type_id' => $leave->leave_type_id,
                //     'leave_balance' => $leave->num_of_day,

                // ]);
            }
            $bug = 0;
            DB::commit();
        } catch (\Exception $e) {
            DB::rollBack();
            $bug = 1;
        }

        if ($bug == 0) {
            return redirect('leaveType')->with('success', 'Leave Type successfully saved.');
        } else {
            return redirect('leaveType')->with('error', $e->getMessage());
        }
    }


    public function edit($id)
    {
        $editModeData = LeaveType::findOrFail($id);
        $nationality = $this->leaveRepository->nationality();
        $religion = $this->leaveRepository->religion();
        $gender = $this->leaveRepository->gender();
        return view('admin.leave.leaveType.edit', ['editModeData' => $editModeData, 'nationality' => $nationality, 'religion' => $religion, 'gender' => $gender]);
    }


    public function update(LeaveTypeRequest $request, $id)
    {
        $data   = LeaveType::findOrFail($id);
        $input  = $request->all();
        try {
            $data->update($input);
            $bug = 0;
        } catch (\Exception $e) {
            $bug = 1;
        }

        if ($bug == 0) {
            return redirect()->back()->with('success', 'Leave Type successfully updated.');
        } else {
            return redirect()->back()->with('error', 'Something Error Found !, Please try again.');
        }
    }


    public function destroy($id)
    {
        $count = LeaveApplication::where('leave_type_id', '=', $id)->count();
        if ($count > 0) {
            return "hasForeignKey";
        }
        try {
            $data = LeaveType::findOrFail($id);
            DB::beginTransaction();
            EmpLeaveBalance::where('leave_type_id', $id)->delete();
            $data->delete();
            DB::commit();
            $bug = 0;
        } catch (\Exception $e) {
            DB::rollBack();
            $bug = 1;
        }
        if ($bug == 0) {
            echo "success";
        } elseif ($bug == 1451) {
            echo 'hasForeignKey';
        } else {
            echo 'error';
        }
    }
}
