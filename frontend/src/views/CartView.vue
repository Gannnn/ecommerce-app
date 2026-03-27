<script setup lang="ts">
import { ref } from 'vue'
import { useRouter } from 'vue-router'
import { useCartStore } from '@/stores/cart'
import { useAuthStore } from '@/stores/auth'
import { useOrderStore } from '@/stores/orders'
import { ApiError } from '@/api/client'
import CartItemRow from '@/components/CartItemRow.vue'
import AppSpinner from '@/components/AppSpinner.vue'

const cart = useCartStore()
const auth = useAuthStore()
const orderStore = useOrderStore()
const router = useRouter()

const placing = ref(false)
const clearing = ref(false)
const error = ref('')

function formatPrice(price: number): string {
  return new Intl.NumberFormat('en-US', { style: 'currency', currency: 'USD' }).format(price)
}

async function clearCart() {
  clearing.value = true
  try {
    await cart.clearCart()
  } finally {
    clearing.value = false
  }
}

async function placeOrder() {
  if (!auth.isAuthenticated) {
    router.push({ name: 'login', query: { redirect: '/account' } })
    return
  }
  placing.value = true
  error.value = ''
  try {
    await orderStore.placeOrder()
    router.push('/account')
  } catch (e: unknown) {
    error.value = e instanceof ApiError ? e.message : 'Failed to place order.'
  } finally {
    placing.value = false
  }
}
</script>

<template>
  <div class="cart-page">

    <!-- Hero -->
    <div class="page-hero">
      <div class="hero-inner">
        <h1 class="hero-title">Your Cart</h1>
      </div>
    </div>

    <div class="page-body">

      <!-- Loading -->
      <div v-if="cart.loading" class="loading">
        <AppSpinner size="28px" />
      </div>

      <!-- Empty -->
      <div v-else-if="cart.items.length === 0" class="empty-state">
        <div class="empty-icon">
          <svg width="48" height="48" viewBox="0 0 24 24" fill="none" stroke="#d2d2d7" stroke-width="1.2">
            <path d="M6 2L3 6v14a2 2 0 002 2h14a2 2 0 002-2V6l-3-4zM3 6h18M16 10a4 4 0 01-8 0" />
          </svg>
        </div>
        <p class="empty-title">Your cart is empty.</p>
        <p class="empty-sub">Add some products to get started.</p>
        <RouterLink to="/" class="btn-primary">Continue Shopping</RouterLink>
      </div>

      <!-- Cart -->
      <div v-else class="cart-layout">

        <!-- Items -->
        <div class="cart-items">
          <CartItemRow
            v-for="item in cart.items"
            :key="item.id"
            :item="item"
          />
          <div class="items-footer">
            <button class="clear-btn" :disabled="clearing" @click="clearCart">
              <span v-if="clearing" class="clear-spinner" />
              <template v-else>Clear Cart</template>
            </button>
          </div>
        </div>

        <!-- Summary -->
        <aside class="summary-card">
          <h2 class="summary-title">Order Summary</h2>

          <div class="summary-rows">
            <div class="summary-row">
              <span>Subtotal ({{ cart.itemCount }} {{ cart.itemCount === 1 ? 'item' : 'items' }})</span>
              <span>{{ formatPrice(cart.subtotal) }}</span>
            </div>
            <div class="summary-row">
              <span>Shipping</span>
              <span class="free">Free</span>
            </div>
          </div>

          <div class="summary-divider" />

          <div class="summary-total">
            <span>Total</span>
            <span>{{ formatPrice(cart.subtotal) }}</span>
          </div>

          <p v-if="error" class="error-msg">{{ error }}</p>

          <button
            class="checkout-btn"
            :disabled="placing"
            @click="placeOrder"
          >
            {{ auth.isAuthenticated ? (placing ? 'Placing Order…' : 'Place Order') : 'Sign In to Checkout' }}
          </button>

          <p class="checkout-note">Free delivery. No hidden fees.</p>
        </aside>

      </div>
    </div>
  </div>
</template>

<style scoped>
.cart-page {
  min-height: 100vh;
}

/* Hero */
.page-hero {
  padding: 32px 0 28px;
  border-bottom: 1px solid #d2d2d7;
}

.hero-inner {
  max-width: 1000px;
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
  max-width: 1000px;
  margin: 0 auto;
  padding: 40px 48px 100px;
}

/* Loading */
.loading {
  display: flex;
  justify-content: center;
  padding: 80px 0;
}

/* Empty */
.empty-state {
  text-align: center;
  padding: 80px 0;
}

.empty-icon {
  display: flex;
  justify-content: center;
  margin-bottom: 20px;
}

.empty-title {
  font-size: 1.375rem;
  font-weight: 600;
  color: #1d1d1f;
  margin: 0 0 8px;
}

.empty-sub {
  font-size: 0.9375rem;
  color: #6e6e73;
  margin: 0 0 28px;
}

/* Layout */
.cart-layout {
  display: grid;
  grid-template-columns: 1fr 300px;
  gap: 48px;
  align-items: start;
}

/* Items */
.cart-items {
  border-top: 1px solid #d2d2d7;
}

.items-footer {
  padding: 16px 0 0;
  display: flex;
  justify-content: flex-end;
}

.clear-btn {
  background: none;
  border: none;
  font-family: inherit;
  font-size: 0.8125rem;
  color: #6e6e73;
  cursor: pointer;
  padding: 0;
  transition: color 0.15s;
}

.clear-btn:hover:not(:disabled) {
  color: #ff3b30;
}

.clear-spinner {
  display: inline-block;
  width: 11px;
  height: 11px;
  border: 1.5px solid rgba(0, 0, 0, 0.15);
  border-top-color: #6e6e73;
  border-radius: 50%;
  animation: clear-spin 0.6s linear infinite;
  vertical-align: middle;
}

@keyframes clear-spin {
  to { transform: rotate(360deg); }
}

/* Summary */
.summary-card {
  background: #fff;
  border: 1px solid #d2d2d7;
  border-radius: 18px;
  padding: 28px;
  position: sticky;
  top: 64px;
}

.summary-title {
  font-size: 1rem;
  font-weight: 600;
  color: #1d1d1f;
  margin: 0 0 20px;
  letter-spacing: -0.01em;
}

.summary-rows {
  display: flex;
  flex-direction: column;
  gap: 12px;
}

.summary-row {
  display: flex;
  justify-content: space-between;
  font-size: 0.9375rem;
  color: #1d1d1f;
}

.free {
  color: #28cd41;
  font-weight: 500;
}

.summary-divider {
  border: none;
  border-top: 1px solid #d2d2d7;
  margin: 20px 0;
}

.summary-total {
  display: flex;
  justify-content: space-between;
  font-size: 1rem;
  font-weight: 600;
  color: #1d1d1f;
  margin-bottom: 24px;
}

.error-msg {
  font-size: 0.875rem;
  color: #ff3b30;
  margin: 0 0 12px;
}

.checkout-btn {
  width: 100%;
  background: #0071e3;
  color: #fff;
  border: none;
  border-radius: 980px;
  padding: 14px;
  font-size: 0.9375rem;
  font-family: inherit;
  font-weight: 400;
  cursor: pointer;
  transition: background 0.2s;
}

.checkout-btn:hover:not(:disabled) {
  background: #0077ed;
}

.checkout-btn:disabled {
  opacity: 0.5;
  cursor: not-allowed;
}

.checkout-note {
  text-align: center;
  font-size: 0.75rem;
  color: #aeaeb2;
  margin: 12px 0 0;
}

@media (max-width: 767px) {
  .hero-inner {
    padding: 0 20px;
  }

  .hero-title {
    font-size: 1.75rem;
  }

  .page-body {
    padding: 24px 20px 80px;
  }

  .cart-layout {
    grid-template-columns: 1fr;
    gap: 32px;
  }

  .summary-card {
    position: static;
  }
}
</style>
