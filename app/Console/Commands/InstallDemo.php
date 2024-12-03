<?php

namespace App\Console\Commands;

use Exception;
use App\Models\User;
use App\Models\Store;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Artisan;

class InstallDemo extends Command
{
   /**
    * The name and signature of the console command.
    *
    * @var string
    */
   protected $signature = 'app:install-demo';

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

         if (!File::exists(database_path('demo/assets')) || !File::exists(database_path('demo/database.sql'))) {
            throw new Exception('demo not found');
         }

         $this->line('Copying assets please wait...');
         File::deleteDirectory(public_path('upload/images'));
         File::copyDirectory(database_path('demo/assets'), public_path('upload/images'));

         $this->line('Migrating Database please wait...');
         Artisan::call('migrate:fresh', ['--force' => true]);

         $this->line('Import Database please wait...');
         $sql = database_path('demo/database.sql');
         DB::unprepared(file_get_contents($sql));

         User::find(1)->update([
            'email' => 'admin@example.com',
            'password' => Hash::make('admin123')
         ]);

         Store::find(1)->update([
            'name' => 'Nextshop Whatsapp',
            'phone' => '083842587851'
         ]);
         Artisan::call('optimize:clear');
      } catch (\Throwable $th) {

         $this->info($th->getMessage());
         Log::info($th->getMessage());
      }
   }
}
