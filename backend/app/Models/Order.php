<?php
// backend/app/Models/Order.php
// Laravel Eloquent 模型，代表資料庫中的 `orders` 資料表。

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Order extends Model
{
    use HasFactory;

    /**
     * 指定資料表名稱
     * @var string
     */
    protected $table = 'orders';

    /**
     * 允許批量賦值的欄位
     * @var array
     */
    protected $fillable = [
        'user_id',
        'shipping_address',
        'status',
        'total_amount',
    ];
    
    /**
     * 訂單與使用者的關聯
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }
    
    /**
     * 訂單與訂單商品的關聯
     * @return \Illuminate\Database\Eloquent\Relations\HasMany
     */
    public function orderItems(): HasMany
    {
        return $this->hasMany(OrderItem::class);
    }
}
