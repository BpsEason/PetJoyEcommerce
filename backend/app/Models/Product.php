<?php
// backend/app/Models/Product.php
// Laravel Eloquent 模型，代表資料庫中的 `products` 資料表。

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Product extends Model
{
    use HasFactory;

    /**
     * 指定資料表名稱
     * @var string
     */
    protected $table = 'products';

    /**
     * 允許批量賦值的欄位
     * @var array
     */
    protected $fillable = [
        'name',
        'slug',
        'description',
        'price',
        'stock',
        'image_url',
        'category_id',
    ];

    /**
     * 產品與分類的關聯
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function category(): BelongsTo
    {
        return $this->belongsTo(Category::class);
    }
}
