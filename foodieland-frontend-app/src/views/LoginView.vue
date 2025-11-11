<template>
  <div class="max-w-md mx-auto mt-20 p-8 border rounded-lg shadow-lg">
    <!-- Login Form -->
    <div v-if="!showOtpForm">
      <h1 class="text-3xl font-bold mb-6 text-center">Login to Foodieland</h1>
      <form @submit.prevent="handleLogin">
        <!-- Main Error Message -->
        <div v-if="error && !verificationRequired" class="p-3 bg-red-100 text-red-700 rounded mb-4">{{ error }}</div>

        <!-- Email Verification Required Message -->
        <div v-if="verificationRequired" class="p-3 bg-yellow-100 text-yellow-800 rounded mb-4">
          <p>{{ error }}</p>
          <button @click.prevent="handleResendOtp" class="font-semibold underline mt-2 hover:text-yellow-900">
            {{ resendMessage || 'Resend verification OTP' }}
          </button>
        </div>

        <div class="mb-4">
          <label for="email" class="block mb-2 font-semibold">Email</label>
          <input v-model="form.email" type="email" id="email" class="w-full p-3 border rounded" required />
        </div>
        <div class="mb-6">
          <label for="password" class="block mb-2 font-semibold">Password</label>
          <input v-model="form.password" type="password" id="password" class="w-full p-3 border rounded" required />
        </div>
        <button type="submit" :disabled="isLoading"
          class="w-full bg-gray-900 text-white py-3 rounded hover:bg-gray-800 disabled:bg-gray-400">
          {{ isLoading ? 'Logging in...' : 'Login' }}
        </button>
      </form>
    </div>

    <!-- OTP Verification Form -->
    <div v-else>
      <h1 class="text-3xl font-bold mb-6 text-center">Verify Your Email</h1>
      <div class="p-3 bg-green-100 text-green-800 rounded mb-4">
        An OTP has been sent to <strong>{{ form.email }}</strong>.
      </div>
      <form @submit.prevent="handleVerifyOtp">
        <div v-if="error" class="p-3 bg-red-100 text-red-700 rounded mb-4">{{ error }}</div>
        <div class="mb-4">
          <label for="otp" class="block mb-2 font-semibold">Enter OTP</label>
          <input v-model="otp" type="text" id="otp"
            class="w-full p-3 border rounded text-center text-2xl tracking-widest" required maxlength="6" />
        </div>
        <button type="submit" :disabled="isLoading"
          class="w-full bg-teal-600 text-white py-3 rounded hover:bg-teal-700 disabled:bg-gray-400">
          {{ isLoading ? 'Verifying...' : 'Verify Email' }}
        </button>
      </form>
    </div>

    <div class="text-center mt-4">
      <router-link to="/register" class="text-teal-600 hover:underline">Don't have an account? Register</router-link>
    </div>
    <div class="mt-6">
  <div class="relative"><div class="absolute inset-0 flex items-center"><div class="w-full border-t border-gray-300"></div></div><div class="relative flex justify-center text-sm"><span class="px-2 bg-white text-gray-500">Or continue with</span></div></div>
  <div class="mt-6 grid grid-cols-2 gap-3">
    <div><button @click="socialLogin('google')" class="w-full inline-flex justify-center py-2 px-4 border border-gray-300 rounded-md shadow-sm bg-white text-sm font-medium text-gray-500 hover:bg-gray-50">Google</button></div>
    <div><button @click="socialLogin('facebook')" class="w-full inline-flex justify-center py-2 px-4 border border-gray-300 rounded-md shadow-sm bg-white text-sm font-medium text-gray-500 hover:bg-gray-50">Facebook</button></div>
  </div>
</div>
  </div>
</template>

<script setup>
import { ref } from 'vue';
import { useAuthStore } from '@/store/authStore';
import { useRouter } from 'vue-router';
import ApiService from '@/services/ApiService';
import { useToast } from 'vue-toastification';

const authStore = useAuthStore();
const router = useRouter();
const toast = useToast();
const form = ref({ email: '', password: '' });
const otp = ref('');
const isLoading = ref(false);
const error = ref(null);

const verificationRequired = ref(false);
const showOtpForm = ref(false);
const resendMessage = ref('');

const handleLogin = async () => {
  isLoading.value = true;
  error.value = null;
  verificationRequired.value = false;

  try {
    await authStore.login(form.value);
    toast.success('Successfully logged in!');
    router.push('/');
  } catch (err) {
    const errorMessage = err.response?.data?.message || 'Login failed. Please check your credentials.';
    toast.error(errorMessage);

    if (err.response?.status === 403 && errorMessage.includes('verify your email')) {
      verificationRequired.value = true;
    }
  } finally {
    isLoading.value = false;
  }
};

const handleResendOtp = async () => {
  isLoading.value = true;
  resendMessage.value = 'Sending OTP...';
  try {
    await authStore.resendOtp(form.value.email);
    showOtpForm.value = true;
  } catch (err) {
    resendMessage.value = 'Failed to send OTP. Try again.';
  } finally {
    isLoading.value = false;
  }
};

const handleVerifyOtp = async () => {
  isLoading.value = true;
  error.value = null;
  try {
    await authStore.verifyOtp({ email: form.value.email, otp: otp.value });
    router.push('/');
  } catch (err) {
    error.value = err.response?.data?.message || 'OTP verification failed.';
  } finally {
    isLoading.value = false;
  }
};

const socialLogin = async (provider) => {
  try {
    const response = await ApiService.get(`/auth/${provider}/redirect`);
    // Open the provider's login page in a new window
    window.location.href = response.data.redirect_url;
  } catch (error) {
    console.error('Social login error', error);
  }
};
</script>