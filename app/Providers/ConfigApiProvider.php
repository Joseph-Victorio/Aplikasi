<?php

namespace App\Providers;

use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Config;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\ServiceProvider;

class ConfigApiProvider extends ServiceProvider
{
    /**
     * Register services.
     *
     * @return void
     */
    public function register()
    {

        if (Schema::hasTable('configs')) {

            $setting = DB::table('configs')->first();

            if ($setting) //checking if table is not empty
            {
    
                if($setting->telegram_bot_token && $setting->telegram_user_id) {
                    Config::set('services.telegram-bot-api', ['token' =>$setting->telegram_bot_token]);
                    Config::set('telegram', ['user_id' =>$setting->telegram_user_id]);
                }
                
                
                if($setting->tripay_api_key && $setting->tripay_private_key && $setting->tripay_merchant_code ) {
                    
                    Config::set('tripay', [
                        'api_key' => $setting->tripay_api_key,
                        'private_key' => $setting->tripay_private_key,
                        'mode' => $setting->tripay_mode,
                        'merchant_code' => $setting->tripay_merchant_code,
                        'api_sanbox_url' => env('TRIPAY_API_SANBOX_URL', 'https://tripay.co.id/api-sandbox/'),
                        'api_url' => env('TRIPAY_API_URL', 'https://tripay.co.id/api/')
                    ]);
                }
            }
    
            Config::set('demo',[
                'is_demo' => env('IS_DEMO_MODE', false),
                'phone' => env('DEMO_PHONE', '')
            ]);
        }

    }

    /**
     * Bootstrap services.
     *
     * @return void
     */
    public function boot()
    {
        //
    }
}
