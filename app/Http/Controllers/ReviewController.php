<?php

namespace App\Http\Controllers;

use App\Models\Config;
use Exception;
use App\Models\Review;
use App\Models\Product;
use Illuminate\Http\Request;

class ReviewController extends Controller
{
    protected $result = ['status' => 200, 'success' => true];
    
    public function index(Request $request, $type)
    {
        
        try {

            $type = strtolower($type);
    
    
            if($type === 'approved') {
    
                $this->result['results'] = Review::with('product')->approved()->latest()->skip($request->skip ?? 0)->take($request->take?? 10)->get();
                
            } elseif($type == 'unapproved') {
    
                $this->result['results'] = Review::with('product')->unapproved()->latest()->skip($request->skip ?? 0)->take($request->take?? 10)->get();
    
            } else {
    
                $this->result['results'] = Review::with('product')->latest()->skip($request->skip ?? 0)->take($request->take?? 10)->get();
            }


        } catch (Exception $e) {

            $this->result = [
                'status' => 500,
                'success' => false,
                'message' => $e->getMessage()
            ];
        }

        return $this->getResponse();
    }
    public function publish($id)
    {
        
        try {
            
            Review::where('id', $id)->update([
                'is_approved' => 1
            ]);

        } catch (Exception $e) {

            $this->result = [
                'status' => 500,
                'success' => false,
                'message' => $e->getMessage()
            ];
        }

        return $this->getResponse();
    }
    public function destroy($id)
    {
        
        try {

            Review::where('id', $id)->delete();

        } catch (Exception $e) {

            $this->result = [
                'status' => 500,
                'success' => false,
                'message' => $e->getMessage()
            ];
        }

        return $this->getResponse();
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

            $this->result['results'] = $review;
            $this->result['message'] = $approvCfg ? 'Berhasil mengulas produk' : 'Ulasan anda menunggu di publish';

        } catch (Exception $e) {

            $this->result = [
                'status' => 500,
                'success' => false,
                'message' => $e->getMessage()
            ];
        }

        return $this->getResponse();

    }
    public function show(Request $request, $id)
    {

        try {
            
            $this->result['results'] = Review::where('product_id', $id)->approved()->latest()->skip($request->skip?? 0)->take(6)->get();

        } catch (Exception $e) {

            $this->result = [
                'status' => 500,
                'success' => false,
                'message' => $e->getMessage()
            ];
        }

        return $this->getResponse();
    }

    protected function getResponse()
    {
        return response()->json($this->result, $this->result['status']);
    }
}
