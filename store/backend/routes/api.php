<?php
use Illuminate\Support\Facades\Route; use App\Http\Controllers\StoreController; use App\Http\Controllers\Api\AuthController; use App\Http\Controllers\Admin\CashPaymentController;
Route::prefix('v1/store')->group(function(){
 Route::get('products',[StoreController::class,'products']);Route::get('products/{slug}',[StoreController::class,'product']);Route::get('cart',[StoreController::class,'cart']);Route::post('cart/items',[StoreController::class,'addCart']);Route::post('checkout',[StoreController::class,'checkout']);Route::post('payments/events',[StoreController::class,'paymentEvent']);Route::get('orders/{id}',[StoreController::class,'order']);
 Route::post('auth/login',[AuthController::class,'login']);
 Route::middleware('auth:sanctum')->group(function(){
  Route::post('auth/logout',[AuthController::class,'logout']);
  Route::middleware('admin')->prefix('admin')->group(function(){
   Route::get('cash-payments',[CashPaymentController::class,'index']);
   Route::post('cash-payments/{publicId}/confirm',[CashPaymentController::class,'confirm']);
  });
 });
});
