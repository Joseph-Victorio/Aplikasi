<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\UpdateController;
use App\Http\Controllers\Frontend\FrontController;

// MOHON UNTUK TIDAK MENGEDIT ROUTE DIBAWAH

Route::get('/', [FrontController::class, 'homepage']);
Route::get('/products', [FrontController::class, 'products']);
Route::get('/products/category/{category}', [FrontController::class, 'productCategory'])->name('product.category');
Route::get('/product/{slug}', [FrontController::class, 'productDetail'])->name('product.show');
Route::get('/posts', [FrontController::class, 'postIndex']);
Route::get('/post/{slug}', [FrontController::class, 'postDetail'])->name('post.show');
Route::get('/p/invoice/{id}', [FrontController::class, 'showInvoice'])->name('invoice');
Route::get('/sitemap.xml', [FrontController::class, 'sitemap']);
Route::get('/clear-cache', [FrontController::class, 'clearCache']);
Route::get('/force-update', [UpdateController::class, 'forceUpdate']);
Route::get('/{any}', [FrontController::class, 'any'])->where('any','^(?!api).*$');