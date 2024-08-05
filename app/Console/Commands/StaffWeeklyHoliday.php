<?php

namespace App\Console\Commands;

use Carbon\Carbon;
use Carbon\CarbonPeriod;
use App\Model\WeeklyHoliday;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;

class StaffWeeklyHoliday extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'generate:weeklyholiday {month?}';

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

    public $month;

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

        // place this before any script you want to calculate time
        $time_start = microtime(true);

        $this->month = $this->argument('month') ?? $this->ask('Enter weekly holiday month yyyy-mm');
        if ($this->month) {
            month_validation:
            $month = validateDate($this->month, 'Y-m');
            if (!$month) {
                $this->error('Month field is invalid.');
                $this->error('Month field format should be yyyy-mm.');
                $this->month = $this->argument('month') ?? $this->ask('Enter weekly holiday month yyyy-mm');
                goto month_validation;
            }
        }
        $this->info($this->month);

        // Create a Carbon instance with the starting date
        $startDate = Carbon::parse($this->month . '-01');
        // Add 3 months to the starting date
        $endDate = $startDate->addMonths(3);

        // Subtract 1 day from the end date
        $endDate->subDay();

        // $dateRange = dateRange('2024-02-01', date('Y-m-t',strtotime($endDate)));
        $this->info('Weekly holiday generation in progress.');
        $this->info('Friday and saturday assumed as weekly holidays.');
        $startDate = Carbon::parse($this->month . '-01');
        $endDate = Carbon::parse($endDate);
        $fulldateRange = CarbonPeriod::create($startDate, '1 month', $endDate);
        foreach ($fulldateRange as $key => $datecarbon) {
            $weekoff_days = [];

            $dateRange = dateRange(date('Y-m-01', strtotime($datecarbon)), date('Y-m-t', strtotime($datecarbon)));
            $weekends = array_filter($dateRange, function ($date) {
                $day = $date->format("N");
                return $day == '5' || $day == '6';
            });

            foreach ($weekends as $date) {
                array_push($weekoff_days, $date->format("Y-m-d"));
            }

            $employee = DB::table('employee')->where(function ($q) {
                $q->where('status', 1);
            })->get();
            foreach ($employee as $key => $value) {
                WeeklyHoliday::updateOrCreate(
                    ['employee_id' => $value->employee_id, 'month' =>  date('Y-m', strtotime($datecarbon))],
                    ['weekoff_days' => json_encode($weekoff_days), 'day_name' => 'Friday,Saturday']
                );
            }
        }
        $this->info('Weekly holiday generated successfully.');
        $this->info(implode(',', $weekoff_days));

        $time_end = microtime(true);

        // dividing with 60 will give the execution time in minutes, otherwise seconds
        $execution_time = $time_end - $time_start;
        $this->info('Execution time = ' . $execution_time . ' Seconds');
        return 1;
    }
}
