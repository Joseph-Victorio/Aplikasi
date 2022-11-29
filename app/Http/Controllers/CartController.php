<?php

namespace App\Http\Controllers;

use App\Models\Cart;
use Carbon\Carbon;
use Illuminate\Http\Request;

class CartController extends Controller
{
    public function get()
    {
        $carts = [];

        if(getSessionUser()) {
            $carts = Cart::all();
        }
        
        return response()->json([
            'results' => $carts,
        ], 200);
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

        $cart = Cart::where('sku', $request->sku)->first();

        if($cart) {
            $cart->quantity += $request->quantity;
            $cart->save();
            
        } else {

            Cart::create([
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


        return response(['status' => true], 200);
    }

    public function update(Request $request)
    {

        $cart = Cart::where('sku', $request->sku)->first();

        if($cart) {
            
            $cart->quantity = $request->quantity;
    
            $cart->save();
    
            return response(['status' => true], 200);
        }

        return response(['status' => false], 400);
    }

    public function destroy(Request $request)
    {
        Cart::where('sku', $request->sku)->delete();

        return response(['status' => true], 200);
    }
    public function clear()
    {
        Cart::where('session_id', getSessionUser())->delete();

        return response(['status' => true], 200);
    }
}
