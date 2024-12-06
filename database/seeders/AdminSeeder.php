<?php

namespace Database\Seeders;

use Illuminate\Support\Str;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Hash;

class AdminSeeder extends Seeder
{
   /**
    * Run the database seeds.
    *
    * @return void
    */
   public function run($name, $email, $password)
   {
      Log::info($name);
      Log::info($email);
      Log::info($password);

      \App\Models\User::create([
         'name' => $name,
         'email' => $email,
         'email_verified_at' => now(),
         'password' => Hash::make($password),
         'remember_token' => Str::random(10),
         'role' => 'admin'
      ]);
   }
}
