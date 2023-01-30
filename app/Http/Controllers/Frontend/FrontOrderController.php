<?php

namespace App\Http\Controllers\Frontend;

use Exception;
use App\Models\User;
use App\Models\Order;
use App\Models\Product;
use App\Models\Transaction;
use Illuminate\Support\Str;
use App\Helpers\ApiResponse;
use App\Models\Config;
use App\Models\OrderItem;
use Illuminate\Http\Request;
use App\Models\ProductVarian;
use Illuminate\Support\Carbon;
use Illuminate\Routing\Controller;
use Illuminate\Support\Facades\DB;
use Silehage\Tripay\Facades\Tripay;
use Illuminate\Support\Facades\Cache;

class FrontOrderController extends Controller
{
    public function getInvoice($orderRef)
    {
        try {
            $data =  Order::with(['items', 'transaction'])->where('order_ref', $orderRef)->first();

            return ApiResponse::success($data);
             
         } catch (\Exception $e) {
 
            return ApiResponse::failed($e);
         }
    }

    public function searchOrder(Request $request)
    {
        $request->validate([
            'key' => ['required', 'string']
        ]);
        
        try {

            $q = filter_var($request->key, FILTER_SANITIZE_SPECIAL_CHARS);

            $data =  Order::with('transaction')
                ->where('customer_whatsapp', $q)
                ->orWhere('order_ref', $q)
                ->orderByDesc('updated_at')
                ->get();

             return ApiResponse::success($data);
             
         } catch (\Exception $e) {
 
            return ApiResponse::failed($e);
         }
 

    }

    public function getRandomOrder()
    {
        // FIXED Performance Issue
        $max = OrderItem::max('id');
        $latest = $max <= 60 ? 0 : $max - 60;
        $data = Cache::remember('order_items_random',  now()->addMinutes(5), function() use ($latest) {

            return DB::table('order_items')
            ->select('order_items.id', 'order_items.name', 'order_items.created_at', 'orders.customer_name', 'assets.filename')
            ->join('orders', 'order_items.order_id', 'orders.id')
            ->join('products', 'order_items.product_id', 'products.id')
            ->join('assets', function($join) {
                $join->on('products.id', '=', 'assets.assetable_id')
                        ->where('assets.assetable_type', '=', 'Product');
            })
            ->where('order_items.id', '>=', $latest)
            ->inRandomOrder()
            ->get()->map(function($item) {
                return [
                    'id' => $item->id,
                    'name' => $item->name,
                    'customer_name' => $item->customer_name,
                    'created' => $item->created_at >= Carbon::now()->subDays(2)? Carbon::parse($item->created_at)->diffForHumans() : 'Beberapa waktu lalu',
                    'image' => url('/upload/images/' . $item->filename)
                ];
            });

        });

        return ApiResponse::success($data);
    }

    public function storeOrder(Request $request)
    {

        $user = null;

        if($request->user_id) {

            $user = User::find($request->user_id);
        }

        $request->validate([
            'customer_name' => ['required', 'string'],
            'customer_phone' => ['required', 'string'],
            'customer_email' => ['required', 'email'],
            'payment_method' => ['required', 'string'],
            'payment_type' => ['required', 'string'],
            'payment_name' => ['required', 'string'],
            'customer_address' => ['required', 'string'],
            'items' => ['required', 'array'],
            'quantity' => ['required', 'numeric'],
            'subtotal' => ['required', 'numeric'],
            'total' => ['required', 'numeric'],
        ]);

        $name = filter_var($request->customer_name, FILTER_SANITIZE_SPECIAL_CHARS);
        $whatsapp = filter_var($request->customer_phone, FILTER_SANITIZE_SPECIAL_CHARS);

        $orderRef = 'INV' .Carbon::now()->format('ymdHs') .  rand(1,99) . Str::upper(Str::random(2));

        DB::beginTransaction();

        try {

            $order = Order::create([
                'user_id' => $user? $user->id : null,
                'order_ref' => $orderRef,
                'customer_name' => $name,
                'customer_whatsapp' => $whatsapp,
                'customer_email' => $request->customer_email,
                'shipping_address' => $request->customer_address,
                'order_qty' => $request->quantity,
                'order_weight' => $request->weight,
                'order_unique_code' => $request->unique_code,
                'order_subtotal' => $request->subtotal,
                'order_total' => $request->total,
                'order_status' =>  $request->payment_type == 'COD' ? 'TOSHIP' :'PENDING',
                'shipping_courier_name' => $request->shipping_courier_name,
                'shipping_courier_service' => $request->shipping_courier_service,
                'shipping_cost' => $request->shipping_cost,
                'payment_fee' => $request->payment_fee,
                'service_fee' => $request->service_fee,
            ]);
                
            foreach($request->items as $item) {

                $item = $order->items()->create([
                    'sku' => $item['sku'],
                    'name' => $item['name'],
                    'product_id' => $item['product_id'],
                    'quantity' => $item['quantity'],
                    'price' => $item['price'],
                    'note' => $item['note']
                ]);

                $this->setStock($item->sku, $item->quantity, true);

            }

            $config = Config::select('order_expired_time')->first();

            if($request->payment_type == 'BANK_TRANSFER' || $request->payment_type == 'COD') {
  
                $transaction = new Transaction();
    
                $transaction->order_id = $order->id;
                $transaction->payment_type = $request->payment_type;
                $transaction->payment_method = $request->payment_method;
                $transaction->payment_code = $request->payment_code;
                $transaction->payment_name = $request->payment_name;
                $transaction->amount = $order->order_total;
    
                $transaction->payment_ref = 'DTR' . Carbon::now()->format('ymd') . rand(10, 99) .Str::upper(Str::random(5));
                $transaction->expired_time = Carbon::now()->addHours($config->order_expired_time)->timestamp;
                $transaction->total_fee = 0;
                $transaction->amount_received = 0;
    
                $transaction->save();

            } else {

                $payload = [
                    'method'                => $request->payment_method,
                    'merchant_ref'          => $order->order_ref,
                    'amount'                => $order->order_total,
                    'customer_name'         => $order->customer_name,
                    'customer_email'        => $order->customer_email,
                    'customer_phone'        => $order->customer_whatsapp,
                    'order_items'           => $request->items,
                    'order_expired_time'    => $config->order_expired_time
                ];

                $json = Tripay::createTransaction($payload);

                $obj = json_decode($json);

                if(!$obj->success) {
                    throw new Exception('Can\'t not connect host tripay');
                }

                $transaction = new Transaction();

                $transaction->order_id = $order->id;
                $transaction->payment_type = $request->payment_type;
                $transaction->payment_name = $request->payment_name;
                $transaction->payment_method = $request->payment_method;

                $transaction->qr_url = $obj->data->qr_url ?? '';
                
                $transaction->payment_code = $obj->data->pay_code ?? '';
                $transaction->payment_ref = $obj->data->reference;
                $transaction->expired_time = $obj->data->expired_time;

                $transaction->amount = $obj->data->amount;
                $transaction->amount_received = $obj->data->amount_received;
                $transaction->total_fee = $obj->data->total_fee;
                $transaction->fee_merchant = $obj->data->fee_merchant;
                $transaction->fee_customer = $obj->data->fee_customer;
                $transaction->instructions = json_encode($obj->data->instructions);

                $transaction->save();

                $order->fresh();

                $order->payment_fee = $obj->data->fee_customer;

                $order->save();
                    
            }

            DB::commit();

            $data = $order->load('items','transaction');

            return ApiResponse::success($data);

        } catch (\Exception $e) {

            DB::rollBack();

            return ApiResponse::failed($e);
        }        
        
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
