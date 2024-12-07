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
   protected $signature = 'app:install-with-demo 
                           {--shop_name=Nextshop}
                           {--shop_phone=083842587851}
                           {--admin_name=Admin}
                           {--admin_email=admin@example.com}
                           {--admin_password=admin123}';

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

         $shop_name = $this->option('shop_name');
         $shop_phone = $this->option('shop_phone');
         $admin_name = $this->option('admin_name');
         $admin_email = $this->option('admin_email');
         $admin_password = $this->option('admin_password');

         User::find(1)->update([
            'name' => $admin_name,
            'email' => $admin_email,
            'password' => Hash::make($admin_password)
         ]);

         Store::find(1)->update([
            'name' => $shop_name,
            'phone' => $shop_phone
         ]);
         Artisan::call('optimize:clear');
      } catch (\Throwable $th) {

         $this->info($th->getMessage());
         Log::info($th->getMessage());
      }
   }
}
