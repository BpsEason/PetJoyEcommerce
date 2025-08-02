<!-- frontend/pages/products/[slug].vue -->
<template>
  <div v-if="pending" class="flex justify-center items-center h-screen">
    <p class="text-xl font-bold text-gray-700">載入中...</p>
  </div>
  <div v-else-if="error" class="flex justify-center items-center h-screen">
    <p class="text-xl font-bold text-red-500">無法載入商品資訊。</p>
  </div>
  <div v-else-if="product" class="container mx-auto p-8">
    <div class="flex flex-col lg:flex-row gap-8">
      <div class="lg:w-1/2">
        <img :src="product.image_url" :alt="product.name" class="w-full h-auto rounded-lg shadow-lg object-cover" />
      </div>
      <div class="lg:w-1/2 flex flex-col justify-center">
        <h1 class="text-4xl font-extrabold text-gray-900 mb-4">{{ product.name }}</h1>
        <p class="text-2xl font-bold text-indigo-600 mb-4">NT$ {{ product.price }}</p>
        <div class="prose text-gray-700 mb-6" v-html="product.description"></div>
        <button
          class="bg-indigo-600 text-white font-semibold py-3 px-6 rounded-lg shadow-md hover:bg-indigo-700 transition-colors"
          @click="addToCart(product.id)"
        >
          加入購物車
        </button>
      </div>
    </div>
  </div>
  <div v-else class="flex justify-center items-center h-screen">
    <p class="text-xl font-bold text-gray-700">商品不存在。</p>
  </div>
</template>

<script setup>
import { useRoute } from 'vue-router';
import { useAsyncData } from '#app';

const route = useRoute();
const slug = route.params.slug;

const config = useRuntimeConfig();
const API_URL = config.public.apiBaseUrl;

const { data: product, pending, error } = await useAsyncData(
  `product-${slug}`,
  () => $fetch(`${API_URL}/products/${slug}`),
  {
    lazy: true,
    server: true,
  }
);

/**
 * 將商品加入購物車，使用 JWT 進行認證。
 * @param {number} productId - 商品的 ID。
 */
const addToCart = async (productId) => {
  const token = localStorage.getItem('petjoy_jwt_token');

  if (!token) {
    alert('請先登入才能將商品加入購物車！');
    return;
  }

  try {
    const response = await $fetch(`${API_URL}/cart/add`, {
      method: 'POST',
      headers: {
        'Authorization': `Bearer ${token}`,
        'Content-Type': 'application/json',
      },
      body: JSON.stringify({
        product_id: productId,
        quantity: 1 // 預設數量為 1
      }),
    });
    console.log('商品已成功加入購物車:', response.message);
    alert('商品已成功加入購物車！');
  } catch (err) {
    console.error('加入購物車失敗:', err);
    alert('加入購物車失敗，請稍後再試。');
  }
};

// 增強 SEO，加入 OG 和 Twitter 標籤
useHead(() => ({
  title: product.value ? product.value.name : '商品詳情',
  meta: [
    { name: 'description', content: product.value ? product.value.description.substring(0, 150) : '寵物商品詳情' },
    { property: 'og:title', content: product.value ? product.value.name : '商品詳情' },
    { property: 'og:description', content: product.value ? product.value.description.substring(0, 150) : '寵物商品詳情' },
    { property: 'og:image', content: product.value ? product.value.image_url : 'https://placehold.co/1200x630' },
    { property: 'og:url', content: () => `${config.public.appUrl}/products/${slug}` },
    { name: 'twitter:card', content: 'summary_large_image' },
    { name: 'twitter:title', content: product.value ? product.value.name : '商品詳情' },
    { name: 'twitter:description', content: product.value ? product.value.description.substring(0, 150) : '寵物商品詳情' },
    { name: 'twitter:image', content: product.value ? product.value.image_url : 'https://placehold.co/1200x675' },
  ]
}));
</script>
