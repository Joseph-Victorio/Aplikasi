<?php

namespace App\Http\Controllers;

use Exception;
use Illuminate\Http\Request;
use App\Repositories\ProductRepository;
use App\Http\Resources\ProductListCollection;

class FrontProductController extends Controller
{
    private $productRepository;

    public function __construct(ProductRepository $productRepository)
    {
        $this->productRepository = $productRepository;
    }
    
    public function getProducts()
    {

        try {

            return new ProductListCollection($this->productRepository->getAll());

        } catch (Exception $e) {

            $this->result = [
                'status' => 500,
                'success' => false,
                'message' => $e->getMessage()
            ];
        }

        return response()->json($this->result, $this->result['status']);


    }

    public function getProductsFavorites(Request $request)
    {
        $request->validate([
            'pids' => 'required'
        ]);

        try {

            return new ProductListCollection($this->productRepository->getProductsFavorites($request->pids));

        } catch (Exception $e) {

            return response()->json(['status' => 500, 'success' => false,'message' => $e->getMessage()]);
        }

        return response()->json($this->result, $this->result['status']);
       
    }

    public function getProductsByCategory(Request $request, $id)
    {     

        try {
            return new ProductListCollection($this->productRepository->getProductsByCategory($id, $request));


        } catch (Exception $e) {

            return response()->json(['status' => 500, 'success' => false,'message' => $e->getMessage()]);
        }


       
    }

    public function searchProduct($key)
    {
        if(!$key) {
            return response([
                'success' => false,
            ], 404);
         }
 
         $key = filter_var($key, FILTER_SANITIZE_SPECIAL_CHARS);
 
         try {
             
            return new ProductListCollection($this->productRepository->search($key));
 
         } catch (Exception $e) {
 
             return response()->json(['status' => 500, 'success' => false,'message' => $e->getMessage()]);
         }
    }

    public function getProductDetail($slug)
    {
       
        try {
            
            return $this->productRepository->show($slug);

       } catch (Exception $e) {

           return response()->json(['status' => 500, 'success' => false,'message' => $e->getMessage()]);
       }
    }
}
