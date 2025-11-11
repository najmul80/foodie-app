<template>
  <div class="mt-16 bg-white rounded-xl p-6 shadow-sm">
    <h2 class="text-2xl font-bold text-gray-900 mb-6">
      Comments ({{ comments.length }})
    </h2>

    <!-- ========================================================= -->
    <!-- THIS IS THE FORM WHERE USERS WILL WRITE COMMENTS -->
    <!-- It is only visible if the user is logged in -->
    <!-- ========================================================= -->
    <div v-if="authStore.isAuthenticated" class="mb-8">
      <form @submit.prevent="submitComment">
        <label for="comment-body" class="sr-only">Your Comment</label>
        <textarea 
          v-model="newComment" 
          id="comment-body"
          placeholder="Write your comment here..."
          class="w-full p-4 border border-gray-300 rounded-lg focus:ring-2 focus:ring-teal-500 focus:border-transparent transition"
          rows="4"
          required
        ></textarea>
        
        <div class="flex justify-end mt-4">
          <button 
            type="submit" 
            :disabled="isSubmitting"
            class="px-6 py-3 bg-gray-900 text-white font-semibold rounded-lg hover:bg-gray-800 disabled:bg-gray-400 disabled:cursor-not-allowed transition-colors"
          >
            {{ isSubmitting ? 'Posting...' : 'Post Comment' }}
          </button>
        </div>
      </form>
    </div>
    
    <!-- This message is shown if the user is NOT logged in -->
    <div v-else class="mb-8 p-6 bg-gray-50 rounded-lg text-center">
      <p class="text-gray-700">Please <router-link to="/login" class="text-teal-600 font-semibold hover:underline">log in</router-link> or <router-link to="/register" class="text-teal-600 font-semibold hover:underline">register</router-link> to post a comment.</p>
    </div>

    <!-- This is the list of existing comments -->
    <div class="space-y-6">
      <div v-if="comments.length === 0 && !authStore.isAuthenticated" class="text-gray-500 text-center py-4">
        No comments yet.
      </div>
       <div v-if="comments.length === 0 && authStore.isAuthenticated" class="text-gray-500 text-center py-4">
        No comments yet. Be the first one to share your thoughts!
      </div>
      
      <div v-for="comment in comments" :key="comment.id" class="flex items-start gap-4 p-4 border-b border-gray-100 last:border-b-0">
        <img :src="comment.author?.profile_image_url || defaultAvatar" alt="Author" class="w-12 h-12 rounded-full object-cover flex-shrink-0">
        <div class="flex-1">
          <div class="flex justify-between items-start">
            <div>
              <p class="font-semibold text-gray-900">{{ comment.author?.name || 'Unknown' }}</p>
              <p class="text-sm text-gray-500 mb-2">{{ comment.created_at }}</p>
            </div>
            <button 
              v-if="authStore.currentUser?.id === comment.author?.id"
              @click="handleDeleteComment(comment.id)"
              class="text-xs text-red-500 hover:text-red-700 hover:underline opacity-75 hover:opacity-100 transition"
            >
              Delete
            </button>
          </div>
          <p class="text-gray-800 leading-relaxed whitespace-pre-line">{{ comment.body }}</p>
        </div>
      </div>
    </div>
  </div>
</template>

<script setup>
import { ref } from 'vue';
import { useAuthStore } from '@/store/authStore';
import { useToast } from 'vue-toastification';
import { showConfirmation } from '@/utils/confirmation';

const props = defineProps({
  comments: {
    type: Array,
    required: true,
  },
  postSlug: {
    type: String,
    required: true,
  },
  store: {
    type: Object,
    required: true,
  },
});

const authStore = useAuthStore();
const toast = useToast();

const newComment = ref('');
const isSubmitting = ref(false);
const defaultAvatar = '/No_Image.jpg';

const submitComment = async () => {
  if (!newComment.value.trim()) {
    toast.error("Comment cannot be empty.");
    return;
  }

  isSubmitting.value = true;
  try {
    await props.store.addComment(props.postSlug, { body: newComment.value });
    newComment.value = '';
  } catch (error) {
    // Error toast is already handled inside the store action
  } finally {
    isSubmitting.value = false;
  }
};

const handleDeleteComment = async (commentId) => {
  const result = await showConfirmation("Delete this comment?", "This action cannot be undone.");
  if (result.isConfirmed) {
    try {
      await props.store.deleteComment(commentId);
    } catch (error) {
       // Error is handled by the store
    }
  }
};
</script>