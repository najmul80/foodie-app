<template>
    <Layout>
        <div class="text-center py-20">
            <h1 class="text-6xl font-bold text-gray-800">404</h1>
            <p class="text-2xl text-gray-600 mt-4">Page Not Found</p>
            <p class="mt-2 text-gray-500">Sorry, the page you are looking for does not exist.</p>
            <router-link to="/" class="mt-6 inline-block px-6 py-3 bg-teal-600 text-white rounded-lg hover:bg-teal-700">
                Go to Homepage
            </router-link>
        </div>
    </Layout>
</template>
<script setup>
import Layout from '@/components/Layout.vue'
</script>