// src/store/blogStore.js
import { defineStore } from 'pinia'
import ApiService from '../services/ApiService'
import { useToast } from 'vue-toastification'

export const useBlogStore = defineStore('blog', {
  state: () => ({
    posts: [],
    post: null,
    meta: {},
    loading: false,
    error: null,
  }),

  actions: {
    async fetchPosts(page = 1, search = '') {
      this.loading = true
      this.error = null
      try {
        const response = await ApiService.get(`/blog?page=${page}&search=${search}`)
        this.posts = response.data.data
        this.meta = response.data.meta
      } catch (error) {
        this.error = "Failed to fetch blog posts."
        console.error(error)
      } finally {
        this.loading = false
      }
    },

    async fetchPostBySlug(slug) {
      this.loading = true
      this.error = null
      this.post = null
      try {
        const response = await ApiService.get(`/blog/${slug}`)
        this.post = response.data.data
      } catch (error) {
        this.error = 'Failed to fetch the blog post.'
        console.error(error)
      } finally {
        this.loading = false
      }
    },

    // --- ADD THESE NEW ACTIONS ---

    async addComment(slug, commentData) {
      const toast = useToast();
      try {
        const response = await ApiService.post(`/blog/${slug}/comments`, commentData);
        if (this.post && this.post.slug === slug) {
          this.post.comments.unshift(response.data.data);
        }
        toast.success(response.data.message || 'Comment posted successfully!');
      } catch (error) {
        toast.error(error.response?.data?.message || 'Failed to post comment.');
        console.error("Failed to add comment:", error);
        throw error;
      }
    },

    async deleteComment(commentId) {
      const toast = useToast();
      try {
        const response = await ApiService.delete(`/comments/${commentId}`);
        if (this.post) {
          const index = this.post.comments.findIndex(c => c.id === commentId);
          if (index !== -1) {
            this.post.comments.splice(index, 1);
          }
        }
        toast.success(response.data.message || 'Comment deleted.');
      } catch (error) {
        toast.error(error.response?.data?.message || 'Failed to delete comment.');
        console.error("Failed to delete comment:", error);
        throw error;
      }
    },
  },
})