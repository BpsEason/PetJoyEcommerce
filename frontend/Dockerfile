# frontend/Dockerfile
# 此 Dockerfile 用於建立 Nuxt 前端服務的 Docker 映像檔。

# 使用 Node.js 18 映像檔
FROM node:18-alpine

# 設定工作目錄
WORKDIR /app

# 複製 package.json 和 package-lock.json
COPY package*.json ./

# 安裝 Node.js 依賴
RUN npm install

# 複製專案程式碼
COPY . .

# 建立 Nuxt 應用程式
RUN npm run build

# 暴露埠號
EXPOSE 3000

# 啟動 Nuxt 服務
CMD ["npm", "run", "start"]
