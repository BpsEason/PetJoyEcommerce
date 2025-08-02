<?php
// backend/app/Http/Controllers/CategoryController.php
// 負責處理商品分類相關的 API 請求。

namespace App\Http\Controllers;

use App\Models\Category;
use Illuminate\Http\Request;

class CategoryController extends Controller
{
    /**
     * 取得所有商品分類列表
     * @return \Illuminate\Http\JsonResponse
     */
    public function index()
    {
        $categories = Category::all();
        return response()->json($categories);
    }

    /**
     * 根據 slug 取得單一商品分類及旗下所有商品
     * @param string $slug
     * @return \Illuminate\Http\JsonResponse
     */
    public function show(string $slug)
    {
        $category = Category::where('slug', $slug)->with('products')->firstOrFail();
        return response()->json($category);
    }
}
