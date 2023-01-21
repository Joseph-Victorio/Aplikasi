<?php

namespace App\Http\Controllers\Frontend;

use Exception;
use App\Helpers\ApiResponse;
use Illuminate\Http\Request;
use Illuminate\Routing\Controller;
use App\Http\Resources\ProductResource;
use App\Http\Resources\ProductListCollection;
use App\Repositories\Products\ProductRepository;

class FrontProductController extends Controller
{
    private $productRepository;
    protected $limit = 6;

    public function __construct(ProductRepository $productRepository)
    {
        $this->productRepository = $productRepository;

        if(request('limit')) {
            $this->limit = request('limit');
        }
    }
    
    public function getProducts(Request $request)
    {

        try {
            $per_page = $request->per_page ?? $this->limit;
            $order_by = $request->order_by ?? 'DESC';

            return new ProductListCollection($this->productRepository->getAll($per_page, $order_by));

        } catch (Exception $e) {

            return ApiResponse::failed($e);
        }


    }

    public function getProductsFavorites(Request $request)
    {
        $request->validate([
            'pids' => 'required'
        ]);

        try {

            return new ProductListCollection($this->productRepository->getManyInId($request->pids));

        } catch (Exception $e) {

            return ApiResponse::failed($e);
        }

    }

    public function productsByCategory(Request $request)
    {     

        try {
            $ids = array($request->category_id);
            $per_page = $request->per_page ?? $this->limit;
            $order_by = $request->order_by ?? 'DESC';

            return new ProductListCollection($this->productRepository->getManyInCategory($ids, $per_page, $order_by));


        } catch (Exception $e) {

            return ApiResponse::failed($e);
        }
       
    }

    public function searchProduct($key)
    {
        
        try {
            
            $key = filter_var($key, FILTER_SANITIZE_SPECIAL_CHARS);

            if(!$key) {
               throw new Exception('Search keyword invalid');
             }
             
            return new ProductListCollection($this->productRepository->search($key));
 
         } catch (Exception $e) {
 
             return ApiResponse::failed($e);
         }
    }

    public function productDetail($slug)
    {
       
        try {
            
            return new ProductResource($this->productRepository->getSingleProduct($slug));

       } catch (Exception $e) {

           return ApiResponse::failed($e);
       }
    }
}
