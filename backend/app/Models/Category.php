<?php
// backend/app/Models/Category.php
// Laravel Eloquent 模型，代表資料庫中的 `categories` 資料表。

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Category extends Model
{
    use HasFactory;
    
    /**
     * 指定資料表名稱
     * @var string
     */
    protected $table = 'categories';
    
    /**
     * 允許批量賦值的欄位
     * @var array
     */
    protected $fillable = [
        'name',
        'slug',
    ];
    
    /**
     * 分類與商品的關聯
     * @return \Illuminate\Database\Eloquent\Relations\HasMany
     */
    public function products(): HasMany
    {
        return $this->hasMany(Product::class);
    }
}
