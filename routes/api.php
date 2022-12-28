<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\BankController;
use App\Http\Controllers\CartController;
use App\Http\Controllers\PostController;
use App\Http\Controllers\UserController;
use App\Http\Controllers\BlockController;
use App\Http\Controllers\OrderController;
use App\Http\Controllers\PromoController;
use App\Http\Controllers\StoreController;
use App\Http\Controllers\ConfigController;
use App\Http\Controllers\NotifyController;
use App\Http\Controllers\ReviewController;
use App\Http\Controllers\SliderController;
use App\Http\Controllers\TripayController;
use App\Http\Controllers\UpdateController;
use App\Http\Controllers\ProductController;
use App\Http\Controllers\CategoryController;
use App\Http\Controllers\FrontApiController;
use App\Http\Controllers\FrontOrderController;
use App\Http\Controllers\FrontProductController;
use App\Http\Controllers\ShippingController;
use App\Http\Controllers\MailConfigController;
use App\Http\Controllers\TransactionController;
use App\Http\Controllers\PasswordResetController;

Route::middleware(['auth:sanctum', 'auth.admin'])->group(function() {

    Route::get('userList', [UserController::class, 'userList']);
    Route::get('findUser/{key}', [UserController::class, 'findUser']);
    Route::delete('user/{id}', [UserController::class, 'destroy']);

    Route::apiResource('products', ProductController::class);

    Route::post('toggleProductPromo', [ProductController::class, 'toggleProductPromo']);
    Route::get('getProductPromo/{promoId}', [ProductController::class, 'getProductPromo']);
    Route::get('findNotDiscountProduct', [ProductController::class, 'findNotDiscountProduct']);
    Route::get('searchAdminProducts/{key}', [ProductController::class, 'searchAdminProducts']);
    Route::get('getProductVariansByProduct/{productId}', [ProductController::class, 'getProductVariansByProduct']);

    Route::apiResource('sliders', SliderController::class);
    Route::post('update-slider-weight', [SliderController::class, 'updateSliderWeight']);

    Route::post('shop', [StoreController::class, 'update']);
    
    Route::apiResource('categories', CategoryController::class);
    Route::apiResource('posts', PostController::class);
    Route::apiResource('blocks', BlockController::class);
    Route::apiResource('banks', BankController::class);

    Route::delete('orders/{id}', [OrderController::class, 'destroy']);
    Route::get('orders', [OrderController::class, 'index']);
    Route::post('searchAdminOrder', [OrderController::class, 'searchAdminOrder']);
    Route::put('orders', [OrderController::class, 'update']);
    Route::post('updateStatusOrder', [OrderController::class, 'updateStatusOrder']);
    Route::post('paymentAccepted/{id}', [OrderController::class, 'paymentAccepted']);
    Route::post('inputResi', [OrderController::class, 'inputResi']);
    Route::post('cancelOrder/{id}', [OrderController::class, 'cancelOrder']);
    Route::get('orders/{orderRef}',[OrderController::class, 'show']);

    Route::get('update', [UpdateController::class, 'overview']);
    Route::post('update', [UpdateController::class, 'update']);  
    Route::post('clearCache', [UpdateController::class, 'clearCache']);  
    
    Route::apiResource('promo', PromoController::class);
    Route::get('getPromoDetail/{id}', [PromoController::class, 'getPromoDetail']);
    Route::post('removeProductPromo', [PromoController::class, 'removeProductPromo']);

    Route::post('submitProductPromo', [ProductController::class, 'submitProductPromo']);
    Route::get('getProductPromo/{promoId}', [ProductController::class, 'getProductPromo']);
    Route::get('findProductWithoutPromo/{key}', [ProductController::class, 'findProductWithoutPromo']);

    Route::get('testingTelegram', [NotifyController::class, 'testingTelegram']);
    Route::get('testingEmail', [NotifyController::class, 'testingEmail']);

    Route::get('mailConfig', [MailConfigController::class, 'show']);
    Route::post('mailConfig', [MailConfigController::class, 'update']);

    Route::get('reviews/{type}', [ReviewController::class, 'index']);
    Route::put('reviews/{id}', [ReviewController::class, 'publish']);
    Route::delete('reviews/{id}', [ReviewController::class, 'destroy']);
    
});

Route::middleware('auth:sanctum')->group(function() {

    Route::get('adminConfig', [ConfigController::class, 'adminConfig']);
    Route::post('config', [ConfigController::class, 'update']);
    
    Route::get('user', [UserController::class, 'index']);
    Route::post('user/logout', [UserController::class, 'logout']);
    Route::post('user/update', [UserController::class, 'update']);
    
    Route::post('filterOrder', [OrderController::class, 'filterOrder']);
    Route::get('getCustomerOrders', [OrderController::class, 'getCustomerOrders']);
    Route::post('refreshToken', [UserController::class, 'refreshToken']);
    
});

Route::middleware(['throttle:auth'])->group(function() {
    
    Route::post('user/login', [UserController::class, 'login']);
    Route::post('user/register', [UserController::class, 'register']);
    Route::post('requestPasswordToken', [PasswordResetController::class, 'requestPasswordToken']);
    Route::get('validateToken/{token}', [PasswordResetController::class, 'validateToken']);
    Route::post('resetPassword', [PasswordResetController::class, 'resetPassword']);
    
});

Route::get('homepage', [FrontApiController::class, 'homepage']);
Route::get('getPosts', [FrontApiController::class, 'getPosts']);
Route::get('getPostDetail/{slug}', [FrontApiController::class, 'getPostDetail']);
Route::get('getBanks', [FrontApiController::class, 'getBanks']);
Route::get('getSliders', [FrontApiController::class, 'getSliders']);
Route::get('getCategories', [FrontApiController::class, 'getCategories']);
Route::get('getPromotePosts', [FrontApiController::class, 'getPromotePosts']);

Route::get('getProducts', [FrontProductController::class, 'getProducts']);
Route::get('getProductDetail/{slug}', [FrontProductController::class, 'getProductDetail']);
Route::post('getProductsFavorites', [FrontProductController::class, 'getProductsFavorites']);
Route::get('getProductsByCategory/{id}', [FrontProductController::class, 'getProductsByCategory']);
Route::get('searchProduct/{key}', [FrontProductController::class, 'searchProduct']);

Route::get('getRandomOrder', [FrontOrderController::class, 'getRandomOrder']);
Route::post('storeOrder', [FrontOrderController::class, 'storeOrder']);
Route::post('searchOrder', [FrontOrderController::class, 'searchOrder']);
Route::get('getInvoice/{invoice}', [FrontOrderController::class, 'getInvoice']);

Route::post('addProductReview', [ReviewController::class, 'store']);
Route::get('loadProductReview/{id}', [ReviewController::class, 'show']);

Route::get('shop', [StoreController::class, 'index']);
Route::get('config',[ConfigController::class, 'show']);

Route::get('transaction/detail',[TransactionController::class, 'show']);

Route::get('shipping/getProvince', [ShippingController::class, 'getProvince']);
Route::get('shipping/getCity/{province_id}', [ShippingController::class, 'getCity']);
Route::get('shipping/getSubdistict/{city_id}', [ShippingController::class, 'getSubdistrict']);
Route::post('shipping/getCost', [ShippingController::class, 'getCost']);
Route::post('shipping/waybill', [ShippingController::class, 'waybill']);
Route::get('shipping/findSubdistrict/{key}', [ShippingController::class, 'findSubdistrict']);

Route::get('tripay/payment-chanel',[TripayController::class, 'getPaymentChanels']);
Route::post('tripay/callback',[TripayController::class, 'callback'])->name('tripay.callback');

Route::get('carts', [CartController::class, 'get']);
Route::post('carts', [CartController::class, 'store']);
Route::put('carts', [CartController::class, 'update']);
Route::post('cart/delete', [CartController::class, 'destroy']);
Route::post('clearCart', [CartController::class, 'clear']);

Route::post('sendOrderNotify', [NotifyController::class, 'sendOrderNotify']);
