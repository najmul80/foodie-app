# Foodieland Frontend

This is the Vue.js frontend for the Foodieland web application. It is a modern, responsive Single Page Application (SPA) built with Vue 3 and Vite. It communicates with the separate Laravel backend API to display and manage all content.

## Features

- **Modern Tech Stack:** Built with Vue 3 (Composition API), Vite, and Pinia.
- **Routing:** All pages handled client-side with Vue Router, including protected routes.
- **State Management:** Centralized state management with Pinia for authentication, recipes, blogs, etc.
- **Dynamic Content:** All content (recipes, blogs, comments, user profiles) is fetched dynamically from the backend API.
- **User Interaction:** Full implementation of login, registration (with OTP), profile updates, commenting, and content creation forms.
- **Polished UX:** Includes skeleton loaders, toast notifications, and smooth page transitions.
- **Styling:** Styled with Tailwind CSS for a responsive and modern design that matches the original mockups.

## Tech Stack

- **Framework:** Vue 3 (with `<script setup>`)
- **Build Tool:** Vite
- **Routing:** Vue Router 4
- **State Management:** Pinia
- **HTTP Client:** Axios
- **CSS Framework:** Tailwind CSS
- **Notifications:** `vue-toastification` & `sweetalert2`

---

## Getting Started

### Prerequisites

- Node.js >= 18.x
- npm or yarn

### Installation and Setup

1.  **Clone the repository:**
    ```bash
    git clone https://github.com/najmul80/foodie-app
    cd foodieland-frontend-app
    ```

2.  **Install Node.js dependencies:**
    ```bash
    npm install
    ```

3.  **Create your environment file:**
    Create a new file named `.env.development.local` in the project root. This file will store the URL of your local backend API.
    ```env
    # .env.development.local

    baseURL: 'http://127.0.0.1:8000/api/',
    ```

4.  **Configure `ApiService.js` to use the environment variable:**
    Make sure your `src/services/ApiService.js` file is configured to use this variable.
    ```javascript
    // src/services/ApiService.js
    import axios from 'axios';

    const ApiService = axios.create({
      baseURL: import.meta.env.VITE_API_BASE_URL,
      // ...
    });
    ```

5.  **Start the development server:**
    Make sure your Laravel backend API is running first. Then, run this command:
    ```bash
    npm run dev
    ```
    The frontend application will now be running at `http://localhost:5173` (or another available port).

---
© 2024 Foodieland. All rights reserved.
