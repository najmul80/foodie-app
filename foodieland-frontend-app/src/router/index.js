import { createRouter, createWebHistory } from "vue-router";
import Home from "@/pages/Home.vue";
import BlogPost from "@/pages/BlogPost.vue";
import BlogList from "@/pages/BlogList.vue";
import Contact from "@/pages/Contact.vue";
import AboutPage from "@/pages/AboutPage.vue";
import RecipeDetails from "@/pages/RecipeDetails.vue";
import LoginView from '@/views/LoginView.vue'
import RegisterView from '@/views/RegisterView.vue'
import RecipesView from '@/views/RecipesView.vue'
import AuthorView from '@/views/AuthorView.vue';
import CreateRecipeView from '@/views/CreateRecipeView.vue';
import ProfileView from '@/views/ProfileView.vue';
import NotFoundView from '@/views/NotFoundView.vue'
import { useAuthStore } from "@/store/authStore";

const routes = [
  {
    path: "/",
    name: "Home",
    component: Home,
  },
  {
    path: "/blog",
    name: "BlogList",
    component: BlogList,
  },
  {
    path: "/blog/:slug",
    name: "BlogPost",
    component: BlogPost,
  },

  {
    path: '/recipes',
    name: 'Recipes',
    component: RecipesView
  },
  {
    path: "/recipe/:slug",
    name: "RecipeDetails",
    component: RecipeDetails,
  },
  {
    path: '/author/:id',
    name: 'Author',
    component: AuthorView
  },
  {
    path: "/contact",
    name: "Contact",
    component: Contact,
  },
  {
    path: "/about",
    name: "about",
    component: AboutPage,
  },
  {
    path: '/login',
    name: 'Login',
    component: LoginView,
  },
  {
    path: '/register',
    name: 'Register',
    component: RegisterView,
  },
  {
    path: '/:pathMatch(.*)*',
    name: 'NotFound',
    component: NotFoundView
  },

  {
    path: '/create-recipe',
    name: 'CreateRecipe',
    component: CreateRecipeView,
    meta: { requiresAuth: true }
  },
  {
    path: '/recipe/:slug/edit',
    name: 'EditRecipe',
    component: () => import('@/views/EditRecipeView.vue'),
    meta: { requiresAuth: true }
  },
  {
    path: '/profile',
    name: 'Profile',
    component: ProfileView,
    meta: { requiresAuth: true }
  },

  {
    path: '/blog/create',
    name: 'CreateBlog',
    component: () => import('@/views/CreateBlogView.vue'),
    meta: { requiresAuth: true }
  },
  {
    path: '/blog/:slug/edit',
    name: 'EditBlog',
    component: () => import('@/views/EditBlogView.vue'),
    meta: { requiresAuth: true }
  },
  { path: '/categories', name: 'Categories', component: () => import('@/views/CategoriesView.vue') },
  {
    path: '/auth/callback',
    name: 'AuthCallback',
    component: () => import('@/views/AuthCallback.vue')
  }
];

const router = createRouter({
  history: createWebHistory(import.meta.env.BASE_URL),
  routes,
  scrollBehavior(to, from, savedPosition) {
    return { top: 0 }
  },
});

// Global Navigation Guard
router.beforeEach((to, from, next) => {
  const authStore = useAuthStore()
  const requiresAuth = to.matched.some(record => record.meta.requiresAuth)
  if (requiresAuth && !authStore.isAuthenticated) {
    next({ name: 'Login' })
  } else {
    next()
  }
});

export default router;
