<?php

namespace App\Http\Controllers\Employee;

use App\User;
use DateTime;
use Carbon\Carbon;
use App\Model\Role;
use App\Model\Branch;
use App\Model\Device;
use App\Model\Employee;
use App\Model\PayGrade;
use App\Model\LeaveType;
use App\Model\WorkShift;
use App\Model\Department;
use App\Components\Common;
use App\Model\Designation;
use App\Model\HourlySalary;
use App\Model\AccessControl;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use App\Model\EmpLeaveBalance;
use App\Model\EmployeeExperience;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\URL;
use App\Http\Controllers\Controller;
use App\Lib\Enumerations\UserStatus;
use Illuminate\Support\Facades\Hash;
use Maatwebsite\Excel\Facades\Excel;
use App\Exports\EmployeeDetailsExport;
use App\Http\Requests\EmployeeRequest;
use Illuminate\Support\Facades\Storage;
use App\Repositories\EmployeeRepository;
use App\Model\EmployeeEducationQualification;

class EmployeeController extends Controller
{

    protected
        $employeeRepositories;

    public function __construct(EmployeeRepository $employeeRepositories)
    {
        $this->employeeRepositories = $employeeRepositories;
    }

    public function index(Request $request)
    {
        $departmentList = Department::get();
        $designationList = Designation::get();
        $roleList = Role::get();
        $list_of_employee = Employee::count();

        $results = Employee::with(['userName' => function ($q) {
            $q->with('role');
        }, 'department', 'designation', 'branch', 'payGrade', 'supervisor', 'hourlySalaries'])
            ->orderBy('employee_id', 'DESC')->paginate($list_of_employee);
        // ->orderBy('employee_id', 'DESC')->paginate(100);
        // $results = [];
        if (request()->ajax()) {
            if ($request->role_id != '') {
                $results = Employee::whereHas('userName', function ($q) use ($request) {
                    $q->with('role')->where('role_id', $request->role_id);
                })->with('department', 'designation', 'branch', 'payGrade', 'supervisor', 'hourlySalaries')->orderBy('employee_id', 'DESC');
            } else {
                $results = Employee::with(['userName' => function ($q) {
                    $q->with('role');
                }, 'department', 'designation', 'branch', 'payGrade', 'supervisor', 'hourlySalaries'])->orderBy('employee_id', 'DESC');
            }

            if ($request->department_id != '') {
                $results->where('department_id', $request->department_id);
            }

            if ($request->designation_id != '') {
                $results->where('designation_id', $request->designation_id);
            }

            if ($request->employee_name != '') {
                $results->where(function ($query) use ($request) {
                    $query->where('first_name', 'like', '%' . $request->employee_name . '%')
                        ->orWhere('last_name', 'like', '%' . $request->employee_name . '%');
                });
            }

            $results = $results->paginate($list_of_employee);
            // $results = $results->paginate(10);
            return View('admin.employee.employee.pagination', ['results' => $results])->render();
        }

        return view('admin.employee.employee.index', ['results' => $results, 'departmentList' => $departmentList, 'designationList' => $designationList, 'roleList' => $roleList]);
    }

    public function create()
    {
        $userList = User::where('status', 1)->get();
        $roleList = Role::get();
        $departmentList = Department::get();
        $designationList = Designation::get();
        $branchList = Branch::get();
        $workShiftList = WorkShift::get();
        $supervisorList = Employee::with('user')
            ->whereHas('user', function ($query) {
                $query->whereIn('role_id', [1, 2]);
            })
            ->where('status', 1)
            ->get();

        $operationManagerList = Employee::with('user')
            ->whereHas('user', function ($query) {
                $query->whereIn('role_id', [3, 2]);
            })
            ->where('status', 1)
            ->get();

        $payGradeList = PayGrade::all();
        $hourlyPayGradeList = HourlySalary::all();
        $incentive = $this->employeeRepositories->incentive();
        $salaryLimit = $this->employeeRepositories->salaryLimit();
        $workShift = $this->employeeRepositories->workShift();
        $nationality = $this->employeeRepositories->nationality();
        // $country = $this->employeeRepositories->country();
        $religion = $this->employeeRepositories->religion();
        // $workHours = $this->employeeRepositories->workHours();
        $data = [
            'userList' => $userList,
            'roleList' => $roleList,
            'departmentList' => $departmentList,
            'designationList' => $designationList,
            'branchList' => $branchList,
            'supervisorList' => $supervisorList,
            'operationManagerList' => $operationManagerList,
            'workShiftList' => $workShiftList,
            'payGradeList' => $payGradeList,
            'hourlyPayGradeList' => $hourlyPayGradeList,
            'incentive' => $incentive,
            'salaryLimit' => $salaryLimit,
            'workShift' => $workShift,
            'nationality' => $nationality,
            'religion' => $religion,
        ];

        return view('admin.employee.employee.addEmployee', $data);
    }

    public function store(EmployeeRequest $request)
    {
        //   dd($request->all());
        $photo = $request->file('photo');
        $document = $request->file('document_file');
        $document2 = $request->file('document_file2');
        $document3 = $request->file('document_file3');
        $document4 = $request->file('document_file4');
        $document5 = $request->file('document_file5');
        $document8 = $request->file('document_file8');
        $document9 = $request->file('document_file9');
        $document10 = $request->file('document_file10');
        $document11 = $request->file('document_file11');
        $document16 = $request->file('document_file16');
        $document17 = $request->file('document_file17');
        $document18 = $request->file('document_file18');
        $document19 = $request->file('document_file19');
        $document20 = $request->file('document_file20');
        $document21 = $request->file('document_file21');

        if ($photo) {
            $imgName = md5(str_random(30) . time() . '_' . $request->file('photo')) . '.' . $request->file('photo')->getClientOriginalExtension();
            $request->file('photo')->move('uploads/employeePhoto/', $imgName);
            $employeePhoto['photo'] = $imgName;
        }
        if ($document) {
            $document_name = date('Y_m_d_H_i_s') . '_' . $request->file('document_file')->getClientOriginalName();
            $request->file('document_file')->move('uploads/employeeDocuments/', $document_name);
            $employeeDocument['document_file'] = $document_name;
        }
        if ($document2) {
            $document_name2 = date('Y_m_d_H_i_s') . '_' . $request->file('document_file2')->getClientOriginalName();
            $request->file('document_file2')->move('uploads/employeeDocuments/', $document_name2);
            $employeeDocument['document_file2'] = $document_name2;
        }
        if ($document3) {
            $document_name3 = date('Y_m_d_H_i_s') . '_' . $request->file('document_file3')->getClientOriginalName();
            $request->file('document_file3')->move('uploads/employeeDocuments/', $document_name3);
            $employeeDocument['document_file3'] = $document_name3;
        }
        if ($document4) {
            $document_name4 = date('Y_m_d_H_i_s') . '_' . $request->file('document_file4')->getClientOriginalName();
            $request->file('document_file4')->move('uploads/employeeDocuments/', $document_name4);
            $employeeDocument['document_file4'] = $document_name4;
        }
        if ($document5) {
            $document_name5 = date('Y_m_d_H_i_s') . '_' . $request->file('document_file5')->getClientOriginalName();
            $request->file('document_file5')->move('uploads/employeeDocuments/', $document_name5);
            $employeeDocument['document_file5'] = $document_name5;
        }
        if ($document8) {
            $document_name8 = date('Y_m_d_H_i_s') . '_' . $request->file('document_file8')->getClientOriginalName();
            $request->file('document_file8')->move('uploads/employeeDocuments/', $document_name8);
            $employeeDocument['document_file8'] = $document_name8;
        }
        if ($document9) {
            $document_name9 = date('Y_m_d_H_i_s') . '_' . $request->file('document_file9')->getClientOriginalName();
            $request->file('document_file9')->move('uploads/employeeDocuments/', $document_name9);
            $employeeDocument['document_file9'] = $document_name9;
        }
        if ($document10) {
            $document_name10 = date('Y_m_d_H_i_s') . '_' . $request->file('document_file10')->getClientOriginalName();
            $request->file('document_file10')->move('uploads/employeeDocuments/', $document_name10);
            $employeeDocument['document_file10'] = $document_name10;
        }
        if ($document11) {
            $document_name11 = date('Y_m_d_H_i_s') . '_' . $request->file('document_file11')->getClientOriginalName();
            $request->file('document_file11')->move('uploads/employeeDocuments/', $document_name11);
            $employeeDocument['document_file11'] = $document_name11;
        }
        if ($document16) {
            $document_name16 = date('Y_m_d_H_i_s') . '_' . $request->file('document_file16')->getClientOriginalName();
            $request->file('document_file16')->move('uploads/employeeDocuments/', $document_name16);
            $employeeDocument['document_file16'] = $document_name16;
        }

        if ($document17) {
            $document_name17 = date('Y_m_d_H_i_s') . '_' . $request->file('document_file17')->getClientOriginalName();
            $request->file('document_file17')->move('uploads/employeeDocuments/', $document_name17);
            $employeeDocument['document_file17'] = $document_name17;
        }

        if ($document18) {
            $document_name18 = date('Y_m_d_H_i_s') . '_' . $request->file('document_file18')->getClientOriginalName();
            $request->file('document_file18')->move('uploads/employeeDocuments/', $document_name18);
            $employeeDocument['document_file18'] = $document_name18;
        }

        if ($document19) {
            $document_name19 = date('Y_m_d_H_i_s') . '_' . $request->file('document_file19')->getClientOriginalName();
            $request->file('document_file19')->move('uploads/employeeDocuments/', $document_name19);
            $employeeDocument['document_file19'] = $document_name19;
        }

        if ($document20) {
            $document_name20 = date('Y_m_d_H_i_s') . '_' . $request->file('document_file20')->getClientOriginalName();
            $request->file('document_file20')->move('uploads/employeeDocuments/', $document_name20);
            $employeeDocument['document_file20'] = $document_name20;
        }

        if ($document21) {
            $document_name21 = date('Y_m_d_H_i_s') . '_' . $request->file('document_file21')->getClientOriginalName();
            $request->file('document_file21')->move('uploads/employeeDocuments/', $document_name21);
            $employeeDocument['document_file21'] = $document_name21;
        }

        $employeeDataFormat = $this->employeeRepositories->makeEmployeePersonalInformationDataFormat($request->all());
        if (isset($employeePhoto)) {
            $employeeData = $employeeDataFormat + $employeePhoto;
        } else {
            $employeeData = $employeeDataFormat;
        }
        try {
            DB::beginTransaction();

            $employeeAccountDataFormat = $this->employeeRepositories->makeEmployeeAccountDataFormat($request->all());
            $parentData = User::create($employeeAccountDataFormat);

            $employeeData['user_id'] = $parentData->user_id;
            $employeeData['status_remark'] = $request->status_remark;
            $childData = Employee::create($employeeData);
            $employeeList = LeaveType::where('status', 1)->get();
            foreach ($employeeList as $type) {
                if ($type->leave_type_id != 7) {
                    $empLeave = EmpLeaveBalance::create([
                        'employee_id' => $childData->employee_id,
                        'finger_id' => $childData->finger_id,
                        'barnch_id' => $childData->barnch_id,
                        'department_id' => $childData->department_id,
                        'designation_id' => $childData->designation_id,
                        'leave_balance' => $type->num_of_day,
                        'leave_type_id' => $type->leave_type_id,
                    ]);
                } else {
                    $empLeave = EmpLeaveBalance::create([
                        'employee_id' => $childData->employee_id,
                        'finger_id' => $childData->finger_id,
                        'barnch_id' => $childData->barnch_id,
                        'department_id' => $childData->department_id,
                        'designation_id' => $childData->designation_id,
                        'leave_balance' => 0,
                        'leave_type_id' => $type->leave_type_id,
                    ]);
                }
            }
            Employee::where('employee_id', $childData->employee_id)->update(['device_employee_id' => $childData->finger_id]);
            User::where('user_id', $parentData->user_id)->update(['device_employee_id' => $childData->finger_id]);

            DB::commit();
            $bug = 0;
        } catch (\Exception $e) {
            return $e;
            DB::rollback();
            $bug = 1;
        }

        if ($bug == 0) {
            return redirect('employee')->with('success', 'Employee information successfully saved.');
        } else {
            return redirect('employee')->with('error', 'Something Error Found !, Please try again.');
        }
    }

    public function edit($id)
    {
        $userList = User::where('status', 1)->get();
        $roleList = Role::get();
        $departmentList = Department::get();
        $designationList = Designation::get();
        $branchList = Branch::get();
        $supervisorList = Employee::with('user')
            ->whereHas('user', function ($query) {
                $query->whereIn('role_id', [1, 2]);
            })
            ->where('status', 1)
            ->get();

        $operationManagerList = Employee::with('user')
            ->whereHas('user', function ($query) {
                $query->whereIn('role_id', [3, 2]);
            })
            ->where('status', 1)
            ->get();
        $editModeData = Employee::findOrFail($id);
        $workShiftList = WorkShift::get();
        $payGradeList = PayGrade::all();
        $hourlyPayGradeList = HourlySalary::all();
        $incentive = $this->employeeRepositories->incentive();
        $salaryLimit = $this->employeeRepositories->salaryLimit();
        $workShift = $this->employeeRepositories->workShift();
        $nationality = $this->employeeRepositories->nationality();
        $religion = $this->employeeRepositories->religion();

        $employeeAccountEditModeData = User::where('user_id', $editModeData->user_id)->first();
        $educationQualificationEditModeData = EmployeeEducationQualification::where('employee_id', $id)->get();
        $experienceEditModeData = EmployeeExperience::where('employee_id', $id)->get();
        // dd($operationManagerList);
        $data = [
            'userList' => $userList,
            'roleList' => $roleList,
            'departmentList' => $departmentList,
            'designationList' => $designationList,
            'branchList' => $branchList,
            'supervisorList' => $supervisorList,
            'operationManagerList' => $operationManagerList,
            'workShiftList' => $workShiftList,
            'payGradeList' => $payGradeList,
            'editModeData' => $editModeData,
            'hourlyPayGradeList' => $hourlyPayGradeList,
            'employeeAccountEditModeData' => $employeeAccountEditModeData,
            'educationQualificationEditModeData' => $educationQualificationEditModeData,
            'experienceEditModeData' => $experienceEditModeData,
            'incentive' => $incentive,
            'salaryLimit' => $salaryLimit,
            'workShift' => $workShift,
            'nationality' => $nationality,
            'religion' => $religion,

        ];

        return view('admin.employee.employee.editEmployee', $data);
    }

    public function update(EmployeeRequest $request, $id)
    {
        // dd($request->all());


        $document8 = $request->file('document_file8');
        $document9 = $request->file('document_file9');
        $document10 = $request->file('document_file10');
        $document11 = $request->file('document_file11');
        $document16 = $request->file('document_file16');
        $document17 = $request->file('document_file17');
        $document18 = $request->file('document_file18');
        $document19 = $request->file('document_file19');
        $document20 = $request->file('document_file20');
        $document21 = $request->file('document_file21');
        $employee = Employee::findOrFail($id);

        $photo = $request->file('photo');

        $imgName = $employee->photo;

        if ($photo) {
            echo 'photo';
            $imgName = md5(str_random(30) . time() . '_' . $request->file('photo')) . '.' . $request->file('photo')->getClientOriginalExtension();
            $request->file('photo')->move('uploads/employeePhoto/', $imgName);
            if (file_exists('uploads/employeePhoto/' . $employee->photo) and !empty($employee->photo)) {
                unlink('uploads/employeePhoto/' . $employee->photo);
                $employee->update(['photo' => null]);
            }
            $employeePhoto['photo'] = $imgName;
        }


        if ($document8) {
            $document_name8 = date('Y_m_d_H_i_s') . '_' . $request->file('document_file8')->getClientOriginalName();
            $request->file('document_file8')->move('uploads/employeeDocuments/', $document_name8);
            $employeeDocument['document_file8'] = $document_name8;
        }
        if ($document9) {
            $document_name9 = date('Y_m_d_H_i_s') . '_' . $request->file('document_file9')->getClientOriginalName();
            $request->file('document_file9')->move('uploads/employeeDocuments/', $document_name9);
            $employeeDocument['document_file9'] = $document_name9;
        }
        if ($document10) {
            $document_name10 = date('Y_m_d_H_i_s') . '_' . $request->file('document_file10')->getClientOriginalName();
            $request->file('document_file10')->move('uploads/employeeDocuments/', $document_name10);
            $employeeDocument['document_file10'] = $document_name10;
        }
        if ($document11) {
            $document_name11 = date('Y_m_d_H_i_s') . '_' . $request->file('document_file11')->getClientOriginalName();
            $request->file('document_file11')->move('uploads/employeeDocuments/', $document_name11);
            $employeeDocument['document_file11'] = $document_name11;
        }
        if ($document16) {
            $document_name16 = date('Y_m_d_H_i_s') . '_' . $request->file('document_file16')->getClientOriginalName();
            $request->file('document_file16')->move('uploads/employeeDocuments/', $document_name16);
            $employeeDocument['document_file16'] = $document_name16;
        }

        if ($document17) {
            $document_name17 = date('Y_m_d_H_i_s') . '_' . $request->file('document_file17')->getClientOriginalName();
            $request->file('document_file17')->move('uploads/employeeDocuments/', $document_name17);
            $employeeDocument['document_file17'] = $document_name17;
        }

        if ($document18) {
            $document_name18 = date('Y_m_d_H_i_s') . '_' . $request->file('document_file18')->getClientOriginalName();
            $request->file('document_file18')->move('uploads/employeeDocuments/', $document_name18);
            $employeeDocument['document_file18'] = $document_name18;
        }

        if ($document19) {
            $document_name19 = date('Y_m_d_H_i_s') . '_' . $request->file('document_file19')->getClientOriginalName();
            $request->file('document_file19')->move('uploads/employeeDocuments/', $document_name19);
            $employeeDocument['document_file19'] = $document_name19;
        }

        if ($document20) {
            $document_name20 = date('Y_m_d_H_i_s') . '_' . $request->file('document_file20')->getClientOriginalName();
            $request->file('document_file20')->move('uploads/employeeDocuments/', $document_name20);
            $employeeDocument['document_file20'] = $document_name20;
        }

        if ($document21) {
            $document_name21 = date('Y_m_d_H_i_s') . '_' . $request->file('document_file21')->getClientOriginalName();
            $request->file('document_file21')->move('uploads/employeeDocuments/', $document_name21);
            $employeeDocument['document_file21'] = $document_name21;
        }
        $employeeDataFormat = $this->employeeRepositories->makeEmployeePersonalInformationDataFormat($request->all());
        if (isset($employeePhoto)) {
            $employeeData = $employeeDataFormat + $employeePhoto;
        } else {
            $employeeData = $employeeDataFormat;
        }

        try {
            DB::beginTransaction();
            $employeeAccountDataFormat = $this->employeeRepositories->makeEmployeeAccountDataFormat($request->all(), 'update');

            User::where('user_id', $employee->user_id)->update($employeeAccountDataFormat);
            // Update Personal Information
            $employeeData['status_remark'] = $request->status_remark;
            // dd($employeeData);
            $employee->update($employeeData);
            $employee->save();

            Employee::where('employee_id', $employee->employee_id)->WhereNull('device_employee_id')->update(['device_employee_id' => $employee->finger_id]);
            User::where('user_id', $employee->user_id)->WhereNull('device_employee_id')->update(['device_employee_id' => $employee->finger_id]);

            DB::commit();
            $bug = 0;
            return redirect()->back()->with('success', 'Employee information successfully updated.');
        } catch (\Exception $e) {
            DB::rollback();
            $bug = 1;
            $bug = $e->getMessage();
            return redirect()->back()->with('error', 'Something Error Found !, Please try again.' . $bug);
        }
    }

    public function show($id)
    {

        $employeeInfo = Employee::with('department', 'designation', 'branch', 'supervisor', 'role')->where('employee.employee_id', $id)->first();
        $employeeExperience = EmployeeExperience::where('employee_id', $id)->get();
        $employeeEducation = EmployeeEducationQualification::where('employee_id', $id)->get();
        $employeeConDevice = AccessControl::where('employee', $id)->groupBy('device')->get();

        return view('admin.user.user.profile', ['employeeInfo' => $employeeInfo, 'employeeExperience' => $employeeExperience, 'employeeEducation' => $employeeEducation, 'employeeConDevice' => $employeeConDevice]);
    }

    public function destroy($id)
    {
        try {

            DB::beginTransaction();
            $data = Employee::FindOrFail($id);
            $user_data = User::FindOrFail($data->user_id);
            $user_data->delete();



            if (!is_null($data->photo)) {
                if (file_exists('uploads/employeePhoto/' . $data->photo) and !empty($data->photo)) {
                    unlink('uploads/employeePhoto/' . $data->photo);
                }
            }
            $result = $data->delete();
            if ($result) {


                DB::table('user')->where('user_id', $data->user_id)->delete();
                DB::table('weekly_holiday')->where('employee_id', $data->employee_id)->delete();
                DB::table('leave_application')->where('employee_id', $data->employee_id)->delete();
                DB::table('leave_permission')->where('employee_id', $data->employee_id)->delete();
                DB::table('salary_details')->where('employee_id', $data->employee_id)->delete();
                DB::table('emp_leave_balances')->where('employee_id', $data->employee_id)->delete();
                DB::table('employee_increments')->where('employee_id', $data->employee_id)->delete();
                DB::table('advance_deduction')->where('employee_id', $data->employee_id)->delete();
                DB::table('employee_shift')->where('finger_print_id', $data->finger_id)->delete();
            }
            DB::commit();
            $bug = 0;
        } catch (\Exception $e) {
            return $e;
            DB::rollback();
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

    public function bonusdays($employee_id)
    {

        $employees = Employee::where("created_at", ">=", Carbon::now()->subYears(2))->where('status', 1)->get();

        $dataFormat = [];
        $tempArray = [];
        foreach ($employees as $employee) {
            $tempArray['date_of_joining'] = $employee->date_of_joining;
            $tempArray['date_of_leaving'] = $employee->date_of_leaving;
            $tempArray['employee_id'] = $employee->employee_id;
            $tempArray['designation_id'] = $employee->designation_id;
            $tempArray['first_name'] = $employee->first_name;
            $tempArray['last_name'] = $employee->last_name;
            $tempArray['employee_name'] = $employee->first_name . " " . $employee->last_name;
            $tempArray['phone'] = $employee->phone;
            $tempArray['finger_id'] = $employee->finger_id;
            $tempArray['department_id'] = $employee->department_id;
            // $tempArray['country'] = $employee->country;

            $date_of_joining = new DateTime($employee->date_of_joining);

            $dataFormat[$employee->employee_id][] = $tempArray;
        }
        return $dataFormat;
    }

    public function employeeTemplate()
    {
        $file_name = 'templates/employee_details.xlsx';
        $file = Storage::disk('public')->get($file_name);
        return (new Response($file, 200))
            ->header('Content-Type', 'application/vnd.openxmlformats-officedocument.spreadsheetml.sheet');
    }

    public function t_usr(Request $request)
    {
        \set_time_limit(0);
        try {
            $users = DB::connection('sqlsrv')->table('Employees')->join('Departments', 'Departments.DepartmentId', '=', 'Employees.DepartmentId')
                ->where('Employees.EmployeeName', 'NOT LIKE', '%del%')->orderBy('Employees.EmployeeName')->get();
            $date = Carbon::now()->subDay(0)->format('Y-m-d');

            $tempArrayUser = [];
            $tempArrayEmployee = [];
            $totalDatasUser = [];
            $totalDatasEmployee = [];

            if ($request->action == 'truncate') {
                DB::table('user')->truncate();
                DB::table('employee')->truncate();

                DB::table('user')->insert([
                    'user_name' => 'admin',
                    'role_id' => 1,
                    'password' => Hash::make('123'),
                    'created_at' => date('Y-m-d H:i:s'),
                    'updated_at' => date('Y-m-d H:i:s'),
                ]);

                DB::table('employee')->insert([
                    'user_id' => 1,
                    'finger_id' => '1001',
                    'first_name' => 'admin',
                    'created_at' => date('Y-m-d H:i:s'),
                    'updated_at' => date('Y-m-d H:i:s'),
                ]);
            }

            foreach ($users as $key => $employee) {

                $if_employee_exists = DB::table('employee')->where('finger_id', $employee->EmployeeCode)->first();

                if (!$if_employee_exists) {
                    $tempArrayEmployee['finger_id'] = $employee->EmployeeCode;
                    $tempArrayEmployee['first_name'] = $employee->EmployeeName;
                    $tempArrayUser['user_name'] = $employee->EmployeeName;
                    $tempArrayUser['role_id'] = 3;
                    $totalDatasUser[] = $tempArrayUser;
                    $totalDatasEmployee[] = $tempArrayEmployee;

                    $user_id = DB::table('user')->insertGetID([
                        'user_name' => $employee->EmployeeName,
                        'role_id' => 3,
                        'created_at' => date('Y-m-d H:i:s'),
                        'updated_at' => date('Y-m-d H:i:s'),
                    ]);

                    $employee_id = DB::table('employee')->insertGetID([
                        'user_id' => $user_id,
                        'finger_id' => $employee->EmployeeCode,
                        'department_id' => $employee->DepartmentId,
                        'first_name' => $employee->EmployeeName,
                        'created_at' => date('Y-m-d H:i:s'),
                        'updated_at' => date('Y-m-d H:i:s'),
                    ]);

                    $this->pushEmployeeLive([
                        'user_id' => $user_id,
                        'employee_id' => $employee_id,
                        'role_id' => 3,
                        'user_name' => $employee->EmployeeName,
                        'password' => 'demo1234',
                        'status' => 1,
                        'finger_id' => $employee->EmployeeCode,
                        'department_id' => $employee->DepartmentId,
                        'first_name' => $employee->EmployeeName,
                        'created_at' => date('Y-m-d H:i:s'),
                        'updated_at' => date('Y-m-d H:i:s'),
                    ]);
                }
            }



            return redirect('employee')->with('success', 'Employee information sync successfully.');
        } catch (\Throwable $th) {
            // dd($th);
            return redirect('employee')->with('error', 'Something went wrong!');
            //throw $th;
        }

        return redirect('employee')->with('success', 'Employee information sync successfully.');
    }

    public function pushEmployeeLive($form_data)
    {

        $data_set = [];
        foreach ($form_data as $key => $value) {
            if ($value) {
                $data_set[$key] = $value;
            } else {
                $data_set[$key] = '';
            }
        }
        Log::info($data_set);
        $client = new \GuzzleHttp\Client(['verify' => false]);
        $response = $client->request('POST', Common::liveurl() . "addEmployee", [
            'form_params' => $data_set,
        ]);
    }

    public function export($dataSet = [], $fileName = null)
    {

        if (empty($dataSet)) {

            $employees = Employee::where('status', UserStatus::$ACTIVE)->with('department', 'branch', 'designation', 'workshift', 'userName', 'supervisor')->get();

            $extraData = [];
            $inc = 1;
            $supervisor_name = null;

            foreach ($employees as $key => $Data) {
                $user = User::find($Data->user_id);
                $role = Role::find($user->role_id);

                if (isset($Data->supervisor_id)) {
                    $supervisor = Employee::find($Data->supervisor_id);
                    $sup = User::find($supervisor->user_id);
                    $supervisor_name = $sup->user_name ?? '';
                }
                if (isset($Data->operation_manager_id)) {
                    $manager = Employee::find($Data->operation_manager_id);
                    $mana = User::find($manager->user_id);
                    $manager_name = $mana->user_name ?? '';
                }

                if ($Data->status == 1) {
                    $user_status = 'Active';
                }
                if ($Data->status == 0) {
                    $user_status = 'In-Active';
                }
                if ($Data->status == 2) {
                    $user_status = 'Terminated';
                }
                if ($Data->status == 3) {
                    $user_status = 'Resigned';
                }

                $dataSet[] = [
                    $inc,
                    $Data->userName->user_name ?? '',
                    $role->role_name ?? '',
                    $Data->finger_id ?? '',
                    $Data->department->department_name ?? '',
                    $Data->designation->designation_name ?? '',
                    $Data->branch->branch_name ?? '',
                    $supervisor_name ?? '',
                    $manager_name ?? '',
                    (string) $Data->phone ?? '',
                    $Data->email ?? '',
                    $Data->first_name ?? '',
                    $Data->last_name ?? '',
                    $Data->date_of_birth ?? '',
                    $Data->date_of_joining ?? '',
                    $Data->gender == 0 ? 'Male' : 'Female',
                    $Data->marital_status ?? '',
                    $Data->address ?? '',
                    (string)$Data->emergency_contacts,
                    $Data->religion == 0 ? 'Muslim' : 'Non-Muslim',
                    $Data->nationality == 0 ? 'Omanis' : 'Expats',
                    $Data->employee_category == 0 ? 'Employee' : 'Chiefs',
                    $Data->country ?? '',
                    $Data->document_title8 ?? '',
                    $Data->expiry_date8 ?? '',
                    $Data->document_title9 ?? '',
                    $Data->expiry_date9 ?? '',
                    $Data->document_title10 ?? '',
                    $Data->expiry_date10 ?? '',
                    $Data->document_title11 ?? '',
                    $Data->expiry_date11 ?? '',
                    $Data->account_number ?? '',
                    $Data->ifsc_number ?? '',
                    $Data->name_of_the_bank ?? '',
                    $Data->account_holder ?? '',
                    $Data->basic_salary ?? '',
                    $Data->increment ?? '',
                    $Data->housing_allowance ?? '',
                    $Data->utility_allowance ?? '',
                    $Data->transport_allowance ?? '',
                    $Data->living_allowance ?? '',
                    $Data->mobile_allowance ?? '',
                    $Data->special_allowance ?? '',
                    $Data->prem_others ?? '',
                    $Data->education_and_club_allowance ?? '',
                    $Data->membership_allowance ?? '',
                    $Data->date_of_leaving ?? '',
                    $user_status ?? '',
                    $Data->status_remark ?? '',
                    $Data->ip_attendance == 1 ? 'Yes' : 'No',
                    $Data->mobile_attendance == 1 ? 'Yes' : 'No',
                ];
                $inc++;
            }
        }


        $heading = [
            [
                'Sl.No',
                'User Name',
                'Role Name',
                'Employee Id',
                'Department',
                'Designation',
                'Branch',
                'HR',
                'Manager',
                'Phone',
                'Email',
                'First Name',
                'Last Name',
                'Date of Birth',
                'Date of Joining',
                'Gender',
                'Marital Status',
                'Address',
                'Emergency Contact',
                'Religion',
                'Nationality',
                'Employee Category',
                'Country',
                'Passport Number',
                'Passport Expiry Date',
                'Visa Number',
                'Visa Expiry Date',
                'Driving Licence Number',
                'Driving Licence Expiry Date',
                'Civil Id Number',
                'Civil Id Expiry Date',
                'Account Number',
                'Swift Code',
                'Name of the Bank',
                'Account Holder',
                'Basic Salary',
                'Annual Increment',
                'Housing Allowance',
                'Utility Allowance',
                'Transport Allowance',
                'Living Allowance',
                'Mobile Allowance',
                'Special Allowance',
                'Premium and Others',
                'Education and Club',
                'Membership allowances',
                'Date of Leaving',
                'Status',
                'Status Remark',
                'Ip Attendance',
                'Mobile Attendance',
            ],
        ];

        $extraData['heading'] = $heading; // dd($dataSet);

        $filename = $fileName ?? 'EmployeeInformation-' . DATE('dmYHis') . '.xlsx';

        return Excel::download(new EmployeeDetailsExport($dataSet, $extraData), $filename);
    }

    public function exportAsTemplate()
    {
        $dataSet = $emptyArr = [];
        for ($i = 0; $i < 49; $i++) {
            $emptyArr[] = '';
        }
        $dataSet[] = $emptyArr; // dd($dataSet);
        $filename = 'EmployeeTemplate-' . DATE('dmYHis') . '.xlsx';
        return $this->export($dataSet, $filename);
    }
}
