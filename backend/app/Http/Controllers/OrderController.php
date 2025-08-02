<?php
// backend/app/Http/Controllers/OrderController.php
// 負責處理訂單相關的 API 請求。

namespace App\Http\Controllers;

use App\Models\Order;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class OrderController extends Controller
{
    /**
     * 取得目前使用者的所有訂單
     * @param \Illuminate\Http\Request $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function index(Request $request)
    {
        $orders = $request->user()->orders()->with('orderItems.product')->get();
        return response()->json($orders);
    }

    /**
     * 建立新訂單
     * @param \Illuminate\Http\Request $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function store(Request $request)
    {
        $request->validate([
            'items' => 'required|array',
            'items.*.product_id' => 'required|exists:products,id',
            'items.*.quantity' => 'required|integer|min:1',
            'shipping_address' => 'required|string',
        ]);

        DB::beginTransaction();
        try {
            $order = $request->user()->orders()->create([
                'shipping_address' => $request->shipping_address,
                'status' => 'pending',
                'total_amount' => 0,
            ]);

            $total = 0;
            $orderItems = [];
            foreach ($request->items as $productId => $quantity) {
                $product = \App\Models\Product::find($productId);
                if (!$product) {
                    throw new \Exception("商品 ID: {$productId} 不存在。");
                }
                $subtotal = $product->price * $quantity;
                $total += $subtotal;

                $orderItems[] = new \App\Models\OrderItem([
                    'product_id' => $product->id,
                    'quantity' => $quantity,
                    'price' => $product->price,
                    'subtotal' => $subtotal,
                ]);
            }

            $order->orderItems()->saveMany($orderItems);
            $order->update(['total_amount' => $total]);

            DB::commit();
            return response()->json($order->load('orderItems.product'), 201);
        } catch (\Exception $e) {
            DB::rollBack();
            return response()->json(['error' => '建立訂單失敗。' . $e->getMessage()], 500);
        }
    }

    /**
     * 取得單一訂單詳情
     * @param \App\Models\Order $order
     * @return \Illuminate\Http\JsonResponse
     */
    public function show(Order $order)
    {
        if ($order->user_id !== auth()->id()) {
            abort(403);
        }
        return response()->json($order->load('orderItems.product'));
    }
}
