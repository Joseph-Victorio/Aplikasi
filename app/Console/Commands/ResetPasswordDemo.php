<?php

namespace App\Console\Commands;

use App\Models\User;
use App\Models\Store;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Hash;

class ResetPasswordDemo extends Command
{
   /**
    * The name and signature of the console command.
    *
    * @var string
    */
   protected $signature = 'demo:reset';

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
      User::find(1)->update([
         'email' => 'admin@example.com',
         'password' => Hash::make('admin123')
      ]);

      Store::find(1)->update([
         'name' => 'Nextshop Whatsapp',
         'phone' => '083842587851'
      ]);
   }
}
