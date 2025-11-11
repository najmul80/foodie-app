<template>
  <Layout>
    <div class="max-w-4xl mx-auto py-12 px-4 sm:px-6 lg:px-8">
      <div class="bg-white rounded-xl shadow-lg p-8">
        <h1 class="text-3xl font-bold text-gray-900 mb-2">Add a New Recipe</h1>
        <p class="text-gray-600 mb-8">Share your delicious creation with the Foodieland community!</p>

        <form @submit.prevent="handleCreateRecipe" class="space-y-6">
          <!-- Title -->
          <div>
            <label for="title" class="form-label">Recipe Title</label>
            <input v-model="form.title" type="text" id="title" class="form-input" placeholder="e.g., Spicy Chicken Curry" required />
          </div>

          <!-- Description -->
          <div>
            <label for="description" class="form-label">Description</label>
            <textarea v-model="form.description" id="description" rows="4" class="form-input" placeholder="Tell us a little about your recipe..." required></textarea>
          </div>

          <!-- Image Upload -->
          <div>
            <label class="form-label">Recipe Image</label>
            <div class="mt-2 flex items-center gap-4">
              <img :src="previewImage || defaultPlaceholderImage" alt="Image preview" class="w-32 h-32 object-cover rounded-lg bg-gray-100">
              <label for="image" class="cursor-pointer bg-white py-2 px-4 border border-gray-300 rounded-md shadow-sm text-sm font-medium text-gray-700 hover:bg-gray-50">
                <span>Upload a file</span>
                <input id="image" @change="onFileChange" type="file" class="sr-only">
              </label>
            </div>
          </div>
          
          <!-- Prep Time, Cook Time, Difficulty -->
          <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
            <div>
              <label for="prep_time" class="form-label">Prep Time (minutes)</label>
              <input v-model.number="form.prep_time" type="number" id="prep_time" class="form-input" required />
            </div>
            <div>
              <label for="cook_time" class="form-label">Cook Time (minutes)</label>
              <input v-model.number="form.cook_time" type="number" id="cook_time" class="form-input" required />
            </div>
            <div>
              <label for="difficulty" class="form-label">Difficulty</label>
              <select v-model="form.difficulty" id="difficulty" class="form-input" required>
                <option>Easy</option>
                <option>Medium</option>
                <option>Hard</option>
              </select>
            </div>
          </div>

          <!-- Categories -->
          <div>
            <label class="form-label">Categories</label>
            <div v-if="categoryStore.loading">Loading categories...</div>
            <div v-else class="mt-2 grid grid-cols-2 sm:grid-cols-4 gap-4">
              <div v-for="category in categoryStore.categories" :key="category.id" class="flex items-center">
                <input :id="`category-${category.id}`" :value="category.id" v-model="form.category_ids" type="checkbox" class="h-4 w-4 text-teal-600 border-gray-300 rounded focus:ring-teal-500">
                <label :for="`category-${category.id}`" class="ml-3 text-sm text-gray-700">{{ category.name }}</label>
              </div>
            </div>
          </div>
          
          <!-- Ingredients -->
          <div>
            <label class="form-label">Ingredients</label>
            <p class="text-xs text-gray-500 mb-2">Add one ingredient per line. e.g., "1 cup flour"</p>
            <textarea v-model="ingredientsText" rows="6" class="form-input" placeholder="1 cup flour&#10;2 large eggs&#10;1 tsp baking soda"></textarea>
          </div>

          <!-- Instructions -->
          <div>
            <label class="form-label">Instructions</label>
             <p class="text-xs text-gray-500 mb-2">Add one step per line.</p>
            <textarea v-model="instructionsText" rows="8" class="form-input" placeholder="1. Mix all dry ingredients...&#10;2. Add eggs and mix well...&#10;3. Bake for 30 minutes..."></textarea>
          </div>
          
          <!-- Tags -->
          <div>
            <label for="tags" class="form-label">Tags</label>
            <p class="text-xs text-gray-500 mb-2">Separate tags with a comma. e.g., "chicken, spicy, dinner"</p>
            <input v-model="tagsText" type="text" id="tags" class="form-input" placeholder="chicken, spicy, dinner" />
          </div>

          <!-- Submit Button -->
          <div class="pt-4">
            <button type="submit" :disabled="isLoading" class="w-full flex justify-center py-3 px-4 border border-transparent rounded-md shadow-sm text-sm font-medium text-white bg-gray-900 hover:bg-gray-800 disabled:bg-gray-400 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-gray-800">
              {{ isLoading ? 'Submitting...' : 'Submit Recipe' }}
            </button>
          </div>
        </form>
      </div>
    </div>
  </Layout>
</template>

<script setup>
import { ref, onMounted } from 'vue';
import { useRouter } from 'vue-router';
import Layout from '@/components/Layout.vue';
import { useCategoryStore } from '@/store/categoryStore';
import ApiService from '@/services/ApiService';
import { useToast } from 'vue-toastification';

const router = useRouter();
const categoryStore = useCategoryStore();
const toast = useToast();

const defaultPlaceholderImage = '/No_Image.jpg';

const form = ref({
  title: '',
  description: '',
  prep_time: null,
  cook_time: null,
  difficulty: 'Easy',
  category_ids: [],
  image: null,
});

const ingredientsText = ref('');
const instructionsText = ref('');
const tagsText = ref('');

const previewImage = ref(null);
const isLoading = ref(false);

onMounted(() => {
  categoryStore.fetchCategories();
});

const onFileChange = (e) => {
  const file = e.target.files[0];
  if (file) {
    form.value.image = file;
    previewImage.value = URL.createObjectURL(file);
  }
};

const handleCreateRecipe = async () => {
  isLoading.value = true;
  
  const formData = new FormData();
  
  Object.entries(form.value).forEach(([key, value]) => {
    if (key === 'category_ids') {
      value.forEach(id => formData.append('category_ids[]', id));
    } else if (value) {
      formData.append(key, value);
    }
  });

  if (ingredientsText.value) {
    ingredientsText.value.split('\n').forEach(ing => {
      if(ing.trim()) formData.append('ingredients[]', ing.trim());
    });
  }

  if (instructionsText.value) {
    instructionsText.value.split('\n').forEach(inst => {
      if(inst.trim()) formData.append('instructions[]', inst.trim());
    });
  }
  
  if (tagsText.value) {
    tagsText.value.split(',').forEach(tag => {
      if(tag.trim()) formData.append('tags[]', tag.trim());
    });
  }
  
  try {
    const response = await ApiService.post('/recipes', formData, {
      headers: {
        'Content-Type': 'multipart/form-data',
      },
    });
    
    toast.success('Recipe created successfully!');
    router.push(`/recipe/${response.data.data.slug}`);

  } catch (error) {
    console.error('Recipe creation failed:', error);
    toast.error(error.response?.data?.message || 'Failed to create recipe. Please check your input.');
  } finally {
    isLoading.value = false;
  }
};
</script>

<style scoped>
.form-label {
  @apply block text-sm font-medium text-gray-800 mb-1;
}
.form-input {
  @apply block w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm placeholder-gray-400 focus:outline-none focus:ring-teal-500 focus:border-teal-500 sm:text-sm;
}
</style>