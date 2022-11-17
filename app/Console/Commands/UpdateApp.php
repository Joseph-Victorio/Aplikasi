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

            Artisan::call('migrate', ['--force' => true]);

            
            if (Schema::hasTable('mail_configs')) {

                $mailConfig =  \App\Models\MailConfig::firstOrNew(); 
                $mailConfig->save();  

            }
            
            $categories = Category::whereNull('updated_at')->get();

            foreach($categories as $category) {
                $category->updated_at = now();
                $category->save();
            }
                

        } catch (\Exception $e) {
            
            Log::info('error site update');
        }
    }
}
