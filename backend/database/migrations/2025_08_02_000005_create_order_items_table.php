<?php
// backend/database/migrations/2025_08_02_000005_create_order_items_table.php
// 此檔案定義 `order_items` 資料表的結構。

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
        Schema::create('order_items', function (Blueprint $table) {
            $table->id();
            $table->foreignId('order_id')->constrained('orders');
            $table->foreignId('product_id')->constrained('products');
            $table->integer('quantity');
            $table->decimal('price', 8, 2); // 購買時的商品價格
            $table->decimal('subtotal', 10, 2);
            $table->timestamps();
        });
    }

    /**
     * 還原遷移 (移除資料表)
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('order_items');
    }
};
