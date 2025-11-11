<template>
  <form @submit.prevent="submit" class="space-y-6">
    <div>
      <label for="title" class="form-label">Post Title</label>
      <input v-model="form.title" type="text" id="title" class="form-input" placeholder="An Awesome Blog Post Title" required />
    </div>

    <div>
      <label class="form-label">Featured Image</label>
      <div class="mt-2 flex items-center gap-4">
        <img :src="previewImage || defaultImage" alt="Image preview" class="w-48 h-32 object-cover rounded-lg bg-gray-100">
        <label for="image" class="cursor-pointer bg-white py-2 px-4 border border-gray-300 rounded-md shadow-sm text-sm font-medium text-gray-700 hover:bg-gray-50">
          <span>Upload Image</span>
          <input id="image" @change="onFileChange" type="file" class="sr-only" accept="image/*">
        </label>
      </div>
    </div>

    <div>
      <label for="excerpt" class="form-label">Excerpt</label>
      <textarea v-model="form.excerpt" id="excerpt" rows="3" class="form-input" placeholder="A short, catchy summary of your post..." required></textarea>
    </div>

    <div>
      <label for="content" class="form-label">Full Content (HTML supported)</label>
      <textarea v-model="form.content" id="content" rows="12" class="form-input" placeholder="Write your full article here. You can use <h2> for headings and <p> for paragraphs." required></textarea>
    </div>

    <div>
      <label class="form-label">Categories (Select at least one)</label>
      <div v-if="categoryStore.loading && categoryStore.categories.length === 0" class="text-gray-500">Loading categories...</div>
      <div v-else class="mt-2 grid grid-cols-2 sm:grid-cols-4 gap-4 p-4 border rounded-md">
        <div v-for="category in categoryStore.categories" :key="category.id" class="flex items-center">
          <input :id="`category-${category.id}`" :value="category.id" v-model="form.category_ids" type="checkbox" class="h-4 w-4 text-teal-600 border-gray-300 rounded focus:ring-teal-500">
          <label :for="`category-${category.id}`" class="ml-3 text-sm text-gray-700 select-none">{{ category.name }}</label>
        </div>
      </div>
    </div>

    <div>
      <label for="tags" class="form-label">Tags (comma separated)</label>
      <input v-model="tagsText" type="text" id="tags" class="form-input" placeholder="cooking, healthy, vegan" />
    </div>

    <div class="pt-4 flex gap-4">
      <button type="submit" :disabled="isLoading" class="flex-1 flex justify-center py-3 px-4 rounded-md shadow-sm text-sm font-medium text-white bg-teal-600 hover:bg-teal-700 disabled:bg-gray-400">
        {{ isLoading ? 'Saving...' : 'Save Post' }}
      </button>
      <router-link v-if="postSlug" :to="`/blog/${postSlug}`" class="flex-1 flex justify-center items-center py-3 px-4 rounded-md shadow-sm text-sm font-medium text-gray-700 bg-gray-100 hover:bg-gray-200">
        Cancel
      </router-link>
    </div>
  </form>
</template>

<script setup>
import { ref, watch, onMounted } from 'vue';
import { useCategoryStore } from '@/store/categoryStore';
import { useToast } from 'vue-toastification';

const props = defineProps({
  post: { type: Object, default: null },
  isLoading: { type: Boolean, default: false }
});

const emit = defineEmits(['submit']);
const categoryStore = useCategoryStore();
const toast = useToast();

const defaultImage = '/No_Image.jpg';
const previewImage = ref(null);

const form = ref({
  title: '',
  excerpt: '',
  content: '',
  category_ids: [],
  image: null,
});
const tagsText = ref('');
const postSlug = ref(null);

onMounted(() => {
    if (categoryStore.categories.length === 0) {
        categoryStore.fetchCategories();
    }
});

watch(() => props.post, (newPost) => {
  if (newPost) {
    form.value.title = newPost.title;
    form.value.excerpt = newPost.excerpt;
    form.value.content = newPost.content;
    form.value.category_ids = newPost.categories?.map(cat => cat.id) || [];
    tagsText.value = newPost.tags?.map(tag => tag.name).join(', ') || '';
    previewImage.value = newPost.image_url;
    postSlug.value = newPost.slug;
  }
}, { immediate: true, deep: true });

const onFileChange = (e) => {
  const file = e.target.files[0];
  if (file) {
    form.value.image = file;
    previewImage.value = URL.createObjectURL(file);
  }
};

const submit = () => {
  if (form.value.category_ids.length === 0) {
    toast.error('Please select at least one category.');
    return;
  }
  
  const formData = new FormData();
  Object.entries(form.value).forEach(([key, value]) => {
    if (key === 'category_ids') {
      value.forEach(id => formData.append('category_ids[]', id));
    } else if (value) {
      formData.append(key, value);
    }
  });

  if (tagsText.value) {
    tagsText.value.split(',').forEach(tag => {
      if (tag.trim()) formData.append('tags[]', tag.trim());
    });
  }

  emit('submit', formData);
};
</script>

<style scoped>
.form-label { @apply block text-sm font-medium text-gray-800 mb-1; }
.form-input { @apply block w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm placeholder-gray-400 focus:outline-none focus:ring-teal-500 focus:border-teal-500 sm:text-sm; }
</style>