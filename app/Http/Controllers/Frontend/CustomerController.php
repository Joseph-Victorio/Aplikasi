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

            $instance = Order::when($request->status, function ($q) use ($request) {
                if ($request->status != 'ALL') {
                    $q->where('order_status', $request->status);
                }
            })->where('user_id', $request->user()->id);

            $count = $instance->count();
            $data = [];

            if ($count > 0) {

                $data = $instance->skip($request->skip ?? 0)
                    ->latest()
                    ->take($request->limit ?? 6)
                    ->get();
            }

            $results['count'] = $count;
            $results['data'] = $data;

            return ApiResponse::success($results);
        } catch (\Throwable $th) {

            return ApiResponse::failed($th);
        }
    }
}
