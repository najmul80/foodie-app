# Foodieland API

This is the official Laravel backend API for the Foodieland web application. It provides a complete RESTful API for managing recipes, blog posts, users, comments, categories, and more. The API is built to be consumed by a separate frontend application (e.g., Vue.js).

## Features

- **Authentication:** Secure user registration with OTP email verification, login (token-based with Sanctum), and password reset.
- **Role-Based Access Control (RBAC):** `Admin` and `User` roles managed by the Spatie Permission package.
- **Full CRUD Operations:** For recipes, blog posts, categories (admin-only), and comments.
- **Content Relationships:** Many-to-many relationships for categories and tags.
- **File Uploads:** Handles image uploads for recipes, blog posts, and user profiles.
- **Community Features:** Author profiles, recipe favoriting system, and commenting.
- **API Resources:** Consistent JSON responses using Laravel API Resources.
- **Robust Seeding:** Comes with a comprehensive database seeder for realistic test data.

## Tech Stack

- **Framework:** Laravel 12
- **Authentication:** Laravel Sanctum
- **Authorization:** Spatie Laravel Permission
- **Database:** MySQL 
- **API Testing:** Postman

---

## Getting Started

### Prerequisites

- PHP >= 8.2
- Composer
- A database server (e.g., MySQL)
- Mail server for testing (e.g., Mailtrap.io)

### Installation and Setup

1.  **Clone the repository:**
    ```bash
    git clone https://github.com/freelancershahjalal/backend_food_blog_project_food_ieland
    cd foodieland-api
    ```

2.  **Install PHP dependencies:**
    ```bash
    composer install
    ```

3.  **Create your environment file:**
    Copy the example `.env` file and generate an application key.
    ```bash
    cp .env.example .env
    php artisan key:generate
    ```

4.  **Configure your `.env` file:**
    Open the `.env` file and update the following variables with your local environment settings:
    ```env
    APP_URL=http://127.0.0.1:8000
    FRONTEND_URL=http://localhost:5173

    DB_CONNECTION=mysql
    DB_HOST=127.0.0.1
    DB_PORT=3306
    DB_DATABASE=your_database_name
    DB_USERNAME=your_db_user
    DB_PASSWORD=your_db_password

    MAIL_MAILER=smtp
    MAIL_HOST=sandbox.smtp.mailtrap.io
    MAIL_PORT=2525
    MAIL_USERNAME=your_mailtrap_username
    MAIL_PASSWORD=your_mailtrap_password
    ```

5.  **Run database migrations and seed data:**
    This command will create all necessary tables and populate them with a rich set of dummy data.
    ```bash
    php artisan migrate:fresh --seed
    ```

6.  **Create the storage link:**
    This makes your uploaded files publicly accessible.
    ```bash
    php artisan storage:link
    ```

7.  **Start the development server:**
    ```bash
    php artisan serve
    ```
    php artisan queue:work

    The API will now be running at `http://127.0.0.1:8000`.

---

## API Endpoints

A complete list of API endpoints is available in the Postman collection or can be viewed by running `php artisan route:list`.

- **Authentication:** `/api/register`, `/api/login`, `/api/logout`, `/api/verify-otp`, etc.
- **Recipes:** `GET /api/recipes`, `GET /api/recipes/{slug}`, `POST /api/recipes`, etc.
- **Blog Posts:** `GET /api/blog`, `GET /api/blog/{slug}`, etc.
- **Admin:** `/api/admin/categories`, etc.

---
© 2024 Foodieland. All rights reserved.