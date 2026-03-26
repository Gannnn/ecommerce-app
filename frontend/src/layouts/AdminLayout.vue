<script setup lang="ts">
import { useAuthStore } from '@/stores/auth'
import { useRouter } from 'vue-router'

const auth = useAuthStore()
const router = useRouter()

async function handleLogout() {
  await auth.logout()
  router.push({ name: 'login' })
}
</script>

<template>
  <div class="admin-shell">
    <aside class="sidebar">
      <div class="sidebar-top">
        <div class="brand">
          <svg width="18" height="22" viewBox="0 0 814 1000" fill="currentColor">
            <path d="M788.1 340.9c-5.8 4.5-108.2 62.2-108.2 190.5 0 148.4 130.3 200.9 134.2 202.2-.6 3.2-20.7 71.9-68.7 141.9-42.8 61.6-87.5 123.1-155.5 123.1s-85.5-39.5-164-39.5c-76 0-103.7 40.8-165.9 40.8s-105-57.8-155.5-127.4C46 790.7 0 663.4 0 541.8c0-192.5 125.4-294.1 248.7-294.1 66.1 0 121.2 43.4 162.7 43.4 39.5 0 101.1-46 176.3-46 28.5 0 130.9 2.6 198.3 99.2zm-234-181.5c31.1-36.9 53.1-88.1 53.1-139.3 0-7.1-.6-14.3-1.9-20.1-50.6 1.9-110.8 33.7-147.1 75.8-28.5 32.4-55.1 83.6-55.1 135.5 0 7.8 1.3 15.6 1.9 18.1 3.2.6 8.4 1.3 13.6 1.3 45.4 0 102.5-30.4 135.5-71.3z" />
          </svg>
          <span>Admin</span>
        </div>

        <nav class="nav">
          <RouterLink :to="{ name: 'admin-products' }" class="nav-link">
            <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.5">
              <rect x="2" y="3" width="7" height="7" rx="1" /><rect x="15" y="3" width="7" height="7" rx="1" />
              <rect x="2" y="14" width="7" height="7" rx="1" /><rect x="15" y="14" width="7" height="7" rx="1" />
            </svg>
            Products
          </RouterLink>
          <RouterLink :to="{ name: 'admin-categories' }" class="nav-link">
            <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.5">
              <path d="M4 6h16M4 12h16M4 18h10" />
            </svg>
            Categories
          </RouterLink>
          <RouterLink :to="{ name: 'admin-orders' }" class="nav-link">
            <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.5">
              <path d="M6 2L3 6v14a2 2 0 002 2h14a2 2 0 002-2V6l-3-4zM3 6h18M16 10a4 4 0 01-8 0" />
            </svg>
            Orders
          </RouterLink>
        </nav>
      </div>

      <div class="sidebar-bottom">
        <RouterLink to="/" class="store-link">View Store</RouterLink>
        <button class="logout-btn" @click="handleLogout">Sign Out</button>
      </div>
    </aside>

    <main class="admin-main">
      <RouterView />
    </main>
  </div>
</template>

<style scoped>
.admin-shell {
  display: flex;
  min-height: 100vh;
  background: #f5f5f7;
}

/* Sidebar */
.sidebar {
  width: 224px;
  flex-shrink: 0;
  background: #1d1d1f;
  display: flex;
  flex-direction: column;
  justify-content: space-between;
  position: fixed;
  top: 0;
  left: 0;
  height: 100vh;
  z-index: 50;
}

.sidebar-top {
  display: flex;
  flex-direction: column;
}

.brand {
  display: flex;
  align-items: center;
  gap: 10px;
  padding: 28px 24px;
  color: #f5f5f7;
  font-size: 1rem;
  font-weight: 600;
  letter-spacing: -0.01em;
  border-bottom: 1px solid #3a3a3c;
}

.nav {
  display: flex;
  flex-direction: column;
  padding: 12px 0;
}

.nav-link {
  display: flex;
  align-items: center;
  gap: 12px;
  padding: 10px 24px;
  font-size: 0.9375rem;
  color: #a1a1a6;
  text-decoration: none;
  transition: color 0.15s, background 0.15s;
  border-radius: 0;
}

.nav-link:hover {
  color: #f5f5f7;
  background: rgba(255,255,255,0.05);
}

.nav-link.router-link-active {
  color: #f5f5f7;
  font-weight: 500;
  background: rgba(255,255,255,0.08);
}

.sidebar-bottom {
  padding: 20px 24px;
  border-top: 1px solid #3a3a3c;
  display: flex;
  flex-direction: column;
  gap: 8px;
}

.store-link {
  font-size: 0.875rem;
  color: #a1a1a6;
  text-decoration: none;
  transition: color 0.15s;
}

.store-link:hover {
  color: #f5f5f7;
}

.logout-btn {
  background: none;
  border: none;
  color: #a1a1a6;
  font-size: 0.875rem;
  font-family: inherit;
  text-align: left;
  cursor: pointer;
  padding: 0;
  transition: color 0.15s;
}

.logout-btn:hover {
  color: #ff453a;
}

/* Main */
.admin-main {
  margin-left: 224px;
  flex: 1;
  padding: 48px;
  min-height: 100vh;
}
</style>
