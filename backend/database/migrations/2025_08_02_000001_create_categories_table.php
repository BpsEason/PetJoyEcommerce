<?php
// backend/database/migrations/2025_08_02_000001_create_categories_table.php
// 此檔案定義 `categories` 資料表的結構。

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
        Schema::create('categories', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->string('slug')->unique();
            $table->timestamps();
        });
    }

    /**
     * 還原遷移 (移除資料表)
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('categories');
    }
};
