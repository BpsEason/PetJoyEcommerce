<!-- frontend/pages/blog/[slug].vue -->
<template>
  <div v-if="pending" class="flex justify-center items-center h-screen">
    <p class="text-xl font-bold text-gray-700">載入中...</p>
  </div>
  <div v-else-if="error" class="flex justify-center items-center h-screen">
    <p class="text-xl font-bold text-red-500">無法載入文章資訊。</p>
  </div>
  <div v-else-if="post" class="container mx-auto p-8 max-w-4xl">
    <h1 class="text-5xl font-extrabold text-gray-900 mb-4">{{ post.title }}</h1>
    <p class="text-lg text-gray-500 mb-6">發布於: {{ new Date(post.created_at).toLocaleDateString() }}</p>
    <div class="prose lg:prose-xl text-gray-700 max-w-none">
      <div v-html="renderMarkdown(post.content)"></div>
    </div>
  </div>
  <div v-else class="flex justify-center items-center h-screen">
    <p class="text-xl font-bold text-gray-700">文章不存在。</p>
  </div>
</template>

<script setup>
import { useRoute } from 'vue-router';
import { useAsyncData } from '#app';
import { marked } from 'marked';

const route = useRoute();
const slug = route.params.slug;

const config = useRuntimeConfig();
const API_URL = config.public.apiBaseUrl;

const { data: post, pending, error } = await useAsyncData(
  `blog-post-${slug}`,
  () => $fetch(`${API_URL}/blog/${slug}`),
  {
    lazy: true,
    server: true,
  }
);

// 渲染 Markdown 內容
const renderMarkdown = (markdown) => {
  if (markdown) {
    return marked(markdown);
  }
  return '';
};

// 增強 SEO，加入 OG 和 Twitter 標籤
useHead(() => ({
  title: post.value ? post.value.title : '部落格文章',
  meta: [
    { name: 'description', content: post.value ? post.value.content.substring(0, 150) : '寵物部落格文章' },
    { property: 'og:title', content: post.value ? post.value.title : '部落格文章' },
    { property: 'og:description', content: post.value ? post.value.content.substring(0, 150) : '寵物部落格文章' },
    { property: 'og:image', content: post.value ? post.value.image_url : 'https://placehold.co/1200x630' },
    { property: 'og:url', content: () => `${config.public.appUrl}/blog/${slug}` },
    { name: 'twitter:card', content: 'summary_large_image' },
    { name: 'twitter:title', content: post.value ? post.value.title : '部落格文章' },
    { name: 'twitter:description', content: post.value ? post.value.content.substring(0, 150) : '寵物部落格文章' },
    { name: 'twitter:image', content: post.value ? post.value.image_url : 'https://placehold.co/1200x675' },
  ]
}));
</script>
