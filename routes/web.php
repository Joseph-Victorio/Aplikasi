<?php

use App\Models\Product;
use App\Models\ProductVarian;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\FrontController;
use App\Http\Controllers\UpdateController;


// DO NOT EDIT THE ROUTE BELOW

Route::get('test', function() {

  //  $data = ProductVarian::whereHas('productUnupdated')
  //   ->get();

    
    $data = Product::select('id', 'price', 'weight')->whereHas('varians')->with(['varians' => function($query) {
      $query->whereNotNull('price');
      $query->where('has_subvarian', 0);
    }])->get();

    foreach($data as $product) {

      foreach($product->varians as $varian) {

          if($varian->price < $product->price) {
              $varian->price += $product->price;
          }
          if($varian->weight == 0) {
              $varian->weight = $product->weight;
          }
          // $varian->save();
      }
  }

    return $data;
});

Route::get('/', [FrontController::class, 'homepage']);
Route::get('products', [FrontController::class, 'products']);
Route::get('products/category/{category}', [FrontController::class, 'productCategory'])->name('product.category');
Route::get('product/{slug}', [FrontController::class, 'productDetail'])->name('product.show');
Route::get('posts', [FrontController::class, 'postIndex']);
Route::get('post/{slug}', [FrontController::class, 'postDetail'])->name('post.show');
Route::get('p/invoice/{id}', [FrontController::class, 'showInvoice'])->name('invoice');
Route::get('sitemap.xml', [FrontController::class, 'sitemap']);
Route::get('clear-cache', [FrontController::class, 'clearCache']);
Route::get('force-update', [UpdateController::class, 'forceUpdate']);
Route::get('/{any}', [FrontController::class, 'any'])->where('any','^(?!api).*$');