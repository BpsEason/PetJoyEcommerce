<?php
// backend/app/Http/Controllers/ProductController.php
// 負責處理商品相關的 API 請求。

namespace App\Http\Controllers;

use App\Models\Product;
use Illuminate\Http\Request;

class ProductController extends Controller
{
    /**
     * 取得所有商品列表
     * @return \Illuminate\Http\JsonResponse
     */
    public function index()
    {
        $products = Product::all();
        return response()->json($products);
    }

    /**
     * 根據 slug 取得單一商品
     * @param string $slug
     * @return \Illuminate\Http\JsonResponse
     */
    public function show(string $slug)
    {
        $product = Product::where('slug', $slug)->firstOrFail();
        return response()->json($product);
    }
}
