<?php

namespace App\Console\Commands;

use Carbon\CarbonPeriod;
use Illuminate\Console\Command;
use Illuminate\Database\Eloquent\Collection;
use Symfony\Component\Console\Helper\ProgressBar;
use App\Http\Controllers\Attendance\GenerateReportController;

class ReportDev extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'dev:report {fdate?} {tdate?} {--force : force generation}';
    protected $name = "dev-report";

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Run this to create attendacne report with two params. format: yyyy-mm-dd';

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
        // $this->info('Please wait.............');
        $this->info('Report Generation in Progress.............');
        // echo "\n";

        //place this before any script you want to calculate time
        // $startTime = date('Y-m-d H:i:s');
        // $this->info('Initiated at ' .  $startTime . '............');
        $time_start = microtime(true);

        $fdate = $this->argument('fdate') ?? $this->ask('Enter from date:');
        $tdate = $this->argument('tdate');



        $force = $this->option('force');

        if (validateDate($fdate, 'Y-m')) {
            $fdate = date('Y-m-d', strtotime($fdate . '-01'));
            $tdate = date('Y-m-t', strtotime($fdate));
        } else if ($fdate && !$tdate) {
            $tdate = $fdate;
        } else if (!$fdate && !$tdate) {
            $fdate = date('Y-m-d');
            $tdate =  date('Y-m-d');
        }

        if ($tdate >= date('Y-m-d')) {
            $tdate = date('Y-m-d');
        }

        $this->info('Report generating between ' . $fdate . ' and ' . $tdate . ' dates.............');

        $datePeriod = CarbonPeriod::create(dateConvertFormtoDB($fdate), dateConvertFormtoDB($tdate));

        $dateRange = new Collection;

        foreach ($datePeriod as $key => $value) {
            $dateRange->push($value->format('Y-m-d'));
            if ($value->format('Y-m-d') >= date('Y-m-d')) {
                break;
            }
        }

        if (!$force && !$this->confirm('Do you wish to continue? (yes|no)[no]', true)) {
            $this->info("Process terminated by user");

            // Display Script End time
            $time_end = microtime(true);
            // $endTime = date('Y-m-d H:i:s');
            // $this->info('Completed at ' . $endTime . '............');

            //dividing with 60 will give the execution time in minutes, otherwise seconds
            $execution_time = $time_end - $time_start;
            $this->info('Execution time = ' . $execution_time . ' Seconds' . '............');
            return;
        }

        $now = date('Y-m-d H:i:s');
        // Set a new progress bar placeholder definition.
        ProgressBar::setPlaceholderFormatterDefinition(
            'started',
            function (ProgressBar $bar) {
                // Use the `date` function to format the start time date.
                return date('Y-m-d H:i:s', $bar->getStartTime());
            }
        );
        // Set a new progress bar format definition.
        ProgressBar::setFormatDefinition('custom', ' %current%/%max% [%bar%] %percent:3s%% %elapsed% %message%');
        $progressBar = $this->output->createProgressBar(count($dateRange));
        $progressBar->setFormat('custom');
        $progressBar->setBarCharacter('=');
        $progressBar->setProgressCharacter('>');
        $progressBar->setEmptyBarCharacter('-');
        // $progressBar->advance(3);
        $progressBar->setMessage('Processing...' . PHP_EOL);
        $progressBar->start();

        $progressBar->setOverwrite(true);

        $dateRange->each(function ($date, $key) use ($progressBar) {
            $now = date('Y-m-d H:i:s');
            $controller = new GenerateReportController();
            $response = $controller->generateAttendanceReport($date);
            $progressBar->setMessage('Processing...' . PHP_EOL);
            $progressBar->advance();
        });

        $progressBar->setMessage('Finished!' . PHP_EOL);
        $progressBar->finish();

        // Display Script End time
        $time_end = microtime(true);
        // $endTime = date('Y-m-d H:i:s');
        // $this->info('End Time = ' . $endTime);

        //dividing with 60 will give the execution time in minutes, otherwise seconds
        $execution_time = $time_end - $time_start;
        $this->info('Execution time = ' . $execution_time . ' Seconds');
    }
}
