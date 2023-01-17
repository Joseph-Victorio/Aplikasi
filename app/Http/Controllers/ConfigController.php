<?php

namespace App\Http\Controllers;

use App\Helpers\ApiResponse;
use App\Models\Config;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\Cache;

class ConfigController extends Controller
{

    public function show()
    {
        $data =  Config::first();
        return ApiResponse::success($data);
    }
    public function adminConfig()
    {
        $hiddenFields = [
            'rajaongkir_apikey',
            'tripay_api_key',
            'tripay_private_key',
            'tripay_merchant_code',
            'telegram_bot_token',
            'telegram_user_id',
        ];

        $data = Config::first()->makeVisible($hiddenFields);

        return ApiResponse::success($data);
    }

    public function update(Request $request)
    {
        $config = Config::first();

        $config->update($request->all());

        Cache::forget('shop_config');
        Cache::forget('admin_config');

        if($config->theme_color != $request->theme_color) {
            Artisan::all('generate:manifest');
        }

        return ApiResponse::success($config);
    }

}
