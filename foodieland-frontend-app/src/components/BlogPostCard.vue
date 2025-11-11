<template>
  <div class="flex gap-6">
    <img :src="image" :alt="title" class="w-40 h-32 object-cover rounded-lg flex-shrink-0" />
    
    <div class="flex-1">
      <h3 class="text-xl font-bold text-gray-900 mb-2 hover:text-teal-600 cursor-pointer transition">
        {{ title }}
      </h3>
      <p class="text-gray-600 text-sm mb-4">{{ excerpt }}</p>
      
      <div class="flex items-center gap-3">
        <img src="@/assets/images/1.png" alt="Author" class="w-8 h-8 rounded-full" />
        <div class="text-sm">
          <p class="font-semibold text-gray-900">{{ author }}</p>
          <p class="text-gray-500">{{ date }}</p>
        </div>
      </div>
    </div>
  </div>
</template>

<script setup lang="ts">
interface Props {
  title: string
  excerpt: string
  author: string
  date: string
  image: string
}

defineProps<Props>()
</script>
