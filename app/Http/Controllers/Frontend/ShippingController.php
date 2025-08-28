<?php

namespace App\Http\Controllers\Frontend;

use Exception;
use App\Models\Config;
use App\Models\Address;
use App\Helpers\ApiResponse;
use Illuminate\Http\Request;
use Illuminate\Routing\Controller;
use App\Services\RajaongkirService;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Cache;
use Silehage\Rajaongkir\Facades\Rajaongkir;

class ShippingController extends Controller
{
    protected $rajaongkirService;
    protected $active_couriers;
    public function __construct(RajaongkirService $rajaongkirService)
    {
        $this->rajaongkirService = $rajaongkirService;
        $config = Config::select('rajaongkir_apikey', 'rajaongkir_type', 'rajaongkir_couriers')->first();
        $this->active_couriers = $config->rajaongkir_couriers;
    }

    public function searchAddress($key)
    {
        try {
            $key = filter_var($key, FILTER_SANITIZE_SPECIAL_CHARS);
            $cacheKey = 'serach_' . $key;

            $result = [];

            if (Cache::has($cacheKey)) {
                $result = Cache::get($cacheKey);
            } else {
                $result = $this->rajaongkirService->searchAddress($key);

                if (count($result) > 0) {
                    Cache::put($cacheKey, $result, now()->addHours(3));
                }
            }

            $data = [];

            for ($i = 0; $i < count($result); $i++) {

                $item['id'] = $result[$i]['id'];
                $item['city_id'] = $result[$i]['id'];
                $item['name'] = $result[$i]['label'];
                $item['label'] = $result[$i]['label'];
                $item['value'] = $result[$i]['id'];
                $item['subdistrict'] = $result[$i]['subdistrict_name'] . ' ' . $result[$i]['district_name'];
                $item['city'] = $result[$i]['city_name'];
                $item['postal_code'] = $result[$i]['zip_code'] ?? NULL;
                $item['country_name'] = 'Indonesia';

                $data[] = $item;
            }

            return ApiResponse::success($data);
        } catch (\Throwable $th) {
            return ApiResponse::failed('Kecamatan tidak ditemukan, silahkan input kecamatan yang sesuai');
        }
    }

    public function getCost(Request $request)
    {
        $payload = $request->validate([
            "origin"        => 'required',
            "destination"   => 'required',
            "weight"        => 'required',
        ]);


        try {

            $weightKey = intval($payload['weight']);

            if ($weightKey <= 1400) {
                $weightKey = 1;
            }
            if ($weightKey > 1400 && $weightKey <= 2400) {
                $weightKey = 2;
            }
            if ($weightKey > 2400 && $weightKey <= 3400) {
                $weightKey = 3;
            }

            $couriersArr = array_map(function ($item) {
                return $item['value'];
            }, $this->active_couriers);

            $payload['courier'] =  implode(":", $couriersArr);

            $cacheKey = $payload['origin'] . $payload['destination'] . $payload['courier'] . $weightKey;

            $result = [];

            if (Cache::has($cacheKey)) {
                $result = Cache::get($cacheKey);
            } else {
                $result = $this->rajaongkirService->getCosts($payload);

                Log::debug('costs', $result);

                if (count($result) > 0) {
                    Cache::put($cacheKey, $result, now()->addHours(6));
                }
            }

            $costs = [];

            $is_standart_package = $weightKey < 50000;

            if (count($result) > 0) {
                for ($i = 0; $i < count($result); $i++) {

                    $item['id'] = 'RJ' . $result[$i]['service'] . $result[$i]['code'];
                    $item['company'] = $result[$i]['name'];
                    $item['courier_name'] = $result[$i]['name'];
                    $item['courier_code'] = $result[$i]['code'];
                    $item['courier_service_name'] = $result[$i]['description'];
                    $item['courier_service_code'] = $result[$i]['service'];
                    $item['price'] = $result[$i]['cost'];
                    $item['duration'] = $result[$i]['etd'];
                    $item['type'] = $is_standart_package ? 'Standard' : 'Trucking';

                    $name = $result[$i]['description'];

                    if($is_standart_package) {

                        if((str_contains($name, 'Truck') || str_contains($name, 'Motor'))) {
                            Log::debug('is_standart');
                            continue;
                        }

                    }
                    
                    $costs[] = $item;

                }
            }

            return ApiResponse::success($costs);
        } catch (Exception $e) {

            return ApiResponse::failed($e->getMessage());
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
