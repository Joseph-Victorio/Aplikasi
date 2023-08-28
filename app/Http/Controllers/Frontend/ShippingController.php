<?php

namespace App\Http\Controllers\Frontend;

use Exception;
use App\Models\Config;
use App\Models\Address;
use App\Models\Subdistrict;
use App\Helpers\ApiResponse;
use Illuminate\Http\Request;
use Illuminate\Routing\Controller;
use Illuminate\Support\Facades\Cache;
use Silehage\Rajaongkir\Facades\Rajaongkir;

class ShippingController extends Controller
{
    public function __construct()
    {
        $config = Config::select('rajaongkir_apikey', 'rajaongkir_type')->first();

        Rajaongkir::setApikey($config->rajaongkir_apikey, $config->rajaongkir_type);
    }

    public function searchAddress($key)
    {
        $key = filter_var($key, FILTER_SANITIZE_SPECIAL_CHARS);

        $data = Address::where('subdistrict_name', 'like', '%' . $key . '%')
            ->get();

        return ApiResponse::success($data);
    }

    public function getCost(Request $request)
    {
        $payload = $request->validate([
            "origin"        => 'required',
            "destination"   => 'required',
            "weight"        => 'required',
            "courier"       => 'required',
        ]);

        $key = http_build_query($payload);

        try {

            $data = null;

            if (Cache::has($key)) {

                $data =  Cache::get($key);
            } else {

                $json = Rajaongkir::cost($request->all());

                $obj = json_decode($json);

                if (isset($obj->success) && $obj->success == true && count($obj->data) > 0) {

                    Cache::put($key, $obj, now()->addHours(8));

                    $data = $obj;
                } else {

                    throw new Exception($obj->message);
                }
            }

            return ApiResponse::success($data);
        } catch (Exception $e) {

            return ApiResponse::failed($e);
        }
    }
    public function waybill(Request $request)
    {
        $payload = $request->validate([
            'courier' => 'required',
            'waybill' => 'required',
        ]);

        $key = http_build_query($payload);

        try {

            $data = null;

            if (Cache::has($key)) {

                $data =  Cache::get($key);
            } else {

                $json = Rajaongkir::waybill($payload);

                $obj = json_decode($json);

                if (isset($obj->success) && $obj->success == true && count($obj->data) > 0) {

                    Cache::put($key, $obj->data, now()->addHours(8));

                    $data = $obj;
                } else {

                    throw new Exception($obj->message);
                }
            }

            return ApiResponse::success($data);
        } catch (Exception $e) {

            return ApiResponse::failed($e);
        }
    }
}
