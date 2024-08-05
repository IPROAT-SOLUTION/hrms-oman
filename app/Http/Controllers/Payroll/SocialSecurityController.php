<?php

namespace App\Http\Controllers\Payroll;

use Illuminate\Http\Request;
use App\Model\SocialSecurity;
use App\Http\Controllers\Controller;
use App\Repositories\PayrollRepository;
use App\Repositories\EmployeeRepository;
use App\Http\Requests\SocialSecurityRequest;

class SocialSecurityController extends Controller
{

    protected $payrollRepository;
    protected $employeeRepository;

    public function __construct(PayrollRepository $payrollRepository, EmployeeRepository $employeeRepository)
    {
        $this->payrollRepository = $payrollRepository;
        $this->employeeRepository = $employeeRepository;
    }

    public function index()
    {
        $results = SocialSecurity::get();
        return view('admin.payroll.socialSecurity.index', ['results' => $results]);
    }

    public function create()
    {
        $nationality = $this->employeeRepository->nationality();

        return view('admin.payroll.socialSecurity.form', ['nationality' => $nationality]);
    }

    public function store(SocialSecurityRequest $request)
    {
        try {
            // if($request->)
            // $check = SocialSecurity::where('year',$request->year)->where('')

            SocialSecurity::create([
                'gross_salary'                => $request->gross_salary,
                'year'                => $request->year,
                'nationality'             => $request->nationality,
                'percentage'      => $request->percentage,
                'employer_contribution'      => $request->employer_contribution,

            ]);

            $bug = 0;
        } catch (\Exception $e) {
            $bug = 1;
        }

        if ($bug == 0) {
            return redirect('socialSecurity')->with('success', 'Social Security Successfully saved.');
        } else {
            return redirect('socialSecurity')->with('error', 'Something Error Found !, Please try again.');
        }
    }

    public function edit($id)
    {


        $editModeData = SocialSecurity::findOrFail($id);
        $nationality = $this->employeeRepository->nationality();

        return view('admin.payroll.socialSecurity.form', ['editModeData' => $editModeData,'nationality'=>$nationality]);
    }

    public function update(SocialSecurityRequest $request, $id)
    {
        $data = SocialSecurity::FindOrFail($id);
        $input = $request->all(); 
        try {
            $data->update($input);
            $bug = 0;
        } catch (\Exception $e) {
            $bug = 1;
        }

        if ($bug == 0) {
            return redirect()->back()->with('success', 'Social Security Successfully Updated.');
        } else {
            return redirect()->back()->with('error', 'Something Error Found !, Please try again.');
        }
    }

    public function destroy($id)
    {
        try {
            $data = SocialSecurity::FindOrFail($id);
            $data->delete();
            $bug = 0;
        } catch (\Exception $e) {
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
