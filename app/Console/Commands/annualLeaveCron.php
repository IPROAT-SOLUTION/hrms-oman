<?php

namespace App\Console\Commands;

use App\Model\Employee;
use Carbon\Carbon;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class annualLeaveCron extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'annual';

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
     * @return mixed
     */
    public function handle()
    {
        try {
            DB::beginTransaction();
            Log::info("Cron is working fine!");
            // $date = date('Y-m-01');

            if (date('Y-m-d') == date('Y-m-01') && (date('Y-m-d') != date('Y-01-01'))) {
                $sixMonthsAgo = Carbon::now()->subMonths(6);
                $employeeList = Employee::whereDate('date_of_joining', '<=', $sixMonthsAgo)
                    ->where('status', 1)
                    ->get();
                foreach ($employeeList as $employee) {
                    // $leave_credit = 2.5;
                    $leave = $employee->annual_leave + 2.5;
                    $update = Employee::where('employee_id', $employee->employee_id)->update(['annual_leave' => $leave]);
                }
                DB::commit();
            } elseif((date('Y-m-d') == date('Y-01-01'))) {
                return true;
            }

            


            DB::commit();
        } catch (\Exception $e) {

            Log::error($e);
            DB::rollback();
        }
    }
}
