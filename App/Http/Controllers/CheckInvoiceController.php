<?php

namespace App\Http\Controllers;

use App\Models\Order;
use Illuminate\Http\Request;
use App\Helpers\ApiResponse;

class CheckInvoiceController extends Controller
{
    public function search(Request $request)
    {
        $request->validate([
            'key' => ['required', 'string']
        ]);

        try {
            $key = $request->key;

            $orders = Order::select('id', 'order_ref', 'customer_name', 'customer_phone', 'order_status', 'created_at')
                ->where(function ($q) use ($key) {
                    $q->where('order_ref', 'like', "%{$key}%")
                        ->orWhere('customer_phone', 'like', "%{$key}%");
                })
                ->orderByDesc('created_at')
                ->get()
                ->map(function ($o) {
                    return [
                        'id' => $o->id,
                        'order_ref' => $o->order_ref,
                        'customer_name' => $o->customer_name,
                        'customer_phone' => $o->customer_phone,
                        'order_status' => $o->order_status,
                        'created' => $o->created_at->diffForHumans()
                    ];
                });

            return ApiResponse::success($orders);
        } catch (\Throwable $th) {
            return ApiResponse::failed($th->getMessage());
        }
    }
}
