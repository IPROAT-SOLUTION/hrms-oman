<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Carbon\Carbon;

class DatabaseBackUp extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'database:backup';

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
        info('db backup');

        $filename = "backup-" . Carbon::now()->format('Y-m-dHmi') . ".sql";
        $path = "C:/xampp7.4/mysql/bin/mysqldump";
        $username = config()->get('database.connections.mysql.username'); //env('DB_USERNAME');
        $password = config()->get('database.connections.mysql.password'); //env('DB_PASSWORD');
        $host = config()->get('database.connections.mysql.host'); //env('DB_HOST');
        $port = config()->get('database.connections.mysql.port'); //env('DB_PORT');
        $database = config()->get('database.connections.mysql.database'); //env('DB_DATABASE');
        $command = $path . " --user=" . $username . " --password=" . $password . " --host=" . $host . " --port=" . $port . " " . $database . "  > " . storage_path() . "/app/backup/" . $filename;

        $returnVar = NULL;
        $output  = NULL;

        exec($command, $output, $returnVar);

        return 0;
    }
}
