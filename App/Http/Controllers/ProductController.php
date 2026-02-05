<?php

namespace App\Http\Controllers;

use Exception;
use App\Helpers\ApiResponse;
use Illuminate\Http\Request;
use App\Http\Requests\ProductRequest;
use App\Repositories\Products\AdminProductRepository;

class ProductController extends Controller
{
    public $limit = 10;
    private $productRepository;

    public function __construct(AdminProductRepository $productRepository)
    {
        $this->productRepository = $productRepository;
        $this->limit = request('limit') ?? 10;
    }

    public function index()
    {
        try {

            $data = $this->productRepository->index($this->limit);

            return ApiResponse::success($data);

        } catch (Exception $e) {

            return ApiResponse::failed($e);
        }
    }
    public function productVarians($productId)
    {
        try {

            $data =  $this->productRepository->productVarianByProductId($productId);

            return ApiResponse::success($data);

        } catch (Exception $e) {

            return ApiResponse::failed($e);
        }
    }
    

    public function searchProducts($key)
    {
        try {

            $data = $this->productRepository->search($key);

            return ApiResponse::success($data);

        } catch (Exception $e) {

            return ApiResponse::failed($e);
        }
    }

    public function show($id)
    {

        try {
            
            $data =  $this->productRepository->show($id);

            return ApiResponse::success($data);

        } catch (Exception $e) {

            return ApiResponse::failed($e);
        }
    }

    public function store(ProductRequest $request)
    {

        try {
            
            $data =  $this->productRepository->store($request);

            return ApiResponse::success($data);

        } catch (Exception $e) {

            return ApiResponse::failed($e);
        }
        
    }

    public function update(ProductRequest $request, $id)
    {

        try {
            
            $data = $this->productRepository->update($request, $id);

            return ApiResponse::success($data);

        } catch (Exception $e) {

            return ApiResponse::failed($e);
        }

    }

    public function destroy($id)
    {
        try {
            
            return APiResponse::success($this->productRepository->destroy($id));

        } catch (Exception $e) {

            return ApiResponse::failed($e);
        }

    }
    public function findProductWithoutPromo(Request $request)
    {
        try {
            
            $data = $this->productRepository->findProductWithoutPromo($request);

            return ApiResponse::success($data);

        } catch (Exception $e) {

            return ApiResponse::failed($e);
        }

    }
    public function productPromo($promoId)
    {
        
        try {

            $data = $this->productRepository->getProductPromo($promoId);

            return ApiResponse::success($data);

        } catch (Exception $e) {

            return ApiResponse::failed($e);
        }

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

            $data = $this->productRepository->storeProductPromo($data);

            return ApiResponse::success($data);

        } catch (Exception $e) {

            return ApiResponse::failed($e);
        }
    }

}
