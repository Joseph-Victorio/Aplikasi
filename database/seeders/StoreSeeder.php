<?php

namespace Database\Seeders;

use Illuminate\Support\Str;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Log;

class StoreSeeder extends Seeder
{
   /**
    * Run the database seeds.
    *
    * @return void
    */
   public function run($shopName = 'Administrator', $shopPhone = '083842587851')
   {
      Log::debug($shopName);
      Log::debug($shopPhone);
      \App\Models\Store::create([
         'name' => $shopName,
         'slug' => Str::slug($shopName),
         'phone' =>  $shopPhone,
         'slogan' => 'The next generation online shop apps',
      ]);

      \App\Models\Config::create([
         'rajaongkir_apikey' => 'e7495ac700145ab33b3af06bb159ac83'
      ]);
   }
}
