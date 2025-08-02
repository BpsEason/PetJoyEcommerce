<?php
// backend/app/Http/Controllers/CartController.php
// 負責處理購物車相關的 API 請求，使用 Redis 儲存。

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Redis;
use App\Models\Product;
use Illuminate\Support\Facades\DB;

class CartController extends Controller
{
    /**
     * 購物車的 Redis Key 格式
     * @param int $userId
     * @return string
     */
    protected function getCartKey(int $userId): string
    {
        return "cart:{$userId}";
    }
    
    /**
     * 取得目前使用者的購物車內容
     * @param \Illuminate\Http\Request $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function index(Request $request)
    {
        $cartKey = $this->getCartKey($request->user()->id);
        $cart = Redis::hgetall($cartKey);
        
        $items = [];
        $total = 0;
        foreach ($cart as $productId => $quantity) {
            $product = Product::find($productId);
            if ($product) {
                $subtotal = $product->price * $quantity;
                $total += $subtotal;
                $items[] = [
                    'product_id' => $productId,
                    'product_name' => $product->name,
                    'quantity' => (int) $quantity,
                    'price' => $product->price,
                    'subtotal' => $subtotal,
                ];
            }
        }
        
        return response()->json([
            'items' => $items,
            'total' => $total,
        ]);
    }
    
    /**
     * 新增商品到購物車
     * @param \Illuminate\Http\Request $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function add(Request $request)
    {
        $request->validate([
            'product_id' => 'required|exists:products,id',
            'quantity' => 'required|integer|min:1',
        ]);
        
        $cartKey = $this->getCartKey($request->user()->id);
        $productId = $request->input('product_id');
        $quantity = $request->input('quantity');
        
        Redis::hincrby($cartKey, $productId, $quantity);
        
        return response()->json(['message' => '商品已成功加入購物車。']);
    }

    /**
     * 移除購物車中的商品
     * @param \Illuminate\Http\Request $request
     * @param int $productId
     * @return \Illuminate\Http\JsonResponse
     */
    public function remove(Request $request, int $productId)
    {
        $cartKey = $this->getCartKey($request->user()->id);
        Redis::hdel($cartKey, $productId);

        return response()->json(['message' => '商品已成功從購物車移除。']);
    }

    /**
     * 更新購物車中商品的數量
     * @param \Illuminate\Http\Request $request
     * @param int $productId
     * @return \Illuminate\Http\JsonResponse
     */
    public function update(Request $request, int $productId)
    {
        $request->validate([
            'quantity' => 'required|integer|min:1',
        ]);
        
        $cartKey = $this->getCartKey($request->user()->id);
        Redis::hset($cartKey, $productId, $request->input('quantity'));

        return response()->json(['message' => '購物車已更新。']);
    }

    /**
     * 結帳，將購物車內容轉換為訂單
     * @param \Illuminate\Http\Request $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function checkout(Request $request)
    {
        $cartKey = $this->getCartKey($request->user()->id);
        $cart = Redis::hgetall($cartKey);

        if (empty($cart)) {
            return response()->json(['error' => '購物車是空的。'], 400);
        }

        // 使用 transaction 確保訂單和訂單商品同時成功或失敗
        DB::beginTransaction();
        try {
            $orderController = new OrderController();
            $request->request->set('items', $cart); // 模擬訂單建立請求
            $response = $orderController->store($request);
            
            // 訂單建立成功後，清空購物車
            Redis::del($cartKey);
            DB::commit();

            return response()->json($response->getData(), 201);
        } catch (\Exception $e) {
            DB::rollBack();
            return response()->json(['error' => '結帳失敗，請稍後再試。'], 500);
        }
    }
}
