<script setup lang="ts">
import { useAuthStore } from '@/stores/auth'
import { useCartStore } from '@/stores/cart'
import { useRouter } from 'vue-router'

const auth = useAuthStore()
const cart = useCartStore()
const router = useRouter()

async function handleLogout() {
  await auth.logout()
  router.push('/')
}

const categories = [
  { label: 'Mac',         slug: 'mac' },
  { label: 'iPad',        slug: 'ipad' },
  { label: 'iPhone',      slug: 'iphone' },
  { label: 'Watch',       slug: 'apple-watch' },
  { label: 'AirPods',     slug: 'airpods' },
  { label: 'Accessories', slug: 'accessories' },
]
</script>

<template>
  <header class="site-header">
    <nav class="nav-inner">
      <!-- Apple logo -->
      <RouterLink to="/" class="apple-logo" aria-label="Apple">
        <svg width="18" height="22" viewBox="0 0 814 1000" fill="currentColor">
          <path d="M788.1 340.9c-5.8 4.5-108.2 62.2-108.2 190.5 0 148.4 130.3 200.9 134.2 202.2-.6 3.2-20.7 71.9-68.7 141.9-42.8 61.6-87.5 123.1-155.5 123.1s-85.5-39.5-164-39.5c-76 0-103.7 40.8-165.9 40.8s-105-57.8-155.5-127.4C46 790.7 0 663.4 0 541.8c0-192.5 125.4-294.1 248.7-294.1 66.1 0 121.2 43.4 162.7 43.4 39.5 0 101.1-46 176.3-46 28.5 0 130.9 2.6 198.3 99.2zm-234-181.5c31.1-36.9 53.1-88.1 53.1-139.3 0-7.1-.6-14.3-1.9-20.1-50.6 1.9-110.8 33.7-147.1 75.8-28.5 32.4-55.1 83.6-55.1 135.5 0 7.8 1.3 15.6 1.9 18.1 3.2.6 8.4 1.3 13.6 1.3 45.4 0 102.5-30.4 135.5-71.3z" />
        </svg>
      </RouterLink>

      <!-- Category links -->
      <div class="nav-categories">
        <RouterLink to="/" class="nav-item">Store</RouterLink>
        <RouterLink
          v-for="cat in categories"
          :key="cat.slug"
          :to="`/?category=${cat.slug}`"
          class="nav-item"
        >{{ cat.label }}</RouterLink>
        <template v-if="auth.isAuthenticated">
          <RouterLink to="/orders" class="nav-item">Orders</RouterLink>
        </template>
        <template v-if="auth.isAdmin">
          <RouterLink to="/admin" class="nav-item admin-link">Admin</RouterLink>
        </template>
      </div>

      <!-- Right icons -->
      <div class="nav-actions">
        <RouterLink to="/cart" class="nav-action cart-action" aria-label="Shopping bag">
          <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.5">
            <path d="M6 2L3 6v14a2 2 0 002 2h14a2 2 0 002-2V6l-3-4zM3 6h18M16 10a4 4 0 01-8 0" />
          </svg>
          <span v-if="cart.itemCount > 0" class="cart-badge">{{ cart.itemCount }}</span>
        </RouterLink>

        <template v-if="auth.isAuthenticated">
          <button class="nav-action" @click="handleLogout" title="Sign Out">
            <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.5">
              <path d="M9 21H5a2 2 0 01-2-2V5a2 2 0 012-2h4M16 17l5-5-5-5M21 12H9" />
            </svg>
          </button>
        </template>
        <template v-else>
          <RouterLink to="/login" class="nav-action" aria-label="Sign in">
            <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.5">
              <path d="M20 21v-2a4 4 0 00-4-4H8a4 4 0 00-4 4v2M12 11a4 4 0 100-8 4 4 0 000 8z" />
            </svg>
          </RouterLink>
        </template>
      </div>
    </nav>
  </header>
</template>

<style scoped>
.site-header {
  position: fixed;
  top: 0;
  left: 0;
  right: 0;
  z-index: 100;
  height: 44px;
  background: rgba(22, 22, 23, 0.9);
  backdrop-filter: saturate(180%) blur(20px);
}

.nav-inner {
  max-width: 1024px;
  margin: 0 auto;
  padding: 0 16px;
  height: 100%;
  display: grid;
  grid-template-columns: 1fr auto 1fr;
  align-items: center;
}

.apple-logo {
  color: rgba(255, 255, 255, 0.85);
  display: flex;
  align-items: center;
  transition: color 0.2s;
  width: fit-content;
}

.apple-logo:hover {
  color: white;
}

.nav-categories {
  display: flex;
  align-items: center;
  gap: 0;
}

.nav-item {
  color: rgba(255, 255, 255, 0.85);
  text-decoration: none;
  font-size: 0.75rem;
  font-weight: 400;
  padding: 0 12px;
  height: 44px;
  display: flex;
  align-items: center;
  transition: color 0.2s;
  white-space: nowrap;
}

.nav-item:hover {
  color: white;
}

.admin-link {
  color: rgba(255, 255, 255, 0.6);
  font-size: 0.6875rem;
  letter-spacing: 0.04em;
}

.admin-link:hover {
  color: rgba(255, 255, 255, 0.9);
}

.nav-actions {
  display: flex;
  align-items: center;
  gap: 4px;
  justify-content: flex-end;
}

.nav-action {
  color: rgba(255, 255, 255, 0.85);
  display: flex;
  align-items: center;
  justify-content: center;
  width: 36px;
  height: 44px;
  background: none;
  border: none;
  cursor: pointer;
  position: relative;
  transition: color 0.2s;
  text-decoration: none;
}

.nav-action:hover {
  color: white;
}

.cart-action {
  position: relative;
}

.cart-badge {
  position: absolute;
  top: 6px;
  right: 2px;
  background: #0071e3;
  color: white;
  font-size: 0.6rem;
  font-weight: 700;
  min-width: 14px;
  height: 14px;
  border-radius: 7px;
  display: flex;
  align-items: center;
  justify-content: center;
  padding: 0 3px;
}
</style>
