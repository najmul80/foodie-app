import { defineStore } from 'pinia';
import { useToast } from 'vue-toastification';
import ApiService from '@/services/ApiService';

export const useAuthStore = defineStore('auth', {
  state: () => {
    let user = null;
    let token = null;

    if (typeof window !== 'undefined') {
      const storedUser = localStorage.getItem('user');
      const storedToken = localStorage.getItem('token');

      try {
        if (storedUser && storedToken) {
          user = JSON.parse(storedUser);
          token = storedToken;
          ApiService.defaults.headers.common['Authorization'] = `Bearer ${token}`;
        }
      } catch (error) {
        console.error("Failed to parse user from localStorage:", error);
        localStorage.removeItem('user');
        localStorage.removeItem('token');
      }
    }

    return {
      user: user,
      token: token,
    };
  },

  getters: {
    isAuthenticated: (state) => !!state.token && !!state.user,
    currentUser: (state) => state.user,
    isFavorited: (state) => (recipeId) => {
      return state.user?.favorite_recipe_ids?.includes(recipeId) || false;
    }
  },

  actions: {
    setAuth(userData, token) {
      if (!userData.favorite_recipe_ids) {
        userData.favorite_recipe_ids = [];
      }
      this.user = userData;
      this.token = token;
      localStorage.setItem('user', JSON.stringify(userData));
      localStorage.setItem('token', token);
      ApiService.defaults.headers.common['Authorization'] = `Bearer ${token}`;
    },

    purgeAuth() {
      this.user = null;
      this.token = null;
      localStorage.removeItem('user');
      localStorage.removeItem('token');
      delete ApiService.defaults.headers.common['Authorization'];
    },

    async login(credentials) {
      const response = await ApiService.post('/login', credentials);
      this.setAuth(response.data.user, response.data.access_token);
      return response.data;
    },

    async register(credentials) {
      const response = await ApiService.post('/register', credentials);
      return response.data;
    },

    async verifyOtp(otpData) {
      const response = await ApiService.post('/verify-otp', otpData);
      this.setAuth(response.data.data.user, response.data.data.access_token);
      return response.data;
    },

    async resendOtp(email) {
      return ApiService.post('/resend-otp', { email });
    },

    async logout() {
      const toast = useToast();
      try {
        await ApiService.post('/logout');
        toast.info("You have been successfully logged out.");
      } catch (error) {
        console.error("Logout request failed, but logging out on client-side anyway.", error);
      } finally {
        this.purgeAuth();
      }
    },

    async updateProfile(formData) {
      const response = await ApiService.post('/user/profile', formData, {
        headers: { 'Content-Type': 'multipart/form-data' },
      });
      // The response 'data' is the UserResource, which is the user object
      this.setAuth(response.data.data, this.token); // Re-use setAuth to ensure consistency
      return response.data;
    },

    async fetchUser() {
      try {
        if (!this.token) return;
        const response = await ApiService.get('/user');
        // Re-use setAuth to ensure consistency and favorites array existence
        this.setAuth(response.data.data, this.token);
      } catch (error) {
        if (error.response && [401, 419].includes(error.response.status)) {
          this.purgeAuth();
        }
        console.error("Failed to fetch user:", error);
      }
    },

    async toggleFavorite(recipeSlug, recipeId) {
      const toast = useToast();
      if (!this.isAuthenticated) {
        toast.error("Please log in to save recipes.");
        return;
      }

      if (!this.user.favorite_recipe_ids) {
        this.user.favorite_recipe_ids = [];
      }

      const isCurrentlyFavorited = this.user.favorite_recipe_ids.includes(recipeId);

      // Optimistic Update
      if (isCurrentlyFavorited) {
        const index = this.user.favorite_recipe_ids.indexOf(recipeId);
        this.user.favorite_recipe_ids.splice(index, 1);
      } else {
        this.user.favorite_recipe_ids.push(recipeId);
      }

      try {
        await ApiService.post(`/recipes/${recipeSlug}/favorite`);

        // THIS LOGIC SHOWS THE CORRECT MESSAGE ONLY ONCE
        if (!isCurrentlyFavorited) {
          toast.success("Added to favorites!");
        } else {
          toast.info("Removed from favorites.");
        }

        localStorage.setItem('user', JSON.stringify(this.user));

      } catch (error) {
        toast.error('Failed to update favorites on the server.');
        console.error("Failed to toggle favorite:", error);

        // Rollback the UI change if the API call fails
        if (isCurrentlyFavorited) {
          this.user.favorite_recipe_ids.push(recipeId);
        } else {
          const index = this.user.favorite_recipe_ids.indexOf(recipeId);
          if (index > -1) {
            this.user.favorite_recipe_ids.splice(index, 1);
          }
        }
      }
    },
  },
});