# Ecommerce App

A full-stack ecommerce application built with **Laravel** (REST API) and **Vue 3** (SPA frontend).

**Live Demo:** https://boostorder.ganrongshen.com/

---

## Prerequisites

- PHP >= 8.2
- Composer
- Node.js >= 18 & npm

---

## Setup

### 1. Clone the repo

```bash
git clone https://github.com/Gannnn/ecommerce-app.git
cd ecommerce-app
```

---

### 2. Backend

```bash
cd backend
composer install
cp .env.example .env
php artisan key:generate
```

The app uses **SQLite** by default — no database server needed. Create the database file:

```bash
touch database/database.sqlite
```

> On Windows, create an empty file at `backend/database/database.sqlite` manually.

Run migrations and seed dummy data:

```bash
php artisan migrate --seed
```

Start the API server:

```bash
php artisan serve
```

The API will be available at `http://localhost:8000`.

---

### 3. Frontend

Open a new terminal:

```bash
cd frontend
npm install
```

Create the environment file:

```bash
cp .env.example .env
```

Or create `frontend/.env` manually with:

```
VITE_API_URL=http://localhost:8000/api
```

Start the dev server:

```bash
npm run dev
```

The app will be available at `http://localhost:5173`.

---

## Default Accounts

After seeding, you can log in with:

| Role  | Email | Password |
|-------|-------|----------|
| Admin | admin@example.com | password |
| Customer | demo@example.com | password |

Admin panel is accessible at `/admin`. Customer accounts can also be created via the Register page.

---

## Features

- Product catalogue with search and category filtering
- Cart with live item count badge
- Order placement with 16-character order numbers
- Order history with status tracking (customer)
- Admin panel — manage products, categories, orders, and currencies
- Multi-currency support with live BNM exchange rates
- User authentication (register / login)
