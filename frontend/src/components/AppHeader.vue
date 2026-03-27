<script setup lang="ts">
import { ref, watch } from 'vue'
import { useRoute } from 'vue-router'
import { useAuthStore } from '@/stores/auth'
import { useCartStore } from '@/stores/cart'

const auth = useAuthStore()
const cart = useCartStore()
const route = useRoute()
const mobileMenuOpen = ref(false)

watch(() => route.path, () => { mobileMenuOpen.value = false })

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
        <template v-if="auth.isAdmin">
          <RouterLink to="/admin" class="nav-item admin-link">Admin</RouterLink>
        </template>
      </div>

      <!-- Right icons -->
      <div class="nav-actions">
        <!-- Hamburger (mobile only) -->
        <button class="hamburger nav-action" aria-label="Menu" @click="mobileMenuOpen = !mobileMenuOpen">
          <span class="ham-bar" :class="{ open: mobileMenuOpen }" />
          <span class="ham-bar" :class="{ open: mobileMenuOpen }" />
          <span class="ham-bar" :class="{ open: mobileMenuOpen }" />
        </button>

        <RouterLink to="/cart" class="nav-action cart-action desktop-only" aria-label="Shopping cart">
          <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.5">
            <circle cx="9" cy="21" r="1" /><circle cx="20" cy="21" r="1" />
            <path d="M1 1h4l2.68 13.39a2 2 0 001.98 1.61h9.72a2 2 0 001.98-1.71L23 6H6" />
          </svg>
          <span v-if="cart.itemCount > 0" class="cart-badge">{{ cart.itemCount }}</span>
        </RouterLink>

        <template v-if="auth.isAuthenticated">
          <RouterLink to="/account" class="nav-action desktop-only" aria-label="Account">
            <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.5">
              <path d="M20 21v-2a4 4 0 00-4-4H8a4 4 0 00-4 4v2M12 11a4 4 0 100-8 4 4 0 000 8z" />
            </svg>
          </RouterLink>
        </template>
        <template v-else>
          <RouterLink to="/login" class="nav-action desktop-only" aria-label="Sign in">
            <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.5">
              <path d="M20 21v-2a4 4 0 00-4-4H8a4 4 0 00-4 4v2M12 11a4 4 0 100-8 4 4 0 000 8z" />
            </svg>
          </RouterLink>
        </template>
      </div>
    </nav>
  </header>

  <!-- Mobile menu -->
  <Transition name="mobile-menu">
    <div v-if="mobileMenuOpen" class="mobile-menu">
      <RouterLink to="/" class="mobile-item" @click="mobileMenuOpen = false">Store</RouterLink>
      <RouterLink
        v-for="cat in categories"
        :key="cat.slug"
        :to="`/?category=${cat.slug}`"
        class="mobile-item"
        @click="mobileMenuOpen = false"
      >{{ cat.label }}</RouterLink>
      <div class="mobile-divider" />
      <RouterLink to="/cart" class="mobile-item mobile-item-icon" @click="mobileMenuOpen = false">
        <svg width="17" height="17" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.5">
          <circle cx="9" cy="21" r="1" /><circle cx="20" cy="21" r="1" />
          <path d="M1 1h4l2.68 13.39a2 2 0 001.98 1.61h9.72a2 2 0 001.98-1.71L23 6H6" />
        </svg>
        Cart
        <span v-if="cart.itemCount > 0" class="mobile-cart-count">{{ cart.itemCount }}</span>
      </RouterLink>
      <RouterLink v-if="auth.isAuthenticated" to="/account" class="mobile-item mobile-item-icon" @click="mobileMenuOpen = false">
        <svg width="17" height="17" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.5">
          <path d="M20 21v-2a4 4 0 00-4-4H8a4 4 0 00-4 4v2M12 11a4 4 0 100-8 4 4 0 000 8z" />
        </svg>
        Account
      </RouterLink>
      <RouterLink v-else to="/login" class="mobile-item mobile-item-icon" @click="mobileMenuOpen = false">
        <svg width="17" height="17" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.5">
          <path d="M20 21v-2a4 4 0 00-4-4H8a4 4 0 00-4 4v2M12 11a4 4 0 100-8 4 4 0 000 8z" />
        </svg>
        Sign In
      </RouterLink>
      <RouterLink v-if="auth.isAdmin" to="/admin" class="mobile-item mobile-item-muted" @click="mobileMenuOpen = false">Admin</RouterLink>
    </div>
  </Transition>
</template>

<style scoped>
.site-header {
  position: fixed;
  top: 0;
  left: 0;
  right: 0;
  z-index: 100;
  height: 44px;
  background: rgba(255, 255, 255, 0.72);
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
  color: rgba(0, 0, 0, 0.8);
  display: flex;
  align-items: center;
  transition: color 0.2s;
  width: fit-content;
}

.apple-logo:hover {
  color: black;
}

.nav-categories {
  display: flex;
  align-items: center;
  gap: 0;
}

.nav-item {
  color: rgba(0, 0, 0, 0.8);
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
  color: black;
}

.admin-link {
  color: rgba(0, 0, 0, 0.5);
  font-size: 0.6875rem;
  letter-spacing: 0.04em;
}

.admin-link:hover {
  color: rgba(0, 0, 0, 0.85);
}

.nav-actions {
  display: flex;
  align-items: center;
  gap: 4px;
  justify-content: flex-end;
}

.nav-action {
  color: rgba(0, 0, 0, 0.8);
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
  color: black;
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

/* Hamburger button */
.hamburger {
  display: none;
  flex-direction: column;
  justify-content: center;
  gap: 5px;
  padding: 0 8px;
}

.ham-bar {
  display: block;
  width: 18px;
  height: 1.5px;
  background: rgba(0, 0, 0, 0.8);
  border-radius: 2px;
  transition: transform 0.2s ease, opacity 0.2s ease;
  transform-origin: center;
}

/* Animate to X when open */
.ham-bar:nth-child(1).open { transform: translateY(6.5px) rotate(45deg); }
.ham-bar:nth-child(2).open { opacity: 0; }
.ham-bar:nth-child(3).open { transform: translateY(-6.5px) rotate(-45deg); }

/* Mobile dropdown menu */
.mobile-menu {
  position: fixed;
  top: 44px;
  left: 0;
  right: 0;
  background: rgba(255, 255, 255, 0.95);
  backdrop-filter: saturate(180%) blur(20px);
  border-bottom: 1px solid #d2d2d7;
  z-index: 99;
  padding: 8px 0 16px;
}

.mobile-item {
  display: block;
  padding: 12px 24px;
  font-size: 1rem;
  color: #1d1d1f;
  text-decoration: none;
  transition: background 0.15s;
}

.mobile-item:hover { background: #f5f5f7; }

.mobile-item-muted { color: #6e6e73; font-size: 0.9375rem; }

.mobile-divider {
  height: 1px;
  background: #d2d2d7;
  margin: 8px 0;
}

.mobile-menu-enter-active { transition: opacity 0.18s ease, transform 0.18s ease; }
.mobile-menu-leave-active { transition: opacity 0.14s ease, transform 0.14s ease; }
.mobile-menu-enter-from  { opacity: 0; transform: translateY(-8px); }
.mobile-menu-leave-to    { opacity: 0; transform: translateY(-4px); }

.mobile-item-icon {
  display: flex;
  align-items: center;
  gap: 12px;
}

.mobile-cart-count {
  margin-left: auto;
  background: #0071e3;
  color: #fff;
  font-size: 0.7rem;
  font-weight: 700;
  min-width: 18px;
  height: 18px;
  border-radius: 9px;
  display: flex;
  align-items: center;
  justify-content: center;
  padding: 0 5px;
}

@media (max-width: 767px) {
  .nav-categories { display: none; }
  .desktop-only { display: none; }
  .hamburger { display: flex; }

  .nav-inner {
    display: flex;
    justify-content: space-between;
    align-items: center;
  }
}
</style>
