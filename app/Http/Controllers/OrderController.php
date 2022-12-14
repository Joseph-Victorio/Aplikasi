<?php

namespace App\Http\Controllers;

use Tripay;
use Carbon\Carbon;
use App\Models\User;
use App\Models\Order;
use App\Models\Product;
use App\Models\Transaction;
use Illuminate\Support\Str;
use Illuminate\Http\Request;

use App\Models\ProductVarian;
use Illuminate\Support\Facades\DB;

class OrderController extends Controller
{
    protected $data = [
        'success' => true,
        'skip' => 0,
        'limit' => 6,
        'code' => 200,
        'results' => null,
    ];

    public function __construct()
    {
        if(request()->limit) {
            $this->data['limit'] = request()->limit;
        }
        if(request()->skip) {
            $this->data['skip'] = request()->skip;
        }
    }
    public function index(Request $request)
    {
        try {

            $search = $request->query('search') ?? null;
            $filter = $request->query('filter') ?? null;

            $instance = Order::with('transaction');

            if($search) {
                $instance->where('customer_whatsapp', $search)->orWhere('order_ref', $search);
            }
            if($filter && $filter != 'ALL') {
                $instance->where('order_status', $filter);
            }

            $this->data['results'] = $instance
                ->orderByDesc('updated_at')
                ->skip($this->data['skip'])
                ->take($this->data['limit'])
                ->get();

            $this->data['count'] = $instance->count();
            
        } catch (\Throwable $th) {

            $this->data['message'] = $th->getMessage();
            $this->data['code'] = 500;
            $this->data['success'] = false;
        }

        return response($this->data);
    }
    public function getCustomerOrders(Request $request)
    {
       
        try {

           $this->data['results'] = Order::with('transaction')
            ->where('user_id', $request->user()->id)
            ->skip($this->data['skip'])
            ->latest()
            ->take($this->data['limit'])
            ->get();

            $this->data['count'] = Order::where('user_id', $request->user()->id)->count();
            
        } catch (\Throwable $th) {

            $this->data['message'] = $th->getMessage();
            $this->data['code'] = 500;
            $this->data['success'] = false;
        }

        return response($this->data);  
    }

    public function show($orderRef)
    {
        return response([
            'success' => true,
            'results' => Order::with(['items', 'transaction'])->where('order_ref', $orderRef)->first()
        ]);
    }

    public function destroy($id)
    {
        $order = Order::findOrFail($id);

        $order->delete();

        return response([ 'success' => true ], 200);

    }
    public function paymentAccepted($id)
    {
        $order = Order::find($id);
        $transaction = $order->transaction;

        $order->order_status = 'PROCESS';
        $order->save();

        $transaction->status = 'PAID';
        $transaction->paid_at = now();
        $transaction->save();

        foreach($order->items as $item) {
           $this->setStock($item->sku, $item->quantity, true);
        }

        return response([ 'success' => true ], 200);
    }
    public function filterOrder(Request $request)
    {
        $request->validate([
            'filter' => ['required', 'string']
        ]);

        try {

            $this->data['results'] = Order::with('transaction')
             ->skip($this->data['skip'])
             ->take($this->data['limit'])
             ->where('order_status', $request->filter)
             ->orderByDesc('updated_at')
             ->get();
 
             $this->data['count'] = Order::count();
             
         } catch (\Throwable $th) {
 
             $this->data['message'] = $th->getMessage();
             $this->data['code'] = 500;
             $this->data['success'] = false;
         }
 
         return response($this->data);  

    }
    
    public function searchAdminOrder(Request $request)
    {
        $request->validate([
            'key' => ['required', 'string']
        ]);

        try {

            $q = filter_var($request->key, FILTER_SANITIZE_SPECIAL_CHARS);

            $this->data['results'] = Order::with('transaction')
                ->where('customer_whatsapp', 'like', '%'.$q .'%')
                ->orWhere('order_ref', 'like', '%'.$q .'%')
                ->orderByDesc('updated_at')
                ->get();
             
         } catch (\Throwable $th) {
 
             $this->data['message'] = $th->getMessage();
             $this->data['code'] = 500;
             $this->data['success'] = false;
         }
 
         return response($this->data);  

    }
    public function inputResi(Request $request)
    {
        $request->validate([
            'order_id' => ['required'],
            'resi' => ['required'],
        ]);
        $order = Order::findOrFail($request->order_id);

        $order->shipping_courier_code = $request->resi;
        $order->shipping_delivered = now();
        $order->order_status = 'SHIPPING';

        $order->save();

        return response([ 'success' => true ], 200);
    }

    public function updateStatusOrder(Request $request)
    {
        $request->validate([
            'order_id' => 'required',
            'status' => 'required',
        ]);

        $order = Order::find($request->order_id);
        $order->order_status = $request->status;

        $order->save();

        if($order->shipping_courier_name == 'COD' && $request->status == 'COMPLETE') {

            $order->transaction()->update([
                'status' => 'PAID'
            ]);

        }

        if($order->order_status == 'CANCELED') {
            
            foreach($order->items as $item) {

                $this->setStock($item->sku, $item->quantity);
    
            }
        }

        return response([ 'success' => true ], 200);
    }
    public function cancelOrder($id)
    {
        $order = Order::findOrFail($id);

        foreach($order->items as $item) {

            $this->setStock($item->sku, $item->quantity);

        }

        $order->update(['order_status' => 'CANCELED']);

        return response()->json(['success' => true]);
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
