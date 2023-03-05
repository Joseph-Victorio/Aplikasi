<?php

namespace App\Http\Controllers;

use App\Models\Order;
use App\Models\Product;
use App\Helpers\ApiResponse;
use Illuminate\Http\Request;
use App\Models\ProductVarian;

class OrderController extends Controller
{
    protected $data = ['skip' => 0,'limit' => 6,'data' => []];

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

            $instance = Order::with('transaction');

            if($search) {
                $instance->where('customer_whatsapp', 'LIKE', '%'.$search . '%')
                ->orWhere('order_ref', $search)
                ->orWhere('customer_name', 'LIKE', '%'.$search . '%')
                ->orWhere('customer_email', 'LIKE', '%'.$search . '%');
            }
            if($filter && $filter != 'ALL') {
                $instance->where('order_status', $filter);
            }

            $this->data['count'] = $instance->count();

            if($this->data['count'] > 0) {

                if(!$search) {
                    $instance->skip($this->data['skip'])->take($this->data['limit']);
                }

                $this->data['data'] = $instance->orderByDesc('updated_at')->get();
            }

            return ApiResponse::success($this->data);
            
        } catch (\Throwable $th) {

            return ApiResponse::failed($th);
        }

    }

    public function show($orderRef)
    {
        $data = Order::with(['items', 'transaction'])
                        ->where('order_ref', $orderRef)
                        ->first();
        return ApiResponse::success($data);
    }

    public function destroy($id)
    {
        
        $order = Order::findOrFail($id);

        $order->delete();

        return ApiResponse::success();

    }
    public function paymentAccepted($id)
    {
        $order = Order::find($id);
        $transaction = $order->transaction;

        $order->order_status = 'TOSHIP';
        $order->updated_at = now();
        $order->save();

        $transaction->status = 'PAID';
        $transaction->paid_at = now();
        $transaction->save();

        foreach($order->items as $item) {
           $this->setStock($item->sku, $item->quantity, true);
        }

        return ApiResponse::success();
    }
    
    public function searchAdminOrder(Request $request)
    {
        $request->validate([
            'key' => ['required', 'string']
        ]);

        try {

            $q = filter_var($request->key, FILTER_SANITIZE_SPECIAL_CHARS);

            $this->data['data'] = Order::with('transaction')
                ->where('customer_whatsapp', 'like', '%'.$q .'%')
                ->orWhere('order_ref', 'like', '%'.$q .'%')
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
            $order->shipping_delivered = now();
            $order->updated_at = now();
            
            if($request->boolean('update_to_ship')) {

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

        if($request->status == 'COMPLETE') {

            $order->transaction()->update([
                'status' => 'PAID'
            ]);

        }

        if($order->order_status == 'CANCELED') {
            
            foreach($order->items as $item) {

                $this->setStock($item->sku, $item->quantity);
    
            }
        }

        return ApiResponse::success($order);

    }
    public function cancelOrder($id)
    {
        $order = Order::findOrFail($id);

        foreach($order->items as $item) {

            $this->setStock($item->sku, $item->quantity);

        }

        $order->update(['order_status' => 'CANCELED']);

        return ApiResponse::success($order);
    }
    
    protected function setStock($sku, $qty, $decrement = false)
    {
        $productData = Product::where('sku', $sku)->first();

        if(!$productData) {

            $productData = ProductVarian::where('sku', $sku)->first();
        }

        if($productData) {

            if($decrement) {

                $productData->stock -= $qty;

            } else {

                $productData->stock += $qty;
            }
            
            $productData->save();

        }
    }
    
}
