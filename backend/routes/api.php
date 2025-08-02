<?php
// backend/routes/api.php
// 此檔案用於定義所有後端 API 路由，新增了商品分類路由。

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\ProductController;
use App\Http\Controllers\OrderController;
use App\Http\Controllers\CartController;
use App\Http\Controllers\BlogController;
use App\Http\Controllers\CategoryController;

// 公開路由，無需認證
Route::post('register', [AuthController::class, 'register']);
Route::post('login', [AuthController::class, 'login']);

// 商品、分類與部落格公開路由
Route::get('products', [ProductController::class, 'index']);
Route::get('products/{slug}', [ProductController::class, 'show']);
Route::get('categories', [CategoryController::class, 'index']);
Route::get('categories/{slug}', [CategoryController::class, 'show']);
Route::get('blog', [BlogController::class, 'index']);
Route::get('blog/{slug}', [BlogController::class, 'show']);

// 受保護的路由，需 JWT 認證
Route::group(['middleware' => ['api', 'auth:api']], function ($router) {
    Route::post('logout', [AuthController::class, 'logout']);
    Route::post('refresh', [AuthController::class, 'refresh']);
    Route::post('me', [AuthController::class, 'me']);
    
    // 訂單相關路由
    Route::get('orders', [OrderController::class, 'index']);
    Route::post('orders', [OrderController::class, 'store']);
    Route::get('orders/{order}', [OrderController::class, 'show']);
    
    // 購物車相關路由，使用 Redis
    Route::get('cart', [CartController::class, 'index']);
    Route::post('cart/add', [CartController::class, 'add']);
    Route::delete('cart/remove/{item}', [CartController::class, 'remove']);
    Route::put('cart/update/{item}', [CartController::class, 'update']);
    Route::post('cart/checkout', [CartController::class, 'checkout']);
});
