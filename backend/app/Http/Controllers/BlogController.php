<?php
// backend/app/Http/Controllers/BlogController.php
// 負責處理部落格文章相關的 API 請求。

namespace App\Http\Controllers;

use App\Models\BlogPost;
use Illuminate\Http\Request;

class BlogController extends Controller
{
    /**
     * 取得所有部落格文章列表
     * @return \Illuminate\Http\JsonResponse
     */
    public function index()
    {
        $posts = BlogPost::select('title', 'slug', 'created_at')->get();
        return response()->json($posts);
    }

    /**
     * 根據 slug 取得單一部落格文章
     * @param string $slug
     * @return \Illuminate\Http\JsonResponse
     */
    public function show(string $slug)
    {
        $post = BlogPost::where('slug', $slug)->firstOrFail();
        return response()->json($post);
    }
}
