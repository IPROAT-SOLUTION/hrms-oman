<?php

namespace App\Console\Commands;

use App\Model\EmpLeaveBalance;
use Carbon\Carbon;
use App\Model\Employee;
use App\Model\LeaveApplication;
use App\Model\LeaveType;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class AnnualLeaveCreditCron extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'annual-leave';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Command description';

    /**
     * Create a new command instance.
     *
     * @return void
     */
    public function __construct()
    {
        parent::__construct();
    }

    /**
     * Execute the console command.
     *
     * @return int
     */
    public function handle()
    {
        try {
            DB::beginTransaction();
            Log::info("Cron is working fine!");
            $sixMonthsAgo = Carbon::now()->subMonths(6);
            $employeeList = Employee::whereDate('date_of_joining', '<=', $sixMonthsAgo)->get();

            if (date('Y-m-d') == date('Y-01-01')) {
                $leave_type = LeaveType::get();
                $employeeList = Employee::whereDate('date_of_joining', '<=', $sixMonthsAgo)
                    ->where('status', 1)
                    ->get();
                foreach ($employeeList as $employee) {
                    foreach ($leave_type as $type) {
                        if ($type->leave_type_id != 7) {
                            $update = EmpLeaveBalance::where('employee_id', $employee->employee_id)->where('leave_type_id', $type->leave_type_id)->update([
                                'leave_balance' => $type->num_of_day,
                            ]);
                        } else {
                            $emp_leave = EmpLeaveBalance::where('employee_id', $employee->employee_id)->where('leave_type_id', $type->leave_type_id)->first();
                            if ($emp_leave->leave_balance < 15) {
                                $update = EmpLeaveBalance::where('employee_id', $employee->employee_id)->where('leave_type_id', $type->leave_type_id)->update(['leave_balance' => (30 + $emp_leave->leave_balance)]);
                            } else {
                                $update = EmpLeaveBalance::where('employee_id', $employee->employee_id)->where('leave_type_id', $type->leave_type_id)->update(['leave_balance' => (30 + 15)]);
                            }
                        }
                    }
                }
            }
            DB::commit();
        } catch (\Exception $e) {

            Log::error($e);
            DB::rollback();
        }







        return 0;
    }
}
