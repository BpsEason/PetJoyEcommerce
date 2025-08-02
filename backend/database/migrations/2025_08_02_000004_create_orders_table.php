<?php
// backend/database/migrations/2025_08_02_000004_create_orders_table.php
// 此檔案定義 `orders` 資料表的結構。

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
        Schema::create('orders', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained('users');
            $table->string('shipping_address');
            $table->string('status')->default('pending'); // 例如：pending, processing, shipped, completed, cancelled
            $table->decimal('total_amount', 10, 2);
            $table->timestamps();
        });
    }

    /**
     * 還原遷移 (移除資料表)
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('orders');
    }
};
