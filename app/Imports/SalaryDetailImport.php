<?php

namespace App\Imports;

use Carbon\Carbon;
use App\Model\Employee;
use App\Model\SalaryDetails;
use App\Model\ApproveOverTime;
use App\Model\AdvanceDeduction;
use App\Model\LeaveApplication;
use App\Model\EmployeeIncrement;
use App\Model\AdvanceDeductionLog;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Auth;
use App\Lib\Enumerations\LeaveStatus;
use Illuminate\Support\Facades\Validator;
use App\Model\AdvanceDeductionTransaction;
use Maatwebsite\Excel\Concerns\Importable;
use Maatwebsite\Excel\Concerns\ToCollection;
use Maatwebsite\Excel\Concerns\WithStartRow;
use Maatwebsite\Excel\Concerns\WithBatchInserts;
use Maatwebsite\Excel\Concerns\WithChunkReading;
use App\Http\Controllers\Payroll\GenerateSalarySheet;

class SalaryDetailImport implements ToCollection, WithChunkReading, WithStartRow, WithBatchInserts
{

    use Importable;

    /**
     * @param Collection $collection
     */
    protected $generateSalarySheet;

    public function __construct(GenerateSalarySheet $generateSalarySheet)
    {
        $this->generateSalarySheet = $generateSalarySheet;
    }

    public function collection(Collection $collection)
    {

        foreach ($collection as $key => $value) {

            // Each Row Validation
            $index = $key + 1;
            Validator::make($value->toArray(), [
                "0" => 'required',
                "1" => 'required',
                "2" => 'required|exists:employee,finger_id',
                "3" => 'required',
                "4" => 'required',
                "5" => 'required',
                "6" => 'required',
                "7" => 'required',
                "8" => 'required',
                "9" => 'nullable|numeric',
                "10" => 'nullable|numeric',
                "11" => 'nullable|numeric',
            ], [
                "0.required"   => "at row $index - SL.No field is required",
                "1.required"   => "at row $index - Month field is required",
                "2.required"   => "at row $index - Employee id field is required",
                "2.exists"     => "at row $index - Employee Id does not Exist",
                "3.required"   => "at row $index - Name field is required",
                "4.required"   => "at row $index - Department field is required",
                "5.required"   => "at row $index - Designation field is required",
                "6.required"   => "at row $index - Date Of Joining field is required",
                "7.required"   => "at row $index - Nationality field is required",
                "8.required"   => "at row $index - Location/Branch field is required",
                "9.required"   => "at row $index - Arrears allowance field is required",
                "10.require"   => "at row $index - Paycut field is required",
                "11.require"   => "at row $index - Gsm field is required",
                "9.numeric"    => "at row $index - Arrears allowance field must be numeric",
                "10.numeric"   => "at row $index - Paycut field must be numeric",
                "11.numeric"   => "at row $index - Gsm field must be numeric",
            ])->validate();

            $employeeDetails = Employee::with('department', 'designation')->where('finger_id', $value[2])->first();

            $allowance =  $deduction = [];
            $from_date = $value[1] . "-01";
            $to_date = date('Y-m-t', strtotime($from_date));

            $arrears = [
                'arrears_adjustment' => $value[9] ?? 0
            ];

            $deductionData = [
                'pay_cut' => $value[10] ?? 0,
                'gsm' => $value[11] ?? 0,
            ];

            $attendanceInfo = $this->generateSalarySheet->getEmployeeOtmAbsLvLtAndWokDays($value[1], $employeeDetails);

            $allowance = $this->generateSalarySheet->calculateEmployeeAllowance($employeeDetails, $attendanceInfo, $value[1]);

            $socialSecurity = $this->generateSalarySheet->calculateSocialSecurity($employeeDetails, $allowance, $value[1]);

            $deduction = $this->generateSalarySheet->calculateEmployeeDeduction($employeeDetails, $attendanceInfo, $value[1], $deductionData);

            $deduction['social_security'] = $socialSecurity['social_security'];

            $increment =  $allowance['increment'];
            unset($allowance['basic']);
            unset($allowance['increment']);
            unset($allowance['increment_amount']);

            $approve_over_time_amount = ApproveOverTime::whereBetween('date', [$from_date, $to_date])->where('finger_print_id', $employeeDetails->finger_id)->sum('over_time_amount');
            $allowance['extra_amount'] = $approve_over_time_amount ?? 0;
            $salaryData['employee_id'] = $employeeDetails->employee_id;
            $salaryData['employer_contribution'] = $socialSecurity['employer_contribution'] ?? 0;

            $salaryData['month_of_salary'] = $value[1];
            $salaryData['branch_id'] = $employeeDetails->branch_id;
            $salaryData['gross_salary'] = array_sum($allowance);
            $salaryData['total_allowances'] = array_sum($allowance);
            $salaryData['total_deductions'] = array_sum($deduction);
            $salaryData['arrears_adjustment'] = array_sum($arrears);
            $salaryData['net_salary'] = array_sum($allowance) - array_sum($deduction) + array_sum($arrears);

            $lop_days = $attendanceInfo['total_absence'];
            $deductionData['lop'] = $value[11] ??  $salaryData['gross_salary'] / 30 * $lop_days;

            $salaryReport = array_merge($allowance, $deduction, $attendanceInfo, $salaryData, $arrears);

            // update basic into employee table if increment is avaliable
            $increment = EmployeeIncrement::where('employee_id', $salaryData['employee_id'])->where('year', date('Y', strtotime($salaryData['month_of_salary'])))->first();
            if ($increment) {
                Employee::find($salaryData['employee_id'])->update(['basic_salary' => $increment->basic_amount + $increment->increment_amount]);
            }
            $advanceDeductions = AdvanceDeduction::where('advance_deduction.employee_id',  $salaryData['employee_id'])->where('status', 0)->where('payment_type', 0)->orderByDesc('advance_deduction_id')->get();

            $parentData = SalaryDetails::where('month_of_salary', $salaryData['month_of_salary'])->where('employee_id', $salaryData['employee_id'])->first();
            if (!$parentData) {
                foreach ($advanceDeductions as $advance) {
                    $log =  AdvanceDeductionLog::create([
                        'employee_id'                => $salaryData['employee_id'],
                        'advance_deduction_id'       => $advance->advance_deduction_id,
                        'advance_amount'             => $advance->advance_amount,
                        'advancededuction_name'      => $advance->advancededuction_name,
                        'date_of_advance_given'      => dateConvertFormtoDB($advance->date_of_advance_given),
                        'deduction_amouth_per_month' => $advance->deduction_amouth_per_month,
                        'payment_type'               => $advance->payment_type,
                        'no_of_month_to_be_deducted' => $advance->no_of_month_to_be_deducted,
                        'remaining_month'            => $advance->no_of_month_to_be_deducted,
                        'reason'                     => $advance->deduction_amouth_per_month . " " . "Advance Deduction Amount Debited By Payroll",
                        'created_by'                 => Auth::user()->user_name,
                    ]);
                    AdvanceDeductionTransaction::create([
                        'advance_deduction_log_id'  => $log->advance_deduction_log_id,
                        'advance_deduction_id'      => $advance->advance_deduction_id,
                        'employee_id'               => $advance->employee_id,
                        'transaction_date'          => dateConvertFormtoDB(Carbon::today()),
                        'payment_type'              => 0,
                        'cash_received'             => $advance->deduction_amouth_per_month,
                        'created_by'                 => Auth::user()->user_id,
                    ]);

                    $deduction = AdvanceDeduction::where('advance_deduction_id', $advance->advance_deduction_id)->firstOrFail();
                    $transaction = AdvanceDeductionTransaction::where('advance_deduction_id', $advance->advance_deduction_id)->sum('cash_received');
                    $update = AdvanceDeduction::where('advance_deduction_id', $advance->advance_deduction_id)->update(['paid_amount' => $transaction, 'pending_amount' => ($deduction->advance_amount - $transaction)]);
                }
            }

            SalaryDetails::updateOrCreate(['month_of_salary' => $salaryData['month_of_salary'], 'employee_id' => $salaryData['employee_id']], $salaryReport);
        }
    }

    public function batchSize(): int
    {
        return 50;
    }

    public function chunkSize(): int
    {
        return 50;
    }

    public function startRow(): int
    {
        return 2;
    }
}
