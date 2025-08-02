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

const config = useRuntimeConfig();
const router = useRouter();
const form = ref({ email: '', password: '' });

/**
 * 處理使用者登入並儲存 JWT token
 */
const login = async () => {
  try {
    const response = await $fetch(`${config.public.apiBaseUrl}/login`, {
      method: 'POST',
      body: form.value,
    });
    localStorage.setItem('petjoy_jwt_token', response.access_token);
    alert('登入成功！');
    router.push('/products');
  } catch (err) {
    console.error('登入失敗:', err);
    alert('登入失敗，請檢查憑證。');
  }
};
</script>
