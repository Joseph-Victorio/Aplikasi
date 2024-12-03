<?php

namespace Database\Seeders;

use Illuminate\Support\Str;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class DatabaseSeeder extends Seeder
{
   /**
    * Seed the application's database.
    *
    * @return void
    */
   public function run()
   {

      \App\Models\User::create([
         'name' => 'admin',
         'email' => 'admin@example.com',
         'email_verified_at' => now(),
         'password' => Hash::make('password'),
         'remember_token' => Str::random(10),
         'role' => 'admin'
      ]);

      \App\Models\Store::create([
         'name' => 'My Shop',
         'slug' => 'my-shop',
         'phone' => '083842587851',
         'slogan' => 'The next generation online shop apps',
      ]);

      \App\Models\Config::create([
         'rajaongkir_apikey' => 'e7495ac700145ab33b3af06bb159ac83'
      ]);

      $this->call([
         ImportRajaongkirTableSeeder::class
      ]);
   }
}
