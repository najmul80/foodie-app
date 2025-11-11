<template>
  <div class="max-w-md mx-auto mt-20 p-8 border rounded-lg shadow-lg">
    <h1 class="text-3xl font-bold mb-6 text-center">
      {{ registrationSuccess ? 'Verify Your Email' : 'Create an Account' }}
    </h1>
    
    <!-- Step 1: Registration Form -->
    <form v-if="!registrationSuccess" @submit.prevent="handleRegister">
      <!-- ... form fields for name, email, password ... -->
      <div v-if="error" class="p-3 bg-red-100 text-red-700 rounded mb-4">{{ error }}</div>
      
      <div class="mb-4">
        <label for="name" class="block mb-2 font-semibold">Full Name</label>
        <input v-model="form.name" type="text" id="name" class="w-full p-3 border rounded" required />
      </div>
      <div class="mb-4">
        <label for="email" class="block mb-2 font-semibold">Email</label>
        <input v-model="form.email" type="email" id="email" class="w-full p-3 border rounded" required />
      </div>
      <div class="mb-4">
        <label for="password" class="block mb-2 font-semibold">Password</label>
        <input v-model="form.password" type="password" id="password" class="w-full p-3 border rounded" required />
      </div>
      <div class="mb-6">
        <label for="password_confirmation" class="block mb-2 font-semibold">Confirm Password</label>
        <input v-model="form.password_confirmation" type="password" id="password_confirmation" class="w-full p-3 border rounded" required />
      </div>
      
      <button type="submit" :disabled="isLoading" class="w-full bg-gray-900 text-white py-3 rounded hover:bg-gray-800 disabled:bg-gray-400">
        {{ isLoading ? 'Registering...' : 'Register' }}
      </button>
    </form>

    <!-- Step 2: OTP Verification Form -->
    <div v-else>
      <div class="p-3 bg-green-100 text-green-800 rounded mb-4">
        Registration successful! An OTP has been sent to <strong>{{ form.email }}</strong>. Please check your inbox.
      </div>
      <form @submit.prevent="handleVerifyOtp">
        <div v-if="error" class="p-3 bg-red-100 text-red-700 rounded mb-4">{{ error }}</div>
        
        <div class="mb-4">
          <label for="otp" class="block mb-2 font-semibold">Enter OTP</label>
          <input v-model="otp" type="text" id="otp" class="w-full p-3 border rounded text-center text-2xl tracking-widest" required maxlength="6" />
        </div>
        
        <button type="submit" :disabled="isLoading" class="w-full bg-teal-600 text-white py-3 rounded hover:bg-teal-700 disabled:bg-gray-400">
          {{ isLoading ? 'Verifying...' : 'Verify Email' }}
        </button>
      </form>
      
      <!-- Resend OTP Section -->
      <div class="text-center mt-4 text-sm">
        <p class="text-gray-600">
          Didn't receive the OTP? 
          <button @click="handleResendOtp" :disabled="isResending" class="font-semibold text-teal-600 hover:underline disabled:text-gray-400 disabled:cursor-not-allowed">
            {{ resendMessage || 'Resend OTP' }}
          </button>
        </p>
      </div>
    </div>

    <div class="text-center mt-4">
      <router-link to="/login" class="text-teal-600 hover:underline">Already have an account? Login</router-link>
    </div>
  </div>
</template>

<script setup>
import { ref } from 'vue';
import { useAuthStore } from '@/store/authStore';
import { useRouter } from 'vue-router';

const authStore = useAuthStore();
const router = useRouter();

const form = ref({
  name: '',
  email: '',
  password: '',
  password_confirmation: '',
});
const otp = ref('');
const isLoading = ref(false);
const error = ref(null);
const registrationSuccess = ref(false);


const isResending = ref(false);
const resendMessage = ref('');

const handleRegister = async () => {
  isLoading.value = true;
  error.value = null;
  try {
    await authStore.register(form.value);
    registrationSuccess.value = true;
  } catch (err) {
    if (err.response?.data?.errors) {
      const errors = err.response.data.errors;
      const firstErrorKey = Object.keys(errors)[0];
      error.value = errors[firstErrorKey][0];
    } else {
      error.value = err.response?.data?.message || 'Registration failed.';
    }
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


const handleResendOtp = async () => {
  isResending.value = true;
  resendMessage.value = 'Sending...';
  error.value = null; 
  try {
    await authStore.resendOtp(form.value.email);
    resendMessage.value = 'Sent!';

    setTimeout(() => {
      resendMessage.value = '';
      isResending.value = false;
    }, 5000); 
  } catch (err) {
    error.value = err.response?.data?.message || 'Failed to resend OTP.';
    resendMessage.value = '';
    isResending.value = false;
  }
};
</script>