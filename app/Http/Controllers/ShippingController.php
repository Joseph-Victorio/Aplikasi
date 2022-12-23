<?php

namespace App\Http\Controllers;

use App\Models\Config;
use App\Models\Subdistrict;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Cache;
use Silehage\Rajaongkir\Facades\Rajaongkir;

class ShippingController extends Controller
{
    public function __construct()
    {
        $config = Config::select('rajaongkir_apikey', 'rajaongkir_type')->first();

        Rajaongkir::setApikey($config->rajaongkir_apikey);
        Rajaongkir::setAccountType($config->rajaongkir_type);

    }

    public function getCost(Request $request)
    {
        $data = $request->validate([
            "origin"        => 'required',
            "destination"   => 'required',
            "weight"        => 'required',
            "courier"       => 'required',
        ]);
        
        $key = http_build_query($data);

        if(Cache::has($key)) {

            return response()->json([
                'success' => true,
                'results' => Cache::get($key)
            ]);

        } else {
   
            $json = Rajaongkir::cost($request->all());
    
            $obj = json_decode($json);
    
            if($obj->success == true && count($obj->results) > 0) {
    
                Cache::put($key, $obj->results, now()->addHours(8));

                return response()->json($obj);
    
            } else {
    
                return response()->json([
                    'success' => false,
                    'message' => $obj->message
                ]);
            }

        }
    }
    public function waybill(Request $request)
    {
        $data = $request->validate([
            'courier' => 'required',
            'waybill' => 'required',
        ]);

        $key = http_build_query($data);

        if(Cache::has($key)) {

            return response()->json([
                'success' => true,
                'results' => Cache::get($key)
            ]);

        } else {

            $json = Rajaongkir::waybill($data);

            $obj = json_decode($json);
        
            if($obj->success == true && count($obj->results) > 0) {

                Cache::put($key, $obj->results, now()->addHours(2));

                return response()->json($obj);

            } else {

                return response()->json([
                    'success' => false,
                    'message' => $obj->message
                ]);
            }
        }

    }

    public function findSubdistrict($key)
    {
        $key = filter_var($key, FILTER_SANITIZE_SPECIAL_CHARS);
        
        $subdistricts = Subdistrict::where('subdistrict_name', 'like', '%' .$key.'%')
            ->get();

        return response()->json([
            'success' => true,
            'results' => $subdistricts,
            'key' => $key
        ]);
    }
}
