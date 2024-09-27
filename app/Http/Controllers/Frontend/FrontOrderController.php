<?php

namespace App\Http\Controllers\Frontend;

use App\Models\Order;
use App\Models\Product;
use App\Helpers\ApiResponse;
use App\Models\OrderItem;
use Illuminate\Http\Request;
use App\Models\ProductVarian;
use Illuminate\Support\Carbon;
use Illuminate\Routing\Controller;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Cache;

class FrontOrderController extends Controller
{
   public function getInvoice($orderRef)
   {
      try {
         $data =  Order::with(['items'])->where('order_ref', $orderRef)->first();

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

         $data =  Order::where('customer_phone', $q)
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
      $data = Cache::remember('order_items_random',  now()->addMinutes(5), function () use ($latest) {

         return DB::table('order_items')
            ->select('order_items.id', 'order_items.name', 'order_items.created_at', 'orders.customer_name', 'assets.filename')
            ->join('orders', 'order_items.order_id', 'orders.id')
            ->join('products', 'order_items.product_id', 'products.id')
            ->join('assets', function ($join) {
               $join->on('products.id', '=', 'assets.assetable_id')
                  ->where('assets.assetable_type', '=', 'Product');
            })
            ->where('order_items.id', '>=', $latest)
            ->inRandomOrder()
            ->get()->map(function ($item) {
               return [
                  'id' => $item->id,
                  'name' => $item->name,
                  'customer_name' => $item->customer_name,
                  'created' => $item->created_at >= Carbon::now()->subDays(2) ? Carbon::parse($item->created_at)->diffForHumans() : 'Beberapa waktu lalu',
                  'image' => url('/upload/images/' . $item->filename)
               ];
            });
      });

      return ApiResponse::success($data);
   }

   public function storeOrder(Request $request)
   {

      $request->validate([
         'customer_name' => ['required', 'string'],
         'customer_phone' => ['required', 'string'],
         'customer_address' => ['required', 'string'],
         'items' => ['required', 'array'],
         'quantity' => ['required', 'numeric'],
         'subtotal' => ['required', 'numeric'],
         'total' => ['required', 'numeric'],
         'weight' => ['required', 'numeric'],
         'shipping_cost' => ['required', 'numeric'],
         'shipping_courier_name' => ['required', 'string'],
         'shipping_courier_service' => ['required', 'string'],
      ]);

      DB::beginTransaction();

      try {

         $order = Order::create([
            'customer_name' => $request->customer_name,
            'customer_phone' => $request->customer_phone,
            'shipping_address' => $request->customer_address,
            'order_qty' => $request->quantity,
            'order_weight' => $request->weight,
            'order_subtotal' => $request->subtotal,
            'order_total' => $request->total,
            'order_status' => 'PENDING',
            'shipping_courier_name' => $request->shipping_courier_name . ' - ' . $request->shipping_courier_service,
            'shipping_cost' => $request->shipping_cost,
            'note' => $request->note ?? NULL
         ]);

         foreach ($request->items as $item) {

            $item = $order->items()->create([
               'sku' => $item['sku'],
               'name' => $item['name'],
               'product_id' => $item['product_id'],
               'quantity' => $item['quantity'],
               'price' => $item['price'],
               'note' => $item['note']
            ]);

            $this->decrementStock($item->sku, $item->quantity);
         }

         DB::commit();

         $data = $order->load('items');

         return ApiResponse::success($data);
      } catch (\Exception $e) {

         DB::rollBack();

         return ApiResponse::failed($e);
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
