<?php

namespace App\Http\Controllers\Frontend;

use Exception;
use App\Models\Config;
use App\Models\Review;
use App\Models\Product;
use App\Helpers\ApiResponse;
use Illuminate\Http\Request;
use Illuminate\Routing\Controller;
use Illuminate\Support\Facades\Cache;

class ReviewController extends Controller
{
    
    public function index(Request $request, $type)
    {
        
        try {

            $type = strtolower($type);

            $data = [];
    
    
            if($type === 'approved') {
    
                $data = Review::with('product')->approved()->latest()->skip($request->skip ?? 0)->take($request->take?? 10)->get();
                
            } elseif($type == 'unapproved') {
    
                $data = Review::with('product')->unapproved()->latest()->skip($request->skip ?? 0)->take($request->take?? 10)->get();
    
            } else {
    
                $data = Review::with('product')->latest()->skip($request->skip ?? 0)->take($request->take?? 10)->get();
            }

            return ApiResponse::success($data);


        } catch (Exception $e) {

            return ApiResponse::failed($e);
        }

    }
    public function publish($id)
    {
        
        try {
            
            Review::where('id', $id)->update([
                'is_approved' => 1
            ]);

            return ApiResponse::success();


        } catch (Exception $e) {

            return ApiResponse::failed($e);
        }

    }
    public function destroy($id)
    {
        
        try {

            Review::where('id', $id)->delete();

            return ApiResponse::success();


        } catch (Exception $e) {

            return ApiResponse::failed($e);
        }
    }

    public function store(Request $request)
    {
        $request->validate([
            'product_id' => ['required'],
            'name' => ['required'],
            'comment' => ['required'],
            'rating' => ['required', 'numeric', 'min:1', 'max:5'],
        ]);

        try {

            $product = Product::findOrFail($request->product_id);

            $approvCfg = false;

            $config = Config::first();

            if($config->review_auto_approved){
                $approvCfg = true;
            }

            $review = $product->reviews()->create([
                'comment' => $request->comment,
                'rating' => $request->rating,
                'name' => $request->name,
                'is_approved' => $approvCfg
            ]);

            $data = $review;
            $msg = $approvCfg ? 'Berhasil mengulas produk' : 'Ulasan anda menunggu di publish';

            Cache::forget($product->slug);

            return ApiResponse::success($data, $msg);


        } catch (Exception $e) {

            return ApiResponse::failed($e);
        }

    }
    public function show(Request $request, $id)
    {

        try {
            
            $data  = Review::where('product_id', $id)->approved()->latest()->skip($request->skip?? 0)->take(6)->get();

            return ApiResponse::success($data);


        } catch (Exception $e) {

            return ApiResponse::failed($e);
        }
    }

}
