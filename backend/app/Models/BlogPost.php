<?php
// backend/app/Models/BlogPost.php
// Laravel Eloquent 模型，代表資料庫中的 `blog_posts` 資料表。

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class BlogPost extends Model
{
    use HasFactory;
    
    /**
     * 指定資料表名稱
     * @var string
     */
    protected $table = 'blog_posts';
    
    /**
     * 允許批量賦值的欄位
     * @var array
     */
    protected $fillable = [
        'title',
        'slug',
        'content',
    ];
}
