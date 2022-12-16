<?php

namespace App\Http\Controllers;

use Exception;
use App\Models\Promo;
use App\Models\Product;
use App\Models\ProductPromo;
use Illuminate\Http\Request;
use App\Http\Requests\ProductRequest;
use Illuminate\Support\Facades\Cache;
use App\Repositories\ProductRepository;

class ProductController extends Controller
{
    public $limit = 10;
    private $productRepository;

    protected $result = ['status' => 200, 'success' => true];

    public function __construct(ProductRepository $productRepository)
    {
        $this->productRepository = $productRepository;
    }

    public function index()
    {
        try {

            $this->result['results'] = Product::with(['featuredImage', 'category', 'varianItems.parent'])
                    ->latest()
                    ->paginate($this->limit);

        } catch (Exception $e) {

            $this->result = [
                'status' => 500,
                'success' => false,
                'message' => $e->getMessage()
            ];
        }
        return response()->json($this->result, $this->result['status']);
    }

    public function searchAdminProducts($key)
    {
        try {

            $this->result['results'] = Product::where('title', 'like', '%'.$key.'%')
                ->with(['featuredImage', 'category', 'varianItems.parent'])
                ->paginate($this->limit);

        } catch (Exception $e) {

            $this->result = [
                'status' => 500,
                'success' => false,
                'message' => $e->getMessage()
            ];
        }
        
        return response()->json($this->result, $this->result['status']);
    }

    public function show($id)
    {

        try {
            
            $this->result['results'] = Product::with('assets', 'category', 'varians.subvarian')
            ->where('id', $id) 
            ->first();

        } catch (Exception $e) {

            $this->result = [
                'status' => 500,
                'success' => false,
                'message' => $e->getMessage()
            ];
        }

        return response()->json($this->result, $this->result['status']);
    }

    public function store(ProductRequest $request)
    {

        try {
            
            $this->result['results'] = $this->productRepository->store($request);

        } catch (Exception $e) {

            $this->result = [
                'status' => 500,
                'success' => false,
                'message' => $e->getMessage()
            ];
        }

        return response()->json($this->result, $this->result['status']);
        
    }

    public function update(Request $request, $id)
    {
        $product = Product::find($id);

        $request->validate([
            'id' => 'required',
            'title' => 'required',
            'price' => 'required',
            'weight' => 'required',
            'stock' => 'required',
            'description' => 'required',
            'images' => $request->del_images && count($product->assets) == count($request->del_images) && !$request->images?'required' : 'nullable'
        ]);

        try {
            
            $this->result['results'] = $this->productRepository->update($request);

        } catch (Exception $e) {

            $this->result = [
                'status' => 500,
                'success' => false,
                'message' => $e->getMessage()
            ];
        }

        return response()->json($this->result, $this->result['status']);

    }

    public function findNotDiscountProduct()
    {
        try {
            
            $this->result['results'] = Product::whereNull('promote_id')->whereNull('discount_id')->get();

        } catch (Exception $e) {

            $this->result = [
                'status' => 500,
                'success' => false,
                'message' => $e->getMessage()
            ];
        }

        return response()->json($this->result, $this->result['status']);

    }

    public function toggleProductPromo(Request $request)
    {
        $request->validate([
            'product_id' => 'required',
            'promote_id' => 'required',
        ]);

        try {
            
            $product = Product::find($request->product_id);

            if($product->promote_id) {

                $product->promote_id = null;

            } else {

                $product->promote_id = $request->promote_id;
            }
    
            $product->save();

            Cache::forget('products');
            Cache::forget('initial_products');

        } catch (Exception $e) {

            $this->result = [
                'status' => 500,
                'success' => false,
                'message' => $e->getMessage()
            ];
        }

        return response()->json($this->result, $this->result['status']);
    }
    public function destroy($id)
    {
        try {
            
            $this->productRepository->destroy($id);

        } catch (Exception $e) {

            $this->result = [
                'status' => 500,
                'success' => false,
                'message' => $e->getMessage()
            ];
        }

        return response()->json($this->result, $this->result['status']);
    }
    public function findProductWithoutPromo($key)
    {
        try {
            
            $this->result['results'] = Product::doesntHave('promo')->where('title', 'like', '%'. $key . '%')->get();

        } catch (Exception $e) {

            $this->result = [
                'status' => 500,
                'success' => false,
                'message' => $e->getMessage()
            ];
        }

        return response()->json($this->result, $this->result['status']);

    }
    public function getProductPromo($promoId)
    {
        
        try {
            $promo = Promo::find($promoId);

            $this->result['results'] = $promo->products;

        } catch (Exception $e) {

            $this->result = [
                'status' => 500,
                'success' => false,
                'message' => $e->getMessage()
            ];
        }

        return response()->json($this->result, $this->result['status']);

    }
    public function submitProductPromo(Request $request)
    {
        $data = $request->validate([
            'product_id' => 'required',
            'promo_id' => 'required',
            'discount_amount' => 'required',
            'discount_type' => 'required'
        ]);

        try {

            $product = ProductPromo::updateOrCreate([
                'product_id' => $request->product_id
            ], $data);
            
            $this->result['data'] = $product;

            Cache::forget('products');
            Cache::forget('initial_products');
            Cache::forget('product_promo');

        } catch (Exception $e) {

            $this->result = [
                'status' => 500,
                'success' => false,
                'message' => $e->getMessage()
            ];
        }

        return response()->json($this->result, $this->result['status']);
    }
}
