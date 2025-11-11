<template>
  <Layout>
    <!-- Hero Section -->
    <section class="relative bg-gradient-to-br from-teal-50 to-blue-50 py-16 md:py-24">
      <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 text-center">
        <h1 class="text-4xl md:text-5xl font-bold text-gray-900 mb-6">Get in Touch</h1>
        <p class="text-xl text-gray-600 max-w-2xl mx-auto">
          Have a question, suggestion, or want to collaborate? We'd love to hear from you!
        </p>
      </div>
    </section>

    <!-- Contact Form Section -->
    <section class="py-16 md:py-20">
      <div class="max-w-6xl mx-auto px-4 sm:px-6 lg:px-8">
        <div class="grid grid-cols-1 lg:grid-cols-2 gap-12 items-start">
          <!-- Contact Info -->
          <div v-if="!loadingSettings && settings">
            <h2 class="text-3xl font-bold text-gray-900 mb-8">Let's Connect</h2>
            <div class="space-y-6 mb-8">
              <div class="flex items-start gap-4">
                <div class="w-12 h-12 bg-teal-100 rounded-lg flex items-center justify-center flex-shrink-0"><svg class="w-6 h-6 text-teal-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17.657 16.657L13.414 20.9a1.998 1.998 0 01-2.827 0l-4.244-4.243a8 8 0 1111.314 0z" /><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 11a3 3 0 11-6 0 3 3 0 016 0z" /></svg></div>
                <div><h3 class="font-semibold text-gray-900 mb-1">Visit Us</h3><p class="text-gray-600" v-html="settings.address"></p></div>
              </div>
              <div class="flex items-start gap-4">
                <div class="w-12 h-12 bg-teal-100 rounded-lg flex items-center justify-center flex-shrink-0"><svg class="w-6 h-6 text-teal-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 8l7.89 5.26a2 2 0 002.22 0L21 8M5 19h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z" /></svg></div>
                <div><h3 class="font-semibold text-gray-900 mb-1">Email Us</h3><p class="text-gray-600" v-html="settings.email"></p></div>
              </div>
              <div class="flex items-start gap-4">
                <div class="w-12 h-12 bg-teal-100 rounded-lg flex items-center justify-center flex-shrink-0"><svg class="w-6 h-6 text-teal-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 5a2 2 0 012-2h3.28a1 1 0 01.948.684l1.498 4.493a1 1 0 01-.502 1.21l-2.257 1.13a11.042 11.042 0 005.516 5.516l1.13-2.257a1 1 0 011.21-.502l4.493 1.498a1 1 0 01.684.949V19a2 2 0 01-2 2h-1C9.716 21 3 14.284 3 6V5z" /></svg></div>
                <div><h3 class="font-semibold text-gray-900 mb-1">Call Us</h3><p class="text-gray-600" v-html="settings.phone"></p></div>
              </div>
            </div>
          </div>
          <div v-else class="animate-pulse space-y-8">
            <div class="h-8 bg-gray-300 rounded w-1/2"></div>
            <div v-for="n in 3" :key="n" class="flex items-start gap-4">
                <div class="w-12 h-12 bg-gray-300 rounded-lg"></div>
                <div class="flex-1 space-y-2"><div class="h-4 bg-gray-300 rounded w-1/4"></div><div class="h-4 bg-gray-300 rounded w-3/4"></div></div>
            </div>
          </div>

          <!-- Contact Form -->
          <div class="bg-white rounded-2xl shadow-xl p-8">
            <h2 class="text-2xl font-bold text-gray-900 mb-6">Send us a Message</h2>
            <form @submit.prevent="handleSubmit" class="space-y-6">
              <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                <div>
                  <label for="name" class="form-label">Name *</label>
                  <input v-model="form.name" type="text" id="name" placeholder="Enter your name..." required class="form-input" />
                </div>
                <div>
                  <label for="email" class="form-label">Email *</label>
                  <input v-model="form.email" type="email" id="email" placeholder="Your email address..." required class="form-input" />
                </div>
              </div>
              <div>
                <label for="subject" class="form-label">Subject *</label>
                <input v-model="form.subject" type="text" id="subject" placeholder="Enter subject..." required class="form-input" />
              </div>
              <div>
                <label for="message" class="form-label">Message *</label>
                <textarea v-model="form.message" id="message" placeholder="Tell us more about your enquiry..." rows="6" required class="form-input resize-none"></textarea>
              </div>
              <button type="submit" :disabled="isLoading" class="w-full px-8 py-3 bg-teal-600 text-white rounded-lg hover:bg-teal-700 transition font-semibold shadow-lg hover:shadow-xl transform hover:-translate-y-1 disabled:bg-gray-400 disabled:cursor-not-allowed">
                {{ isLoading ? 'Sending...' : 'Send Message' }}
              </button>
            </form>
          </div>
        </div>
      </div>
    </section>

    <!-- FAQ Section -->
    <section v-if="!loadingSettings && faqs.length > 0" class="py-16 md:py-20 bg-gray-50">
      <div class="max-w-4xl mx-auto px-4 sm:px-6 lg:px-8">
        <div class="text-center mb-12">
          <h2 class="text-3xl md:text-4xl font-bold text-gray-900 mb-4">Frequently Asked Questions</h2>
          <p class="text-gray-600">Quick answers to common questions</p>
        </div>
        <div class="space-y-4">
          <div v-for="(faq, i) in faqs" :key="i" class="bg-white rounded-lg shadow-sm">
            <button @click="toggleFaq(i)" class="w-full px-6 py-4 text-left flex items-center justify-between hover:bg-gray-50 transition">
              <span class="font-semibold text-gray-900">{{ faq.question }}</span>
              <svg class="w-5 h-5 text-gray-500 transform transition-transform" :class="{ 'rotate-180': faq.open }" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7" /></svg>
            </button>
            <div v-if="faq.open" class="px-6 pb-4 border-t border-gray-100">
              <p class="text-gray-600 pt-4">{{ faq.answer }}</p>
            </div>
          </div>
        </div>
      </div>
    </section>
  </Layout>
</template>

<script setup>
import { ref, onMounted } from 'vue';
import Layout from '@/components/Layout.vue';
import { useToast } from 'vue-toastification';
import ApiService from '@/services/ApiService';

const toast = useToast();
const settings = ref(null);
const loadingSettings = ref(true);
const faqs = ref([]);

const form = ref({
  name: '',
  email: '',
  subject: '',
  message: '',
});
const isLoading = ref(false);

onMounted(async () => {
  try {
    const response = await ApiService.get('/settings/contact_page');
    settings.value = response.data.data;
    if (settings.value && settings.value.faqs) {
      faqs.value = settings.value.faqs.map(faq => ({ ...faq, open: false }));
    }
  } catch (error) {
    console.error('Failed to load contact page settings:', error);
    toast.error('Could not load page information. Please try again later.');
  } finally {
    loadingSettings.value = false;
  }
});

const handleSubmit = async () => {
  isLoading.value = true;
  try {
    const response = await ApiService.post('/contact', form.value);
    toast.success(response.data.message || 'Your message has been sent successfully!');
    form.value = { name: '', email: '', subject: '', message: '' };
  } catch (error) {
    const errorMessage = error.response?.data?.message || 'Failed to send message. Please try again.';
    toast.error(errorMessage);
  } finally {
    isLoading.value = false;
  }
};

const toggleFaq = (index) => {
  faqs.value[index].open = !faqs.value[index].open;
};
</script>

<style scoped>
.form-label { @apply block text-sm font-semibold text-gray-900 mb-2; }
.form-input { @apply w-full px-4 py-3 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-teal-500 focus:border-transparent; }
</style>