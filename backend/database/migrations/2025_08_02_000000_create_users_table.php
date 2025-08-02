<?php
// backend/database/migrations/2025_08_02_000000_create_users_table.php
// 此檔案定義 `users` 資料表的結構。

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
        Schema::create('users', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->string('email')->unique();
            $table->string('password');
            $table->string('role')->default('user'); // 新增 role 欄位以區分使用者和管理員
            $table->timestamps();
        });
    }

    /**
     * 還原遷移 (移除資料表)
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('users');
    }
};
