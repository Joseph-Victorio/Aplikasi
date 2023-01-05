<?php

namespace App\Http\Controllers\Frontend;

use Exception;
use Carbon\Carbon;
use App\Models\Cart;
use App\Helpers\ApiResponse;
use Illuminate\Http\Request;
use Illuminate\Routing\Controller;

class CartController extends Controller
{
    public function get()
    {
        $data = [];

        if(getSessionUser()) {
            $data = Cart::all();
        }
        
        return ApiResponse::success($data);
    }
    
    public function store(Request $request)
    {
        Cart::where('created_at', '<', Carbon::now()->subHours(2))->delete();

        $request->validate([
            'price' => 'required|numeric',
            'name' => 'required',
            'quantity' => 'required|numeric',
            'weight' => 'required|numeric',
            'sku' => 'required',
            'product_stock' => 'required',
        ]);

        $data = Cart::where('sku', $request->sku)->first();

        if($data) {
            $data->quantity += $request->quantity;
            $data->save();
            
        } else {

            $data = Cart::create([
                'session_id' => getSessionUser(),
                'price' => $request->price,
                'name' => $request->name,
                'weight' => $request->weight,
                'quantity' => $request->quantity,
                'sku' => $request->sku,
                'image_url' => $request->image_url,
                'product_url' => $request->product_url,
                'product_stock' => $request->product_stock,
                'product_id' => $request->product_id,
                'note' => $request->note
            ]);
        }


        return ApiResponse::success($data);
    }

    public function update(Request $request)
    {
 
        try {
            $cart = Cart::where('sku', $request->sku)->first();

            if(!$cart) {
                throw new Exception('Data not found');
            }
            $cart->quantity = $request->quantity;
    
            $cart->save();
    
            return ApiResponse::success($cart);

        } catch (Exception $e) {

            return ApiResponse::failed($e);
        }

    }

    public function destroy(Request $request)
    {
        Cart::where('sku', $request->sku)->delete();

        return ApiResponse::success();
    }
    public function clear()
    {
        Cart::where('session_id', getSessionUser())->delete();

        return ApiResponse::success();
    }
}
