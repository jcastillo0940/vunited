<?php
use Illuminate\Support\Facades\Route; use App\Http\Controllers\StoreController;
Route::prefix('v1/store')->group(function(){Route::get('products',[StoreController::class,'products']);Route::get('products/{slug}',[StoreController::class,'product']);Route::get('cart',[StoreController::class,'cart']);Route::post('cart/items',[StoreController::class,'addCart']);Route::post('checkout',[StoreController::class,'checkout']);Route::post('payments/events',[StoreController::class,'paymentEvent']);Route::get('orders/{id}',[StoreController::class,'order']);});
