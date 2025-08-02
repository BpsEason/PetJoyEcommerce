# PetJoyEcommerce 專案

這是一個使用 **Nuxt 3 (前端)** 和 **Laravel 10 (後端)** 構建的寵物電商平台最小可行性產品 (MVP)。專案整合了 **PWA**、**SEO 優化**、**JWT 認證**、**Redis 購物車** 和 **Docker 容器化部署**，提供高效能且易於擴展的電商解決方案，適合開發者學習現代全端開發技術。

## 專案亮點

以下是 PetJoyEcommerce 的核心功能與技術，展示專案的設計理念：

| **功能/技術**             | **描述**                                                                 | **優勢**                                                                 |
|---------------------------|-------------------------------------------------------------------------|-------------------------------------------------------------------------|
| **前後端分離**            | 使用 Nuxt 3 作為前端，Laravel 10 提供後端 API，模組化設計。              | 易於維護與擴展，適合團隊開發。                                         |
| **PWA 支援**             | 透過 `@vite-pwa/nuxt` 實現漸進式網頁應用，支援離線使用與快速載入。      | 提升行動裝置上的用戶體驗，可安裝為應用程式。                           |
| **SEO 優化**             | Nuxt 3 的 SSR 與 `useHead` 設置 OG/Twitter 標籤，優化搜尋引擎排名。     | 增加網站曝光，吸引更多流量。                                           |
| **JWT 認證**             | 使用 `tymon/jwt-auth` 實現安全的 API 認證，支援註冊與登入。              | 確保用戶數據安全，符合電商平台需求。                                   |
| **Redis 購物車**          | 使用 Redis 儲存購物車數據，支援快速讀寫與高併發。                       | 提升購物車性能，適合高峰流量。                                         |
| **Docker 容器化**         | 整合 Laravel、Nuxt、Nginx、MySQL 和 Redis 的 Docker Compose 配置。       | 簡化部署，確保環境一致性。                                             |
| **PlanetScale 支援**      | 支援 `mysql_aurora` 驅動，連接到 PlanetScale 的無伺服器資料庫。          | 提供高可用性與自動擴展，降低維護成本。                                 |
| **CI/CD 流程**            | 使用 GitHub Actions 實現自動化測試與部署。                              | 提高程式碼品質與部署效率。                                             |

## 系統架構圖

以下是 PetJoyEcommerce 的系統架構，展示各組件的互動流程：

```mermaid
graph TD
    A[用戶_瀏覽器] -->|HTTP_請求| B[Nginx_反向代理]
    B -->|前端路由| C[Nuxt_3_前端]
    B -->|API_請求_含_JWT| D[Laravel_10_後端]
    C -->|SSR_渲染| E[產品頁_部落格頁]
    C -->|JWT_認證_請求| D
    D -->|驗證_JWT_中介層| D
    D -->|購物車操作| G[Redis_購物車數據]
    D -->|資料庫操作| H[MySQL_PlanetScale_產品_訂單_用戶]
    C -->|API_呼叫| D
```

## 目錄結構

GitHub 倉庫包含以下關鍵檔案，實現核心功能（如購物車、認證）。其他基本代碼（如 Laravel 和 Nuxt 的基礎檔案）需手動新增：

- `frontend/`：Nuxt 3 前端程式碼
  - `pages/products/[slug].vue`：商品詳情頁，支援 SSR 和 SEO。
  - `pages/blog/[slug].vue`：部落格文章頁，支援 Markdown 渲染。
  - `pages/login.vue`：登入頁面，處理 JWT 認證。
  - `nuxt.config.ts`：Nuxt 配置，包含 PWA 和 Tailwind CSS。
  - `public/icons/`：PWA 圖示占位檔案。
- `backend/`：Laravel 10 後端 API
  - `app/Http/Controllers/`：包含 `CartController.php`（Redis 購物車）、`AuthController.php`（JWT 認證）等。
  - `routes/api.php`：定義 RESTful API 端點。
  - `database/migrations/`：資料庫遷移檔案，定義資料表結構。
- `docker/`：Docker Compose 配置檔案。
- `nginx/`：Nginx 代理配置，包含靜態檔案快取。
- `.github/workflows/`：CI/CD 自動化工作流程。

## API 端點

以下是後端 API 的關鍵端點，採用 RESTful 設計：

### 認證
- **`POST /api/register`**: 註冊新使用者，回傳 JWT token。
- **`POST /api/login`**: 使用電子郵件和密碼登入，儲存 JWT token。
- **`POST /api/logout`**: 登出使用者，需提供有效 token。

### 商品與分類
- **`GET /api/products`**: 獲取所有商品列表。
- **`GET /api/products/{slug}`**: 根據商品 slug 獲取單一商品詳情。
- **`GET /api/categories`**: 獲取所有商品分類列表。
- **`GET /api/categories/{slug}`**: 獲取單一分類及其旗下商品。

### 部落格
- **`GET /api/blog`**: 獲取所有文章列表。
- **`GET /api/blog/{slug}`**: 根據文章 slug 獲取單一文章內容。

### 購物車/訂單（需認證）
- **`GET /api/cart`**: 獲取購物車內容，使用 Redis 儲存。
- **`POST /api/cart/add`**: 添加商品至購物車。
- **`POST /api/orders`**: 建立新訂單，包含訂單商品。
- **`GET /api/orders`**: 獲取目前使用者的所有訂單。
- **`GET /api/orders/{order_id}`**: 獲取單一訂單詳情。

## 關鍵代碼展示

以下是倉庫中的核心代碼片段，附上中文註釋，幫助開發者理解實現邏輯：

### 1. 購物車控制器（Redis 實現）
檔案：`backend/app/Http/Controllers/CartController.php`

```php
<?php
// backend/app/Http/Controllers/CartController.php
// 使用 Redis 儲存購物車數據，提供高效能的購物車功能。

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Redis;
use App\Models\Product;
use Illuminate\Support\Facades\DB;

class CartController extends Controller
{
    /**
     * 生成購物車的 Redis Key，根據用戶 ID 確保唯一性。
     * @param int $userId 使用者 ID
     * @return string Redis Key
     */
    protected function getCartKey(int $userId): string
    {
        return "cart:{$userId}";
    }
    
    /**
     * 取得購物車內容，從 Redis 讀取並計算總額。
     * @param \Illuminate\Http\Request $request HTTP 請求
     * @return \Illuminate\Http\JsonResponse 購物車內容與總額
     */
    public function index(Request $request)
    {
        $cartKey = $this->getCartKey($request->user()->id);
        $cart = Redis::hgetall($cartKey); // 從 Redis 獲取購物車數據
        
        $items = [];
        $total = 0;
        foreach ($cart as $productId => $quantity) {
            $product = Product::find($productId);
            if ($product) {
                $subtotal = $product->price * $quantity;
                $total += $subtotal;
                $items[] = [
                    'product_id' => $productId,
                    'product_name' => $product->name,
                    'quantity' => (int) $quantity,
                    'price' => $product->price,
                    'subtotal' => $subtotal,
                ];
            }
        }
        
        return response()->json([
            'items' => $items,
            'total' => $total,
        ]);
    }
    
    /**
     * 新增商品到購物車，使用 Redis 的 hincrby 確保原子操作。
     * @param \Illuminate\Http\Request $request HTTP 請求
     * @return \Illuminate\Http\JsonResponse 操作結果
     */
    public function add(Request $request)
    {
        $request->validate([
            'product_id' => 'required|exists:products,id',
            'quantity' => 'required|integer|min:1',
        ]);
        
        $cartKey = $this->getCartKey($request->user()->id);
        $productId = $request->input('product_id');
        $quantity = $request->input('quantity');
        
        Redis::hincrby($cartKey, $productId, $quantity); // 原子增量操作，確保高併發安全
        
        return response()->json(['message' => '商品已成功加入購物車。']);
    }
}
```

**說明**：
- 使用 Redis 雜湊結構儲存購物車，支援快速讀寫。
- `hincrby` 確保原子性，避免多用戶同時操作的衝突。
- 結合 Laravel 驗證，檢查商品 ID 和數量有效性。

### 2. 登入頁面（JWT 認證）
檔案：`frontend/pages/login.vue`

```vue
<!-- frontend/pages/login.vue -->
<template>
  <div class="container mx-auto p-8">
    <h1 class="text-3xl font-bold mb-4">登入</h1>
    <form @submit.prevent="login" class="max-w-md">
      <div class="mb-4">
        <label for="email" class="block text-gray-700">電子郵件</label>
        <input v-model="form.email" id="email" type="email" placeholder="輸入電子郵件" class="border p-2 w-full rounded">
      </div>
      <div class="mb-4">
        <label for="password" class="block text-gray-700">密碼</label>
        <input v-model="form.password" id="password" type="password" placeholder="輸入密碼" class="border p-2 w-full rounded">
      </div>
      <button type="submit" class="bg-indigo-600 text-white py-2 px-4 rounded-lg hover:bg-indigo-700 transition-colors">
        登入
      </button>
    </form>
  </div>
</template>

<script setup>
import { ref } from 'vue';
import { useRouter } from 'vue-router';

// 取得 Nuxt 運行時配置與路由器
const config = useRuntimeConfig();
const router = useRouter();
const form = ref({ email: '', password: '' });

/**
 * 處理使用者登入，呼叫後端 API 並儲存 JWT token。
 * 使用 $fetch 進行非同步請求，成功後將 token 儲存至 localStorage。
 */
const login = async () => {
  try {
    const response = await $fetch(`${config.public.apiBaseUrl}/login`, {
      method: 'POST',
      body: form.value,
    });
    localStorage.setItem('petjoy_jwt_token', response.access_token); // 儲存 JWT token
    alert('登入成功！');
    router.push('/products'); // 跳轉至商品頁面
  } catch (err) {
    console.error('登入失敗:', err);
    alert('登入失敗，請檢查憑證。');
  }
};
</script>
```

**說明**：
- 使用 Nuxt 3 的 `$fetch` 與後端 `/api/login` 互動，處理登入邏輯。
- JWT token 儲存於 `localStorage`，用於後續認證請求。
- Tailwind CSS 實現響應式設計，提升頁面美觀性。

## 常見問題 (FAQ)

以下是關於 PetJoyEcommerce 專案的常見技術問題與解答，幫助開發者理解設計決策：

### **Q1: 為什麼選擇 Redis 作為購物車的儲存方案？**
**A**: Redis 是一個高效的記憶體資料庫，適合處理高併發的購物車操作。它的雜湊結構（`hset`/`hincrby`）支援快速讀寫與原子操作，避免多用戶同時操作時的資料衝突。相比資料庫儲存，Redis 減少了 I/O 開銷，確保高峰流量下的性能。例如，`CartController::add` 使用 `hincrby` 實現原子增量。

### **Q2: 如何實現 JWT 認證？**
**A**: 後端使用 `tymon/jwt-auth` 套件生成和驗證 JWT token。用戶透過 `/api/login` 提交憑證，後端驗證後回傳 token（見 `AuthController::login`）。前端在 `login.vue` 中將 token 儲存於 `localStorage`，並在後續 API 請求（如加入購物車）中透過 `Authorization: Bearer` 標頭傳送。Laravel 的 `auth:api` 中介層驗證 token 有效性。

### **Q3: 為什麼使用 Nuxt 3 的伺服器端渲染 (SSR)？**
**A**: SSR 提高頁面初次載入速度，並透過動態 `useHead` 設置 meta 標籤（如 OG/Twitter），優化 SEO。例如，`products/[slug].vue` 動態生成商品頁的標題和描述，提升搜尋引擎排名，同時提供流暢的用戶體驗。

### **Q4: 如何確保 Docker 部署的環境一致性？**
**A**: 使用 Docker Compose 定義 Laravel、Nuxt、Nginx、MySQL 和 Redis 服務，透過 `docker-compose.yml` 統一配置環境變數和依賴。Nginx 作為反向代理，處理前端和 API 請求，並透過卷宗綁定程式碼，確保開發與生產環境一致。

### **Q5: 為什麼選擇 PlanetScale 作為資料庫選項？**
**A**: PlanetScale 提供無伺服器的 MySQL 相容資料庫，支援自動擴展和高可用性，適合電商平台。專案使用 `mysql_aurora` 驅動連接到 PlanetScale，透過 DSN 配置（如 `mysql://user:pscale_pw_xxx@aws.connect.psdb.cloud/petjoy_ecommerce`）簡化連線管理。

### **Q6: 如何處理高併發的訂單建立？**
**A**: 訂單建立（`OrderController::store`）使用 Laravel 的事務（`DB::beginTransaction`）確保資料一致性。購物車數據從 Redis 轉移到 MySQL 時，檢查商品有效性並計算總額，失敗則回滾，防止資料不一致。

### **Q7: PWA 如何提升用戶體驗？**
**A**: 透過 `@vite-pwa/nuxt`，專案支援離線存取和應用程式安裝。`nuxt.config.ts` 配置 manifest 與圖示（如 48x48、512x512 PNG），讓用戶可在行動裝置上像使用原生應用程式般操作，提升便捷性。

## 專案設置與上傳

**注意**：GitHub 倉庫包含核心檔案（如 `CartController.php`、`login.vue`、Docker 配置等）。其他基本代碼（如 Laravel 的 `composer.json` 或 Nuxt 的 `package.json`）需手動新增。

1. **準備環境**：
   - 確保已安裝 PHP 8.2、Node.js 18、Composer 和 Docker。
   - 複製倉庫到本地：
     ```bash
     git clone https://github.com/BpsEason/PetJoyEcommerce.git
     cd PetJoyEcommerce
     ```

2. **手動新增基本代碼**：
   - **前端**：在 `frontend/` 建立 `package.json`，包含以下依賴：
     ```json
     {
       "name": "petjoy-frontend",
       "version": "1.0.0",
       "private": true,
       "scripts": {
         "dev": "nuxt",
         "build": "nuxt build",
         "start": "nuxt start",
         "test": "vitest"
       },
       "dependencies": {
         "nuxt": "^3.0.0",
         "@nuxtjs/tailwindcss": "^6.0.0",
         "@vite-pwa/nuxt": "^0.0.7",
         "marked": "^4.0.0"
       },
       "devDependencies": {
         "vitest": "^0.25.0"
       }
     }
     ```
   - **後端**：在 `backend/` 建立 `composer.json`，包含以下依賴：
     ```json
     {
       "name": "petjoy/backend",
       "description": "PetJoy Ecommerce Backend",
       "require": {
         "php": "^8.2",
         "laravel/framework": "^10.0",
         "tymon/jwt-auth": "^2.0"
       },
       "require-dev": {
         "phpunit/phpunit": "^9.5"
       }
     }
     ```

3. **安裝依賴**：
   - 進入 `frontend/`，執行：
     ```bash
     npm install
     ```
   - 進入 `backend/`，執行：
     ```bash
     composer install
     ```

4. **配置環境**：
   - 複製 `backend/.env.example` 為 `backend/.env`，並根據需求修改。
   - **PlanetScale 配置**：
     1. 在 PlanetScale 創建資料庫，取得連線 DSN（格式：`mysql://user:pscale_pw_xxx@aws.connect.psdb.cloud/petjoy_ecommerce?ssl-mode=REQUIRED`）。
     2. 在 `backend/.env` 中設置：
        ```env
        DB_CONNECTION=mysql_aurora
        DATABASE_URL=mysql://user:pscale_pw_xxx@aws.connect.psdb.cloud/petjoy_ecommerce?ssl-mode=REQUIRED
        ```
     3. 確認 Laravel 已包含 `mysql_aurora` 驅動。

5. **PWA 圖示**：
   - 在 `frontend/public/icons/` 提供 PNG 格式圖示（尺寸：48x48、72x72、96x96、144x144、192x192、512x512）。
   - 建議使用線上工具（如 favicon.io）生成。

6. **生成金鑰**：
   - 在 `backend/` 執行：
     ```bash
     php artisan key:generate
     php artisan jwt:secret
     ```

7. **資料庫遷移**：
   - 執行：
     ```bash
     php artisan migrate
     ```

8. **啟動服務**：
   - 在專案根目錄執行：
     ```bash
     docker-compose up -d
     ```

9. **上傳至 GitHub**（如需更新）：
   ```bash
   git add .
   git commit -m "Update PetJoyEcommerce"
   git push origin main
   ```

## 設計理念

PetJoyEcommerce 旨在提供一個高效能、可擴展的電商平台，透過以下技術實現：
- **高效能**：Redis 處理購物車，Nginx 快取靜態檔案，優化響應速度。
- **安全性**：JWT 認證與 Laravel 驗證保護 API 和用戶數據。
- **可擴展性**：模組化設計與 Docker 化部署，支援快速迭代。
- **用戶體驗**：Nuxt 3 的 SSR 和 PWA 提供快速且友好的瀏覽體驗。

此專案適合開發者學習現代全端開發技術，包括前端渲染、後端 API、容器化部署與資料庫管理。
