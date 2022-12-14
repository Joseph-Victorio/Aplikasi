<?php

namespace App\Http\Controllers;

use App\Models\User;
use App\Models\Order;
use App\Models\Product;
use App\Models\Transaction;
use Illuminate\Support\Str;
use Illuminate\Http\Request;
use App\Models\ProductVarian;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\DB;
use Silehage\Tripay\Facades\Tripay;

class FrontOrderController extends Controller
{
    public function getInvoice($orderRef)
    {
        return response([
            'success' => true,
            'results' => Order::with(['items', 'transaction'])->where('order_ref', $orderRef)->first()
        ]);
    }

    public function searchOrder(Request $request)
    {
        $request->validate([
            'key' => ['required', 'string']
        ]);
        
        
        try {

            $q = filter_var($request->key, FILTER_SANITIZE_SPECIAL_CHARS);

            $this->data['results'] = Order::with('transaction')
                ->where('customer_whatsapp', $q)
                ->orWhere('order_ref', $q)
                ->orderByDesc('updated_at')
                ->get();
 
             $this->data['count'] = Order::where('customer_whatsapp', $q)
             ->orWhere('order_ref', $q)
             ->count();
             
         } catch (\Throwable $th) {
 
             $this->data['message'] = $th->getMessage();
             $this->data['code'] = 500;
             $this->data['success'] = false;
         }
 
         return response($this->data);  

    }

    public function getRandomOrder()
    {
        $items = DB::table('order_items')
        ->select('order_items.id', 'order_items.name', 'order_items.created_at', 'orders.customer_name', 'assets.filename')
        ->join('orders', 'order_items.order_id', 'orders.id')
        ->join('products', 'order_items.product_id', 'products.id')
        ->join('assets', function($join) {
            $join->on('products.id', '=', 'assets.assetable_id')
                    ->where('assets.assetable_type', '=', 'Product');
        })
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

        return response()->json(['results' => $items], 200);
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

        $uniqueCode = $request->payment_type == 'BANK_TRANSFER' ? rand(10, 100) : 0;
        $orderRef = 'INV' .Carbon::now()->format('ymdHs') .  rand(1,99) . Str::upper(Str::random(2));

        $orderTotal = $request->payment_type == 'BANK_TRANSFER' ? $request->total-$uniqueCode : $request->total;

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
                'order_unique_code' => $uniqueCode,
                'order_subtotal' => $request->subtotal,
                'order_total' => $orderTotal,
                'order_status' => 'UNPAID',
                'shipping_courier_name' => $request->shipping_courier_name,
                'shipping_courier_service' => $request->shipping_courier_service,
                'shipping_cost' => $request->shipping_cost,
                'discount' => $request->coupon_discount,
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

            if($request->payment_type == 'BANK_TRANSFER' || $request->payment_type == 'COD') {
  
                $transaction = new Transaction();
    
                $transaction->order_id = $order->id;
                $transaction->payment_type = $request->payment_type;
                $transaction->payment_method = $request->payment_method;
                $transaction->payment_code = $request->payment_code;
                $transaction->payment_name = $request->payment_name;
                $transaction->amount = $order->order_total;
    
                $transaction->payment_ref = 'DTR' . Carbon::now()->format('ymd') . rand(10, 99) .Str::upper(Str::random(5));
                $transaction->expired_time = Carbon::now()->addDays(2)->timestamp;;
                $transaction->total_fee = 0;
                $transaction->amount_received = 0;
    
                $transaction->save();

                DB::commit();
    
                return response([
                    'success' => true,
                    'results' => $order->load('items','transaction')
                ], 200);

            } else {

                $payload = [
                    'method'            => $request->payment_method,
                    'merchant_ref'      => $order->order_ref,
                    'amount'            => $order->order_total,
                    'customer_name'     => $order->customer_name,
                    'customer_email'    => $order->customer_email,
                    'customer_phone'    => $order->customer_whatsapp,
                    'order_items'       => $request->items,
                ];

                $json = Tripay::createTransaction($payload);

                $obj = json_decode($json);


                if($obj->success) {

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

                    DB::commit();

                    return response([
                        'success' => true,
                        'results' => $order->load('transaction', 'items'),
                    ], 200);
                        

                } else {
                    DB::rollBack();

                    return response([
                        'success' => false,
                        'results' => null
                    ], 400);
                }
            }

        } catch (\Throwable $th) {

            DB::rollBack();

            return response([
                'success' => false,
                'results' => null,
                'error' => $th->getMessage()
            ], 400);
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
