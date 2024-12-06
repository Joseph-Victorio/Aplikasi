<?php

namespace App\Console\Commands;

use Database\Seeders\AdminSeeder;
use Database\Seeders\StoreSeeder;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Artisan;

class InstallApp extends Command
{
   /**
    * The name and signature of the console command.
    *
    * @var string
    */
   protected $signature = 'app:install
                           {--shop_name=}
                           {--shop_phone=}
                           {--admin_name=}
                           {--admin_email=}
                           {--admin_password=}
                           ';

   /**
    * The console command description.
    *
    * @var string
    */
   protected $description = 'Install Allikasi';

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
   public function handle(AdminSeeder $adminSeeder, StoreSeeder $storeSeeder)
   {
      $bar = $this->output->createProgressBar(3);

      $this->line('Migrate and Seeding Database please wait...');

      Artisan::call('migrate:fresh', ['--force' => true]);

      $bar->advance();
      $this->newLine();
      Artisan::call('db:seed', ['--force' => true]);

      $bar->advance();
      $this->newLine();

      $shop_name = $this->option('shop_name');
      $shop_phone = $this->option('shop_phone');
      $admin_name = $this->option('admin_name');
      $admin_email = $this->option('admin_email');
      $admin_password = $this->option('admin_password');

      $adminSeeder->run($admin_name, $admin_email, $admin_password);
      $storeSeeder->run($shop_name, $shop_phone);

      Artisan::call('optimize:clear');

      $bar->finish();

      $this->newLine();
      $this->info('Berhasil menginstall aplikasi');
   }
}
