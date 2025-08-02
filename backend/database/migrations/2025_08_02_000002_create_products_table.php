<?php
// backend/database/migrations/2025_08_02_000002_create_products_table.php
// 此檔案定義 `products` 資料表的結構。

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * 執行遷移 (建立資料表)
     * @return void
     */
    public function up()
    {
        Schema::create('products', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->string('slug')->unique(); // 商品 slug，用於友善 URL
            $table->text('description')->nullable();
            $table->decimal('price', 8, 2);
            $table->integer('stock')->default(0);
            $table->string('image_url')->nullable();
            $table->foreignId('category_id')->nullable()->constrained('categories')->nullOnDelete();
            $table->timestamps();
        });
    }

    /**
     * 還原遷移 (移除資料表)
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('products');
    }
};
