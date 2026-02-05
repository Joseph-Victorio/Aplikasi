<?php

namespace App\Http\Controllers;

use App\Models\Order;
use App\Models\Product;
use App\Helpers\ApiResponse;
use Illuminate\Http\Request;
use App\Models\ProductVarian;

class OrderController extends Controller
{
    protected $data = ['skip' => 0, 'limit' => 6, 'data' => []];

    public function __construct()
    {
        $this->data['limit'] = request()->limit ?? 6;
        $this->data['skip'] = request()->skip ?? 0;
    }
    public function index(Request $request)
    {
        try {

            $search = $request->query('search') ?? null;
            $filter = $request->query('filter') ?? null;

            $instance = Order::query();

            if ($search) {
                $instance->where('customer_phone', 'LIKE', '%' . $search . '%')
                    ->orWhere('order_ref', $search)
                    ->orWhere('customer_name', 'LIKE', '%' . $search . '%')
                    ->orWhere('customer_email', 'LIKE', '%' . $search . '%');
            }
            if ($filter && $filter != 'ALL') {
                $instance->where('order_status', $filter);
                $instance->orderByDesc('updated_at');
            } else {
                $instance->latest();
            }

            $this->data['count'] = $instance->count();

            if ($this->data['count'] > 0) {

                if (!$search) {
                    $instance->skip($this->data['skip'])->take($this->data['limit']);
                }

                $this->data['data'] = $instance->get();
            }

            return ApiResponse::success($this->data);
        } catch (\Throwable $th) {

            return ApiResponse::failed($th);
        }
    }

    public function show($orderRef)
    {
        $data = Order::with(['items'])
            ->where('order_ref', $orderRef)
            ->first();
        return ApiResponse::success($data);
    }

    public function destroy(Request $request, $id)
    {

        $order = Order::findOrFail($id);

        if ($request->boolean('update_stock')) {
            foreach ($order->items as $item) {

                $this->incrementStock($item->sku, $item->quantity);
            }
        }

        $order->delete();

        return ApiResponse::success();
    }
    public function paymentAccepted($id)
    {
        $order = Order::find($id);

        $order->order_status = 'TOSHIP';
        $order->updated_at = now();
        $order->save();
        return ApiResponse::success();
    }

    public function searchAdminOrder(Request $request)
    {
        $request->validate([
            'key' => ['required', 'string']
        ]);

        try {

            $q = filter_var($request->key, FILTER_SANITIZE_SPECIAL_CHARS);

            $this->data['data'] = Order::where('customer_phone', 'like', '%' . $q . '%')
                ->orWhere('order_ref', 'like', '%' . $q . '%')
                ->orderByDesc('updated_at')
                ->get();

            return ApiResponse::success($this->data);
        } catch (\Throwable $th) {

            return ApiResponse::failed($th);
        }
    }
    public function inputResi(Request $request)
    {
        $request->validate([
            'order_id' => ['required'],
            'resi' => ['required'],
        ]);

        try {
            $order = Order::findOrFail($request->order_id);

            $order->shipping_courier_code = $request->resi;

            if ($request->boolean('update_to_ship')) {

                $order->order_status = 'SHIPPING';
            }

            $order->save();

            return ApiResponse::success($order);
        } catch (\Throwable $th) {

            return ApiResponse::failed($th);
        }
    }

    public function updateStatusOrder(Request $request)
    {
        $request->validate([
            'order_id' => 'required',
            'status' => 'required',
        ]);

        $order = Order::find($request->order_id);

        $order->order_status = $request->status;
        $order->updated_at = now();

        $order->save();

        if ($request->status == 'CANCELED') {

            if ($request->boolean('update_stock') == true) {

                foreach ($order->items as $item) {

                    $this->incrementStock($item->sku, $item->quantity);
                }
            }
        } else {
            if ($request->boolean('update_stock') == true) {
                foreach ($order->items as $item) {

                    $this->decrementStock($item->sku, $item->quantity);
                }
            }
        }


        return ApiResponse::success($order);
    }

    protected function incrementStock($sku, $qty)
    {
        $productData = Product::where('sku', $sku)->first();

        if (!$productData) {

            $productData = ProductVarian::where('sku', $sku)->first();
        }

        if ($productData) {

            $productData->stock += $qty;
            $productData->save();
        }
    }
    protected function decrementStock($sku, $qty)
    {
        $productData = Product::where('sku', $sku)->first();

        if (!$productData) {

            $productData = ProductVarian::where('sku', $sku)->first();
        }

        if ($productData) {

            $productData->stock -= $qty;
            $productData->save();
        }
    }
}
