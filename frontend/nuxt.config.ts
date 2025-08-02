// frontend/nuxt.config.ts
// 此檔案用於配置 Nuxt 專案的行為，新增了 PWA 模組。
export default defineNuxtConfig({
  ssr: true,
  
  // 啟用 Tailwind CSS 和 PWA 模組
  modules: [
    '@nuxtjs/tailwindcss',
    '@vite-pwa/nuxt',
  ],
  
  // 設置環境變數
  runtimeConfig: {
    public: {
      apiBaseUrl: process.env.NUXT_PUBLIC_API_BASE_URL || 'http://localhost/api',
      appUrl: process.env.NUXT_PUBLIC_APP_URL || 'http://localhost'
    },
  },

  // PWA 模組配置
  pwa: {
    manifest: {
      name: 'PetJoy Ecommerce',
      short_name: 'PetJoy',
      description: '為您的寵物帶來歡樂的電商平台',
      theme_color: '#4f46e5',
      background_color: '#ffffff',
      display: 'standalone',
      icons: [
        {
          src: '/icons/icon_48x48.png',
          sizes: '48x48',
          type: 'image/png',
        },
        {
          src: '/icons/icon_72x72.png',
          sizes: '72x72',
          type: 'image/png',
        },
        {
          src: '/icons/icon_96x96.png',
          sizes: '96x96',
          type: 'image/png',
        },
        {
          src: '/icons/icon_144x144.png',
          sizes: '144x144',
          type: 'image/png',
        },
        {
          src: '/icons/icon_192x192.png',
          sizes: '192x192',
          type: 'image/png',
        },
        {
          src: '/icons/icon_512x512.png',
          sizes: '512x512',
          type: 'image/png',
        },
      ],
    },
  },
});
