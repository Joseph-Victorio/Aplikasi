<?php

namespace App\Console\Commands;

use App\Models\Category;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\Artisan;

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

            // Call Migration
            Artisan::call('migrate', ['--force' => true]);

             // Update Version 2.3.3

            Artisan::call('site:update-v-233');
            $this->info('run 233');
            
            // Update Version > 2.3.4
            
            Artisan::call('site:update-v-234');
            $this->info('run 234');
            
            // Update Version 2.4.0
            Artisan::call('site:update-v-240');
            $this->info('run 240');
            
            Artisan::call('asset:update-path');

            Artisan::call('config:change-field');

        } catch (\Exception $e) {
            
            Log::info('error site update');
        }
    }
}
