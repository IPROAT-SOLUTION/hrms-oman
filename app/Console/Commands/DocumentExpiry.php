<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\Log;
use App\Http\Controllers\Employee\DocumentExpiryController;

class DocumentExpiry extends Command
{
    protected $signature = 'document:expiry';

    protected $description = 'Document Expiry Cron';


    public function __construct()
    {
        parent::__construct();
    }


    public function handle()
    {
        Log::info("Cron is working fine!");
        $controller = new DocumentExpiryController();
        $controller->IfDocumentsExpiry();
    }
}
