<script setup lang="ts">
import { useAdminAuthStore } from '@/stores/adminAuth'
import { useRouter } from 'vue-router'

const adminAuth = useAdminAuthStore()
const router = useRouter()

async function handleLogout() {
  await adminAuth.logout()
  router.push({ name: 'admin-login' })
}
</script>

<template>
  <div class="admin-shell">
    <aside class="sidebar">
      <div class="sidebar-top">
        <div class="brand">
          <svg width="16" height="20" viewBox="0 0 814 1000" fill="currentColor">
            <path d="M788.1 340.9c-5.8 4.5-108.2 62.2-108.2 190.5 0 148.4 130.3 200.9 134.2 202.2-.6 3.2-20.7 71.9-68.7 141.9-42.8 61.6-87.5 123.1-155.5 123.1s-85.5-39.5-164-39.5c-76 0-103.7 40.8-165.9 40.8s-105-57.8-155.5-127.4C46 790.7 0 663.4 0 541.8c0-192.5 125.4-294.1 248.7-294.1 66.1 0 121.2 43.4 162.7 43.4 39.5 0 101.1-46 176.3-46 28.5 0 130.9 2.6 198.3 99.2zm-234-181.5c31.1-36.9 53.1-88.1 53.1-139.3 0-7.1-.6-14.3-1.9-20.1-50.6 1.9-110.8 33.7-147.1 75.8-28.5 32.4-55.1 83.6-55.1 135.5 0 7.8 1.3 15.6 1.9 18.1 3.2.6 8.4 1.3 13.6 1.3 45.4 0 102.5-30.4 135.5-71.3z" />
          </svg>
          <div class="brand-text">
            <span class="brand-title">Admin</span>
            <span class="brand-name">{{ adminAuth.admin?.name }}</span>
          </div>
        </div>

        <nav class="nav">
          <RouterLink :to="{ name: 'admin-products' }" class="nav-link">
            <svg width="15" height="15" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.75">
              <rect x="2" y="3" width="7" height="7" rx="1.5" /><rect x="15" y="3" width="7" height="7" rx="1.5" />
              <rect x="2" y="14" width="7" height="7" rx="1.5" /><rect x="15" y="14" width="7" height="7" rx="1.5" />
            </svg>
            Products
          </RouterLink>
          <RouterLink :to="{ name: 'admin-categories' }" class="nav-link">
            <svg width="15" height="15" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.75">
              <path d="M4 6h16M4 12h16M4 18h10" />
            </svg>
            Categories
          </RouterLink>
          <RouterLink :to="{ name: 'admin-orders' }" class="nav-link">
            <svg width="15" height="15" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.75">
              <circle cx="9" cy="21" r="1" /><circle cx="20" cy="21" r="1" />
              <path d="M1 1h4l2.68 13.39a2 2 0 001.98 1.61h9.72a2 2 0 001.98-1.71L23 6H6" />
            </svg>
            Orders
          </RouterLink>
          <RouterLink :to="{ name: 'admin-currencies' }" class="nav-link">
            <svg width="15" height="15" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.75">
              <circle cx="12" cy="12" r="10" />
              <path d="M12 6v2m0 8v2M8.5 9.5A3.5 3.5 0 0112 8h.5a2.5 2.5 0 010 5h-1a2.5 2.5 0 000 5H12a3.5 3.5 0 003.5-1.5" />
            </svg>
            Currencies
          </RouterLink>
        </nav>
      </div>

      <div class="sidebar-bottom">
        <RouterLink to="/" class="bottom-link">
          <svg width="13" height="13" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.75">
            <path d="M3 9.5L12 3l9 6.5V20a1 1 0 01-1 1H4a1 1 0 01-1-1V9.5z" />
          </svg>
          View Store
        </RouterLink>
        <button class="bottom-link sign-out" @click="handleLogout">
          <svg width="13" height="13" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.75">
            <path d="M9 21H5a2 2 0 01-2-2V5a2 2 0 012-2h4M16 17l5-5-5-5M21 12H9" />
          </svg>
          Sign Out
        </button>
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
  width: 220px;
  flex-shrink: 0;
  background: rgba(255, 255, 255, 0.85);
  backdrop-filter: saturate(180%) blur(20px);
  -webkit-backdrop-filter: saturate(180%) blur(20px);
  border-right: 1px solid #d2d2d7;
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

/* Brand */
.brand {
  display: flex;
  align-items: center;
  gap: 12px;
  padding: 24px 20px;
  color: #1d1d1f;
  border-bottom: 1px solid #e5e5ea;
}

.brand-text {
  display: flex;
  flex-direction: column;
  gap: 1px;
}

.brand-title {
  font-size: 0.9375rem;
  font-weight: 600;
  color: #1d1d1f;
  letter-spacing: -0.01em;
  line-height: 1.2;
}

.brand-name {
  font-size: 0.75rem;
  color: #6e6e73;
  line-height: 1.2;
}

/* Nav */
.nav {
  display: flex;
  flex-direction: column;
  gap: 2px;
  padding: 12px 10px;
}

.nav-link {
  display: flex;
  align-items: center;
  gap: 10px;
  padding: 8px 12px;
  font-size: 0.875rem;
  color: #3a3a3c;
  text-decoration: none;
  border-radius: 10px;
  transition: background 0.15s, color 0.15s;
  font-weight: 400;
}

.nav-link:hover {
  background: rgba(0, 0, 0, 0.05);
  color: #1d1d1f;
}

.nav-link.router-link-active {
  background: rgba(0, 113, 227, 0.1);
  color: #0071e3;
  font-weight: 500;
}

/* Bottom */
.sidebar-bottom {
  padding: 12px 10px 20px;
  border-top: 1px solid #e5e5ea;
  display: flex;
  flex-direction: column;
  gap: 2px;
}

.bottom-link {
  display: flex;
  align-items: center;
  gap: 10px;
  padding: 8px 12px;
  font-size: 0.875rem;
  color: #6e6e73;
  text-decoration: none;
  border-radius: 10px;
  background: none;
  border: none;
  font-family: inherit;
  cursor: pointer;
  text-align: left;
  transition: background 0.15s, color 0.15s;
  width: 100%;
}

.bottom-link:hover {
  background: rgba(0, 0, 0, 0.05);
  color: #1d1d1f;
}

.bottom-link.sign-out:hover {
  color: #ff3b30;
  background: rgba(255, 59, 48, 0.06);
}

/* Main */
.admin-main {
  margin-left: 220px;
  flex: 1;
  padding: 48px;
  min-height: 100vh;
}
</style>
