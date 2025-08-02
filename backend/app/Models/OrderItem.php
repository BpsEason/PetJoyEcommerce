<?php
// backend/app/Models/OrderItem.php
// Laravel Eloquent 模型，代表資料庫中的 `order_items` 資料表。

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class OrderItem extends Model
{
    use HasFactory;

    /**
     * 指定資料表名稱
     * @var string
     */
    protected $table = 'order_items';

    /**
     * 允許批量賦值的欄位
     * @var array
     */
    protected $fillable = [
        'order_id',
        'product_id',
        'quantity',
        'price',
        'subtotal',
    ];
    
    /**
     * 訂單商品與訂單的關聯
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function order(): BelongsTo
    {
        return $this->belongsTo(Order::class);
    }

    /**
     * 訂單商品與商品本身的關聯
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function product(): BelongsTo
    {
        return $this->belongsTo(Product::class);
    }
}
