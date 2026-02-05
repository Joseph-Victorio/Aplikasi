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

         if (!File::exists(database_path('demo/images')) || !File::exists(database_path('demo/content.sql'))) {
            throw new Exception('demo not found');
         }

         File::deleteDirectory(public_path('upload/images'));
         File::copyDirectory(database_path('demo/images'), public_path('upload/images'));

         $sql = database_path('demo/content.sql');
         DB::unprepared(file_get_contents($sql));
      } catch (\Throwable $th) {

         $this->info($th->getMessage());
         Log::error($th->getMessage());
      }
   }
}
