<?php

namespace App\Console\Commands;

use App\Models\MailConfig;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\Log;

class UpdateApp extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'site:update';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Update Sites';

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

            Artisan::call('migrate', ['--force' => true]);

            $mailConfig = MailConfig::firstOrNew();
            $mailConfig->save();

        } catch (\Exception $e) {
            
            Log::info($e->getMesssage());
        }
    }
}
