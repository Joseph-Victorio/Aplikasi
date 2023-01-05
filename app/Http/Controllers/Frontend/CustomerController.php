<?php

namespace App\Http\Controllers\Frontend;

use App\Models\Order;
use App\Helpers\ApiResponse;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;

class CustomerController extends Controller
{
    public function getOrders(Request $request)
    {
       
        try {

           $data['data'] = Order::with('transaction')
            ->where('user_id', $request->user()->id)
            ->skip($request->skip ?? 0)
            ->latest()
            ->take($request->limit ?? 6)
            ->get();

            $data['count'] = Order::where('user_id', $request->user()->id)->count();

            return ApiResponse::success($data);
            
        } catch (\Throwable $th) {

            return ApiResponse::failed($th);
        }

    }
}
