<template>
  <footer class="bg-white border-t border-gray-200 mt-20">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-12">
      <div class="flex flex-col md:flex-row items-center justify-between">
        <div class="mb-6 md:mb-0">
          <router-link to="/" class="text-xl font-bold text-gray-900 mb-1">Foodieland.</router-link>
          <p class="text-sm text-gray-600 mt-2">Your daily dose of deliciousness.</p>
        </div>
        
        <nav class="flex items-center space-x-8 mb-6 md:mb-0">
          <router-link to="/recipes" class="text-sm text-gray-700 hover:text-gray-900 transition-colors">Recipes</router-link>
          <router-link to="/blog" class="text-sm text-gray-700 hover:text-gray-900 transition-colors">Blog</router-link>
          <router-link to="/contact" class="text-sm text-gray-700 hover:text-gray-900 transition-colors">Contact</router-link>
          <router-link to="/about" class="text-sm text-gray-700 hover:text-gray-900 transition-colors">About us</router-link>
        </nav>

        <div class="flex items-center space-x-4">
          <a href="#" target="_blank" rel="noopener noreferrer" class="text-gray-500 hover:text-gray-900 transition-colors">
            <span class="sr-only">Facebook</span>
            <svg class="w-6 h-6" fill="currentColor" viewBox="0 0 24 24" aria-hidden="true">
              <path fill-rule="evenodd" d="M22 12c0-5.523-4.477-10-10-10S2 6.477 2 12c0 4.991 3.657 9.128 8.438 9.878v-6.987H7.901v-2.891h2.537V9.797c0-2.506 1.492-3.89 3.777-3.89 1.094 0 2.238.195 2.238.195v2.46h-1.26c-1.243 0-1.63.771-1.63 1.562v1.875h2.775l-.433 2.891h-2.342v6.987A10.001 10.001 0 0022 12z" clip-rule="evenodd" />
            </svg>
          </a>
          <a href="#" target="_blank" rel="noopener noreferrer" class="text-gray-500 hover:text-gray-900 transition-colors">
            <span class="sr-only">Twitter</span>
            <svg class="w-6 h-6" fill="currentColor" viewBox="0 0 24 24" aria-hidden="true">
              <path d="M8.29 20.251c7.547 0 11.675-6.253 11.675-11.675 0-.178 0-.355-.012-.53A8.348 8.348 0 0022 5.92a8.19 8.19 0 01-2.357.646 4.118 4.118 0 001.804-2.27 8.224 8.224 0 01-2.605.996 4.107 4.107 0 00-6.993 3.743 11.65 11.65 0 01-8.457-4.287 4.106 4.106 0 001.27 5.477A4.072 4.072 0 012.8 9.71v.052a4.105 4.105 0 003.292 4.022 4.095 4.095 0 01-1.853.07 4.108 4.108 0 003.834 2.85A8.233 8.233 0 012 18.407a11.616 11.616 0 006.29 1.84" />
            </svg>
          </a>
          <a href="#" target="_blank" rel="noopener noreferrer" class="text-gray-500 hover:text-gray-900 transition-colors">
            <span class="sr-only">Instagram</span>
            <svg class="w-6 h-6" fill="currentColor" viewBox="0 0 24 24" aria-hidden="true">
                <path fill-rule="evenodd" d="M12.315 2c2.43 0 2.784.013 3.808.06 1.064.049 1.791.218 2.427.465a4.902 4.902 0 011.772 1.153 4.902 4.902 0 011.153 1.772c.247.636.416 1.363.465 2.427.048 1.024.06 1.378.06 3.808s-.012 2.784-.06 3.808c-.049 1.064-.218 1.791-.465 2.427a4.902 4.902 0 01-1.153 1.772 4.902 4.902 0 01-1.772 1.153c-.636.247-1.363.416-2.427.465-1.024.048-1.378.06-3.808.06s-2.784-.012-3.808-.06c-1.064-.049-1.791-.218-2.427-.465a4.902 4.902 0 01-1.772-1.153 4.902 4.902 0 01-1.153-1.772c-.247-.636-.416-1.363-.465-2.427-.048-1.024-.06-1.378-.06-3.808s.012-2.784.06-3.808c.049-1.064.218-1.791.465-2.427a4.902 4.902 0 011.153-1.772A4.902 4.902 0 016.345 2.525c.636-.247 1.363-.416 2.427-.465C9.784 2.013 10.138 2 12.315 2zm-2.193 6.643a3.567 3.567 0 100 7.134 3.567 3.567 0 000-7.134zm7.625-2.276a1.25 1.25 0 10-2.5 0 1.25 1.25 0 002.5 0z" clip-rule="evenodd" />
            </svg>
          </a>
        </div>
      </div>

      <div class="mt-8 pt-8 border-t border-gray-200 text-center text-sm text-gray-600">
        <p>&copy; {{ new Date().getFullYear() }} Foodieland. All rights reserved.</p>
      </div>
    </div>
  </footer>
</template>

<script setup>
// No script logic is needed for this static component
</script>