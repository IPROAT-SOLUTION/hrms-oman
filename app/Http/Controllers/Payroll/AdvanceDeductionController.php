<?php

namespace App\Http\Controllers\Payroll;

use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Auth;
use App\Http\Requests\AdvanceDeductionRequest;
use App\Model\AdvanceDeduction;
use App\Model\AdvanceDeductionLog;
use App\Model\Employee;
use App\Model\Role;
use App\Model\User;
use App\Model\SalaryDetails;
use App\Model\AdvanceDeductionTransaction;
use App\Repositories\PayrollRepository;
use DateTime;
use Illuminate\Http\Request;

class AdvanceDeductionController extends Controller
{

    protected $payrollRepository;

    public function __construct(PayrollRepository $payrollRepository)
    {
        $this->payrollRepository = $payrollRepository;
    }

    public function index()
    {
        $results = Employee::join('advance_deduction', 'advance_deduction.employee_id', '=', 'employee.employee_id')
            ->select('advance_deduction.*', 'employee.first_name', 'employee.last_name')->get();
        return view('admin.payroll.advanceDeduction.index', ['results' => $results]);
    }

    public function create()
    {
        $results   = [];
        $employees = Employee::where('status', 1)->get();
        foreach ($employees as $employee) {
            $results[$employee->employee_id][] = $employee;
        }
        return view('admin.payroll.advanceDeduction.form', ['results' => $results]);
    }

    public function store(AdvanceDeductionRequest $request)
    {
        $input = $this->payrollRepository->makeEmployeeAdvanceDetuctionDataFormat($request->all());

        $results = AdvanceDeduction::where('advancededuction_name', $request->advancededuction_name)->first();

        // if ($results) {
        //     return redirect('advanceDeduction')->with('warning', 'Past Salary Advance Due Still Active.');
        // }
        // dd($request->all());

        try {

            $create =  AdvanceDeduction::create([
                'employee_id'                => $request->employee_id,
                'advance_amount'             => $request->advance_amount,
                'advancededuction_name'      => $request->advancededuction_name,
                'date_of_advance_given'      => dateConvertFormtoDB($request->date_of_advance_given),
                'deduction_amouth_per_month' => $request->deduction_amouth_per_month,
                'payment_type'               => $request->payment_type,
                'no_of_month_to_be_deducted' => $request->no_of_month_to_be_deducted,
                'remaining_month'            => $request->no_of_month_to_be_deducted,
                'status'            => $request->status,
                'pending_amount'             => $request->advance_amount,
                'created_by'                 => Auth::user()->user_id,
            ]);
            $add_deduction_id = $create->advance_deduction_id;
            AdvanceDeductionLog::create([
                'employee_id'                => $request->employee_id,
                'advance_deduction_id'       => $add_deduction_id,
                'advance_amount'             => $request->advance_amount,
                'advancededuction_name'      => $request->advancededuction_name,
                'date_of_advance_given'      => dateConvertFormtoDB($request->date_of_advance_given),
                'deduction_amouth_per_month' => $request->deduction_amouth_per_month,
                'payment_type'               => $request->payment_type,
                'no_of_month_to_be_deducted' => $request->no_of_month_to_be_deducted,
                'remaining_month'            => $request->no_of_month_to_be_deducted,
                'reason'                     => $request->advance_amount . " " . "Advance Deduction Created",
                'created_by'                 => Auth::user()->user_name,
            ]);

            $bug = 0;
        } catch (\Exception $e) {
            info($e);
            $bug = 1;
        }

        if ($bug == 0) {
            return redirect('advanceDeduction')->with('success', 'Advance deduction Successfully saved.');
        } else {
            return redirect('advanceDeduction')->with('error', 'Something Error Found !, Please try again.');
        }
    }

    public function edit($id)
    {

        $results   = [];
        $employees = Employee::where('status', 1)->get();
        foreach ($employees as $employee) {
            $results[$employee->employee_id][] = $employee;
        }
        $editModeData = AdvanceDeduction::findOrFail($id);
        return view('admin.payroll.advanceDeduction.form', ['editModeData' => $editModeData, 'results' => $results]);
    }

    public function update(Request $request, $id)
    {
        $data = AdvanceDeduction::FindOrFail($id);
        $input = $request->all();
        // dd($input);
        try {
            $data->update($input);
            if ($data->advance_amount == $request->advance_amount) {
                $text = "Advance Deduction Updated";
            } else {
                $text = $data->advance_amount . "->" . $request->advance_amount . " Advance Deduction Updated";
            }

            AdvanceDeductionLog::create([
                'employee_id'                => $data->employee_id,
                'advance_deduction_id'                => $id,
                'advance_amount'             => $request->advance_amount,
                'advancededuction_name'      => $data->advancededuction_name,
                'date_of_advance_given'      => dateConvertFormtoDB($data->date_of_advance_given),
                'deduction_amouth_per_month' => $request->deduction_amouth_per_month,
                'payment_type'               => $request->payment_type,
                'no_of_month_to_be_deducted' => $request->no_of_month_to_be_deducted,
                'remaining_month'            => $request->no_of_month_to_be_deducted,
                'reason'                     => $text,
                'updated_by'                 => Auth::user()->user_name,
            ]);
            $bug = 0;
        } catch (\Exception $e) {
            $bug = 1;
            info($e);
        }

        if ($bug == 0) {
            return redirect()->back()->with('success', 'Advance deduction Successfully Updated.');
        } else {
            return redirect()->back()->with('error', 'Something Error Found !, Please try again.');
        }
    }

    public function destroy($id)
    {
        try {
            $data = AdvanceDeduction::FindOrFail($id);

            $salaryDetails = SalaryDetails::where('employee_id', $data->employee_id)->where('month_of_salary', '>=', date('Y-m', strtotime($data->date_of_advance_given)))->first();

            AdvanceDeductionLog::create([
                'employee_id'                => $data->employee_id,
                'advance_deduction_id'                => $id,
                'advance_amount'             => $data->advance_amount,
                'advancededuction_name'      => $data->advancededuction_name,
                'date_of_advance_given'      => dateConvertFormtoDB($data->date_of_advance_given),
                'deduction_amouth_per_month' => $data->deduction_amouth_per_month,
                'payment_type'               => $data->payment_type,
                'no_of_month_to_be_deducted' => $data->no_of_month_to_be_deducted,
                'remaining_month'            => $data->no_of_month_to_be_deducted,
                'reason'                     => "Advance Deduction Deleted",
                'delete_by'                 => Auth::user()->user_name,
            ]);

            if ($salaryDetails && $salaryDetails->salary_advance == $data->deduction_amouth_per_month) {
                return  'hasForeignKey';
            } else {
                $data->delete();
            }


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

    public function calculateEmployeeAdvanceDeduction(Request $request)
    {
        $advanceDeductions = AdvanceDeduction::join('employee', 'employee.employee_id', '=', 'advance_deduction.employee_id')
            ->where('advance_deduction.employee_id', $request->employee_id)->select('advance_deduction.*', 'advance_deduction.created_at')->get();
        $deductionArray = [];
        $totalDeduction = 0;
        foreach ($advanceDeductions as $key => $deduction) {
            $temp                               = [];
            $temp['advance_deduction_id']       = $deduction->advance_deduction_id;
            $temp['employee_id']                = $deduction->employee_id;
            $temp['advance_amount']             = $deduction->advance_amount;
            $temp['date_of_advance_given']      = $deduction->date_of_advance_given;
            $temp['deduction_amouth_per_month'] = $deduction->deduction_amouth_per_month;
            $temp['no_of_month_to_be_deducted'] = $deduction->no_of_month_to_be_deducted;
            $temp['status']                     = $deduction->status;

            $temp['date']             = $deduction->date_of_advance_given;
            $temp['format_date']      = new DateTime($temp['date']);
            $temp['advanced_year']    = $temp['format_date']->format('y');
            $temp['advanced_month']   = $temp['format_date']->format('m');
            $temp['current_year']     = \Carbon\Carbon::today('y')->format('y');
            $temp['current_month']    = \Carbon\Carbon::today('m')->format('m');
            $temp['total_period']     = $deduction->no_of_month_to_be_deducted + $temp['advanced_month'];
            $temp['remaining_period'] = $temp['total_period'] - $temp['current_month'];

            if ($temp['remaining_period'] > 0) {
                $temp['amount_of_advance_deduction'] = $deduction->deduction_amouth_per_month;
            } else {
                $temp['amount_of_advance_deduction'] = 0;
            }
            $totalDeduction += $temp['amount_of_advance_deduction'];
            $deductionArray[$key] = $temp;
        }
        return ['deductionArray' => $deductionArray, 'totalDeduction' => $totalDeduction];
    }


    public function advance()
    {

        $results = AdvanceDeductionTransaction::with('advance', 'employee')->get();

        return view('admin.payroll.advanceDeduction.advance', ['results' => $results]);
    }

    public function advancecreate()
    {
        $employees = Employee::where('status', 1)->get();
        $results = [];
        foreach ($employees as $employee) {
            $advanceDeductions = AdvanceDeduction::where('employee_id', $employee->employee_id)
                ->where('payment_type', 1)
                ->where('status', 0)
                ->where('remaining_month', '!=', 0)
                ->get();
            if ($advanceDeductions->isNotEmpty()) {
                $results[$employee->employee_id] = $employee;
            }
        }
        // dd($results);

        return view('admin.payroll.advanceDeduction.advanceform', ['results' => $results]);
    }

    public function getAdvancedDeductions(Request $request)
    {
        $response = array();
        $selectedId = $request->input('selectedId');

        if ($selectedId !== null) {
            $data = AdvanceDeduction::where('employee_id', $selectedId)
                ->where('status', 0)
                ->where('payment_type', 1)
                ->where('remaining_month', '!=', 0)
                ->get();
            if ($data) {
                $response['status'] = true;
                $response['data'] = $data;
                $response['msg'] = 'Data found';
            } else {
                $response['status'] = false;
                $response['msg'] = 'No data found for the selected ID.';
            }
        } else {
            $response['status'] = false;
            $response['msg'] = 'Selected ID is required!';
        }
        return response()->json($response);
    }

    public function advancestore(Request $request)
    {
        // dd($request->all());
        $advance = AdvanceDeduction::where('advance_deduction_id', $request->advance_name)->first();

        // dd($request->remaining_month);
        try {
            $log = AdvanceDeductionLog::create([
                'employee_id'                => $request->emp_id,
                'advance_deduction_id'       => $advance->advance_deduction_id,
                'advance_amount'             => $request->advance_amount,
                'advancededuction_name'      => $advance->advancededuction_name,
                'date_of_advance_given'      => dateConvertFormtoDB($request->date_of_advance_given),
                'deduction_amouth_per_month' => $advance->deduction_amouth_per_month,
                'payment_type'               => 1,
                'no_of_month_to_be_deducted' => $advance->no_of_month_to_be_deducted,
                'pending_amount'             => $advance->pending_amount - $request->paid_amount,
                'paid_amount'                => $request->paid_amount,
                'remaining_month'            => $request->remaining_month,
                'reason'                     => $request->paid_amount . " " . "Advance Amount Paid",
                'created_by'                 => Auth::user()->user_name,
            ]);

            $update = AdvanceDeductionLog::where('advance_deduction_log_id', $log->advance_deduction_log_id)->decrement('remaining_month');

            $log_id = $log->advance_deduction_log_id;

            // $deduction = AdvanceDeduction::where('advance_deduction_id', $request->advance_name)->firstOrFail();

            AdvanceDeductionTransaction::create([
                'advance_deduction_log_id'  => $log_id,
                'advance_deduction_id'      => $advance->advance_deduction_id,
                'employee_id'               => $request->emp_id,
                'transaction_date'          => dateConvertFormtoDB($request->date_of_advance_given),
                'payment_type'              => 1,
                'cash_received'             => $request->paid_amount,
                'created_by'                => Auth::user()->user_id,
                'pending_amount'                => $advance->pending_amount - $request->paid_amount,
                'remaining_month'                => $request->remaining_month,
            ]);

            $this->pendingAmountCalculation($request->advance_name, $flag = 1);


            $value = AdvanceDeduction::where('advance_deduction_id', $request->advance_name)->update(['remaining_month' => $request->remaining_month]);

            $status = AdvanceDeduction::where('advance_deduction_id', $request->advance_name)->first();

            if ($status->remaining_month == 0) {
                AdvanceDeduction::where('remaining_month', 0)->update(['status' => 2]);
            }

            $status_log = AdvanceDeductionLog::where('advance_deduction_id', $request->advance_name)->first();
            if ($status_log->remaining_month == 0) {
                AdvanceDeductionLog::where('remaining_month', 0)->update(['status' => 2]);
            }

            $deduction_log = AdvanceDeductionLog::where('advance_deduction_id', $request->advance_name)->firstOrFail();
            //   dd($deduction_log->all());
            return redirect('advanceDeduction/advance')->with('success', 'Advance deduction Successfully saved.');
        } catch (\Exception $e) {
            info($e);
            return redirect('advanceDeduction/advance')->with('error', 'Something Error Found !, Please try again.');
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


    public function advanceedit($id)
    {

        $results   = [];
        $employees = Employee::where('status', 1)->get();
        foreach ($employees as $employee) {
            $results[$employee->employee_id][] = $employee;
        }
        $editModeData = AdvanceDeductionTransaction::findOrFail($id);
        $advance = AdvanceDeduction::where('advance_deduction_id', $editModeData->advance_deduction_id)->first();
        // dd($advance);

        return view('admin.payroll.advanceDeduction.advanceedit', ['editModeData' => $editModeData, 'results' => $results, 'advance' => $advance]);
    }

    public function advanceupdate(Request $request, $id)
    {

        try {
            $advance_old = AdvanceDeductionTransaction::where('advance_deduction_transaction_id', $id)->first();

            $update = AdvanceDeductionTransaction::where('advance_deduction_transaction_id', $id)->update(['cash_received' => $request->deduction_amouth_per_month]);


            $advance = AdvanceDeductionTransaction::where('advance_deduction_transaction_id', $id)->first();

            $advanceDeductions = AdvanceDeduction::where('advance_deduction_id', $advance->advance_deduction_id)->first();

            AdvanceDeductionLog::create([
                'employee_id'                => $advance->employee_id,
                'advance_deduction_id'       => $advance->advance_deduction_id,
                'advance_amount'             => $advanceDeductions->advance_amount,
                'advancededuction_name'      => $advanceDeductions->advancededuction_name,
                'date_of_advance_given'      => dateConvertFormtoDB($advanceDeductions->date_of_advance_given),
                'deduction_amouth_per_month' => $advanceDeductions->deduction_amouth_per_month,
                'payment_type'               => 1,
                'no_of_month_to_be_deducted' => $advanceDeductions->no_of_month_to_be_deducted,
                'remaining_month'            => $advanceDeductions->no_of_month_to_be_deducted,
                'paid_amount'            => $advance->cash_received,
                'pending_amount'            => $advanceDeductions->pending_amount,
                'reason'                     => $advance_old->cash_received . "=>" . $request->deduction_amouth_per_month . " " . "Advance Updated",
                'updated_by'                 => Auth::user()->user_name,
            ]);


            $this->pendingAmountCalculation($advance_old->advance_deduction_id, $flag = 2);
            $advanceDeductions = AdvanceDeduction::where('advance_deduction_id', $advance->advance_deduction_id)->first();
            AdvanceDeductionTransaction::where('advance_deduction_transaction_id', $id)->update(['pending_amount' => $advanceDeductions->pending_amount]);

            return redirect()->back()->with('success', 'Advance Successfully Updated.');
        } catch (\Exception $e) {
            info($e);
            return redirect('advanceDeduction/advance')->with('error', 'Something Error Found !, Please try again.');
        }
    }

    public function advancedestroy($id)
    {
        // info($id);
        try {
            $advance = AdvanceDeductionTransaction::where('advance_deduction_transaction_id', $id)->first();
            // info($advance->advance_deduction_transaction_id);
            $data = AdvanceDeduction::where('advance_deduction_id', $advance->advance_deduction_id)->first();

            if ($data) {

                // $data = AdvanceDeduction::where('advance_deduction_id', $advance->advance_deduction_id)->first();

                $log = AdvanceDeductionLog::create([
                    'employee_id'                => $data->employee_id,
                    'advance_deduction_id'       => $data->advance_deduction_id,
                    'advance_amount'             => $data->advance_amount,
                    'advancededuction_name'      => $data->advancededuction_name,
                    'date_of_advance_given'      => $data->date_of_advance_given,
                    'deduction_amouth_per_month' => $data->deduction_amouth_per_month,
                    'payment_type'               => $data->payment_type,
                    'no_of_month_to_be_deducted' => $data->no_of_month_to_be_deducted,
                    'remaining_month'            => $data->no_of_month_to_be_deducted,
                    'pending_amount'             => $data->pending_amount,
                    'paid_amount'                => $data->paid_amount,
                    'reason'                     => $data->deduction_amouth_per_month . " " . "Advance Deleted",
                    'deleted_by' => Auth::user()->user_name,

                ]);
            }

            $advance->delete();
            $this->pendingAmountCalculation($data->advance_deduction_id, $flag = 3);

            return "success";
        } catch (\Exception $e) {
            info($e);
            return "error";
        }
    }

    public function deleteAmountCalculation($id)
    {
        try {
            $transaction = AdvanceDeductionTransaction::where('advance_deduction_transaction_id', $id)->first();
            $deduction = AdvanceDeduction::where('advance_deduction_id', $transaction->advance_deduction_id)->first();

            if ($deduction) {
                $deduction->paid_amount -= $transaction->cash_received;
                if ($deduction->pending_amount == 0) {
                    $deduction->pending_amount = 0.000;
                } else {
                    $deduction->pending_amount -= $transaction->cash_received;
                }
                $deduction->remaining_month += 1;
                if ($deduction->status == 2) {
                    $deduction->status = 1;
                }
                $deduction->update();
            }
        } catch (\Throwable $th) {
            info($th);
            return false;
        }
    }
    public function log()
    {
        // dd("hi");  
        // $log = AdvanceDeduction::where('employee_id', $employee->employee_id)->get();
        // $results = AdvanceDeductionLog::with('advance', 'employee', 'createdByUser', 'updatedByUser')->get();
        $results = AdvanceDeductionLog::with('employee', 'updateduser', 'createduser', 'advance', 'deleted_user')->get();
        // dd($results);

        return view('admin.payroll.advanceDeduction.log', ['results' => $results]);
    }
}
