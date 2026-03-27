<script setup lang="ts">
import { onMounted } from 'vue'
import { useRouter } from 'vue-router'
import { useAuthStore } from '@/stores/auth'
import { useOrderStore } from '@/stores/orders'
import OrderStatusBadge from '@/components/OrderStatusBadge.vue'
import AppSpinner from '@/components/AppSpinner.vue'

const auth = useAuthStore()
const orderStore = useOrderStore()
const router = useRouter()

onMounted(() => orderStore.fetchOrders())

async function handleLogout() {
  await auth.logout()
  router.push('/')
}

function formatPrice(price: number): string {
  return new Intl.NumberFormat('en-US', { style: 'currency', currency: 'USD' }).format(price)
}

function formatDate(dateStr: string): string {
  return new Date(dateStr).toLocaleDateString('en-US', {
    year: 'numeric',
    month: 'long',
    day: 'numeric',
  })
}

function memberSince(dateStr: string): string {
  return new Date(dateStr).toLocaleDateString('en-US', { year: 'numeric', month: 'long' })
}

function initials(name: string): string {
  return name
    .split(' ')
    .map((w) => w[0])
    .join('')
    .toUpperCase()
    .slice(0, 2)
}
</script>

<template>
  <div class="account-page">

    <!-- Hero -->
    <div class="page-hero">
      <div class="hero-inner">
        <h1 class="hero-title">Account</h1>
      </div>
    </div>

    <div class="page-body">

      <!-- Profile card -->
      <section class="profile-card">
        <div class="avatar">{{ initials(auth.user!.name) }}</div>
        <div class="profile-info">
          <p class="profile-name">{{ auth.user!.name }}</p>
          <p class="profile-email">{{ auth.user!.email }}</p>
          <p class="profile-since">Member since {{ memberSince(auth.user!.created_at) }}</p>
        </div>
        <button class="sign-out-btn" @click="handleLogout">Sign Out</button>
      </section>

      <!-- Orders -->
      <section class="orders-section">
        <h2 class="section-heading">Order History</h2>

        <div v-if="orderStore.loading" class="loading">
          <AppSpinner size="28px" />
        </div>

        <div v-else-if="orderStore.orders.length === 0" class="empty-state">
          <p class="empty-text">No orders yet.</p>
          <RouterLink to="/" class="shop-link">Start Shopping</RouterLink>
        </div>

        <div v-else class="orders-list">
          <div v-for="order in orderStore.orders" :key="order.id" class="order-card">

            <div class="order-header">
              <div class="order-meta">
                <span class="order-num">Order #{{ order.id }}</span>
                <span class="order-date">{{ formatDate(order.created_at) }}</span>
              </div>
              <div class="order-header-right">
                <OrderStatusBadge :status="order.status" />
                <span class="order-total">{{ formatPrice(order.total) }}</span>
              </div>
            </div>

            <div class="order-items">
              <div v-for="item in order.items" :key="item.id" class="order-item">
                <img
                  :src="item.product?.image_url ?? ''"
                  :alt="item.product_name"
                  class="item-image"
                  @error="($event.target as HTMLImageElement).src = 'https://placehold.co/64x64/f5f5f7/1d1d1f?text=?'"
                />
                <div class="item-details">
                  <span class="item-name">{{ item.product_name }}</span>
                  <span class="item-unit">{{ formatPrice(item.product_price) }} each</span>
                </div>
                <span class="item-qty">× {{ item.quantity }}</span>
                <span class="item-price">{{ formatPrice(item.subtotal) }}</span>
              </div>
            </div>

          </div>
        </div>
      </section>

    </div>
  </div>
</template>

<style scoped>
.account-page {
  min-height: 100vh;
}

/* Hero */
.page-hero {
  padding: 32px 0 28px;
  border-bottom: 1px solid #d2d2d7;
}

.hero-inner {
  max-width: 860px;
  margin: 0 auto;
  padding: 0 48px;
}

.hero-title {
  font-size: 2.25rem;
  font-weight: 600;
  color: #1d1d1f;
  margin: 0;
  letter-spacing: -0.02em;
}

/* Body */
.page-body {
  max-width: 860px;
  margin: 0 auto;
  padding: 40px 48px 100px;
  display: flex;
  flex-direction: column;
  gap: 48px;
}

/* Profile card */
.profile-card {
  display: flex;
  align-items: center;
  gap: 24px;
  background: #fff;
  border: 1px solid #d2d2d7;
  border-radius: 18px;
  padding: 28px 32px;
}

.avatar {
  width: 64px;
  height: 64px;
  border-radius: 50%;
  background: #1d1d1f;
  color: #fff;
  font-size: 1.375rem;
  font-weight: 500;
  display: flex;
  align-items: center;
  justify-content: center;
  flex-shrink: 0;
  letter-spacing: 0.02em;
}

.profile-info {
  flex: 1;
}

.profile-name {
  font-size: 1.125rem;
  font-weight: 600;
  color: #1d1d1f;
  margin: 0 0 4px;
}

.profile-email {
  font-size: 0.9375rem;
  color: #6e6e73;
  margin: 0 0 4px;
}

.profile-since {
  font-size: 0.8125rem;
  color: #aeaeb2;
  margin: 0;
}

.sign-out-btn {
  background: none;
  border: 1px solid #d2d2d7;
  border-radius: 980px;
  padding: 8px 20px;
  font-size: 0.875rem;
  font-family: inherit;
  color: #1d1d1f;
  cursor: pointer;
  white-space: nowrap;
  transition: border-color 0.15s, background 0.15s;
}

.sign-out-btn:hover {
  border-color: #1d1d1f;
  background: #f5f5f7;
}

/* Orders section */
.section-heading {
  font-size: 1.25rem;
  font-weight: 600;
  color: #1d1d1f;
  margin: 0 0 20px;
  letter-spacing: -0.01em;
}

.loading {
  display: flex;
  justify-content: center;
  padding: 60px 0;
}

.empty-state {
  text-align: center;
  padding: 60px 0;
}

.empty-text {
  font-size: 1rem;
  color: #6e6e73;
  margin: 0 0 20px;
}

.shop-link {
  display: inline-block;
  background: #0071e3;
  color: #fff;
  text-decoration: none;
  border-radius: 980px;
  padding: 10px 24px;
  font-size: 0.9375rem;
  transition: background 0.2s;
}

.shop-link:hover {
  background: #0077ed;
}

.orders-list {
  display: flex;
  flex-direction: column;
  gap: 16px;
}

.order-card {
  background: #fff;
  border: 1px solid #d2d2d7;
  border-radius: 18px;
  overflow: hidden;
}

.order-header {
  display: flex;
  justify-content: space-between;
  align-items: center;
  padding: 16px 24px;
  background: #f5f5f7;
  border-bottom: 1px solid #d2d2d7;
  gap: 16px;
}

.order-meta {
  display: flex;
  flex-direction: column;
  gap: 2px;
}

.order-num {
  font-size: 0.9375rem;
  font-weight: 600;
  color: #1d1d1f;
}

.order-date {
  font-size: 0.8125rem;
  color: #6e6e73;
}

.order-header-right {
  display: flex;
  align-items: center;
  gap: 16px;
  flex-shrink: 0;
}

.order-total {
  font-size: 0.9375rem;
  font-weight: 600;
  color: #1d1d1f;
}

.order-items {
  padding: 4px 24px;
}

.order-item {
  display: flex;
  align-items: center;
  gap: 16px;
  padding: 14px 0;
  border-bottom: 1px solid #f5f5f7;
}

.order-item:last-child {
  border-bottom: none;
}

.item-image {
  width: 64px;
  height: 64px;
  object-fit: contain;
  background: #f5f5f7;
  border-radius: 12px;
  padding: 8px;
  flex-shrink: 0;
}

.item-details {
  flex: 1;
  min-width: 0;
  display: flex;
  flex-direction: column;
  gap: 3px;
}

.item-name {
  font-size: 0.9375rem;
  font-weight: 500;
  color: #1d1d1f;
  white-space: nowrap;
  overflow: hidden;
  text-overflow: ellipsis;
}

.item-unit {
  font-size: 0.8125rem;
  color: #6e6e73;
}

.item-qty {
  color: #6e6e73;
  font-size: 0.875rem;
  white-space: nowrap;
  flex-shrink: 0;
}

.item-price {
  font-size: 0.9375rem;
  font-weight: 500;
  color: #1d1d1f;
  min-width: 72px;
  text-align: right;
  flex-shrink: 0;
}

@media (max-width: 767px) {
  .hero-inner {
    padding: 0 20px;
  }

  .page-body {
    padding: 28px 20px 80px;
  }

  .profile-card {
    flex-wrap: wrap;
    gap: 16px;
    padding: 20px;
  }

  .sign-out-btn {
    width: 100%;
    text-align: center;
  }

  .order-header {
    flex-direction: column;
    align-items: flex-start;
    gap: 8px;
    padding: 14px 16px;
  }

  .order-header-right {
    flex-direction: row;
    gap: 12px;
  }

  .order-items {
    padding: 4px 16px;
  }

  .item-image {
    width: 52px;
    height: 52px;
  }

  .item-qty,
  .item-price {
    font-size: 0.8125rem;
  }
}
</style>
