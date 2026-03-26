# CLAUDE.md

This file provides guidance to Claude Code (claude.ai/code) when working with code in this repository.

## Project Overview

A full-stack e-commerce application with a **Laravel 12 API backend** and a **Vue 3 SPA frontend**. The two are separate projects under `backend/` and `frontend/`.

## Commands

### Backend (`backend/`)

```bash
composer setup        # First-time setup: install deps, copy .env, generate key, migrate, npm install
composer dev          # Start all dev processes concurrently (artisan serve, queue, logs, Vite)
composer test         # Run PHPUnit tests
php artisan migrate   # Run migrations
php artisan tinker    # Interactive REPL
```

Run a single PHPUnit test:
```bash
php artisan test --filter TestClassName
# or
./vendor/bin/phpunit tests/Feature/SomeTest.php
```

### Frontend (`frontend/`)

```bash
npm run dev           # Start Vite dev server
npm run build         # Type-check + production build
npm run test:unit     # Run Vitest unit tests
npm run test:e2e:dev  # Run Cypress E2E against dev server
npm run lint          # Run oxlint + eslint
npm run format        # Format with Prettier
```

Run a single Vitest test:
```bash
npx vitest run src/__tests__/SomeComponent.spec.ts
```

## Architecture

### Backend (Laravel 12)
- Standard MVC structure under `app/Http/Controllers/`, `app/Models/`
- Routes in `routes/web.php` (web) and `routes/console.php`
- Tests split into `tests/Feature/` and `tests/Unit/`; PHPUnit uses in-memory SQLite (separate from app DB)
- App uses MySQL (`ecommerce-app` database) with sessions, cache, and queues all on the database driver

### Frontend (Vue 3 + TypeScript)
- Composition API with `<script setup>` syntax throughout
- Pinia for state management (`src/stores/`)
- Vue Router 5 (`src/router/index.ts`)
- Path alias `@/` maps to `src/`
- Prettier config: no semicolons, single quotes, 100-char line width

### Separation of concerns
The backend serves as a JSON API; the frontend is a standalone SPA that communicates with it. They are developed and run independently — the backend does not serve the frontend's assets in development.

## Database

Active config uses MySQL (host `127.0.0.1:3306`, db `ecommerce-app`, user `root`, no password — standard Laragon defaults). Existing migrations create: users, password_reset_tokens, sessions, cache, jobs tables.
