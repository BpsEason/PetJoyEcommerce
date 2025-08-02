# PetJoyEcommerce 專案

這是一個使用 Nuxt 3 (前端) 和 Laravel 10 (後端) 構建的寵物電商平台最小可行性產品 (MVP)。此專案框架包含 PWA、增強 SEO、JWT 認證、Redis 購物車和 Docker 容器化配置。

## 目錄結構

- `frontend/`: Nuxt 3 前端程式碼。
- `backend/`: Laravel 10 後端 API 程式碼。
- `docker/`: Docker Compose 與服務配置檔案。
- `nginx/`: Nginx 代理配置檔。
- `.github/workflows/`: CI/CD 自動化工作流程。

## API 端點

以下是後端 API 的關鍵端點：

### 認證
- **`POST /api/register`**: 註冊新使用者。
- **`POST /api/login`**: 使用電子郵件和密碼登入。
- **`POST /api/logout`**: 登出使用者 (需認證)。

### 商品與分類
- **`GET /api/products`**: 獲取所有商品列表。
- **`GET /api/products/{slug}`**: 根據商品的 slug 獲取單一商品詳細資訊。
- **`GET /api/categories`**: 獲取所有商品分類列表。
- **`GET /api/categories/{slug}`**: 根據分類的 slug 獲取單一分類及其旗下商品。

### 部落格
- **`GET /api/blog`**: 獲取所有文章列表。
- **`GET /api/blog/{slug}`**: 根據文章的 slug 獲取單一文章內容。

### 購物車/訂單 (需認證)
- **`GET /api/cart`**: 獲取購物車內容。
- **`POST /api/cart/add`**: 添加商品到購物車。
- **`POST /api/orders`**: 建立新訂單。
- **`GET /api/orders`**: 獲取目前使用者的所有訂單。
- **`GET /api/orders/{order_id}`**: 獲取單一訂單詳情。

## 專案設置與上傳

1. **執行腳本**: 執行此 bash 腳本，它會生成所有必要檔案。
2. **安裝依賴**: 進入 `frontend/` 和 `backend/` 目錄，分別執行 `npm install` 和 `composer install`。
3. **配置環境**: 在 `backend/` 目錄中，複製 `.env.example` 為 `.env`，並根據您的需求（例如使用外部資料庫）修改配置。
   - **外部資料庫指引 (PlanetScale)**:
     1. 在 PlanetScale 創建資料庫，取得連線資訊（從 PlanetScale 控制台的 "Connect" 選項複製 DSN）。
     2. 在 `backend/.env` 中設置以下內容：
        ```
        DB_CONNECTION=mysql_aurora
        DATABASE_URL=mysql://user:pscale_pw_xxx@aws.connect.psdb.cloud/petjoy_ecommerce?ssl-mode=REQUIRED
        ```
     3. 確保已安裝 `mysql_aurora` 驅動（包含於 Laravel）。
4. **PWA 圖示**: 在 `frontend/public/icons/` 中提供以下尺寸的圖示檔案（PNG 格式）：48x48、72x72、96x96、144x144、192x192、512x512。可使用線上工具（如 favicon.io）生成。
5. **生成應用程式金鑰**: 在 `backend/` 目錄中執行 `php artisan key:generate` 和 `php artisan jwt:secret`。
6. **資料庫遷移**: 執行 `php artisan migrate`。
7. **上傳至 GitHub**: 執行以下命令：
   ```bash
   git init
   git add .
   git commit -m "Initial commit"
   git remote add origin https://github.com/your-username/PetJoyEcommerce.git
   git push -u origin main
   ```
