<script setup lang="ts">
import { ref } from 'vue'
import { useRouter } from 'vue-router'
import { useCartStore } from '@/stores/cart'
import { useAuthStore } from '@/stores/auth'
import { useOrderStore } from '@/stores/orders'
import { ApiError } from '@/api/client'
import CartItemRow from '@/components/CartItemRow.vue'

const cart = useCartStore()
const auth = useAuthStore()
const orderStore = useOrderStore()
const router = useRouter()

const placing = ref(false)
const error = ref('')

function formatPrice(price: number): string {
  return new Intl.NumberFormat('en-US', { style: 'currency', currency: 'USD' }).format(price)
}

async function placeOrder() {
  if (!auth.isAuthenticated) {
    router.push({ name: 'login', query: { redirect: '/orders' } })
    return
  }
  placing.value = true
  error.value = ''
  try {
    await orderStore.placeOrder()
    router.push('/orders')
  } catch (e: unknown) {
    error.value = e instanceof ApiError ? e.message : 'Failed to place order.'
  } finally {
    placing.value = false
  }
}
</script>

<template>
  <div class="cart-page">
    <div class="page-container">
      <h1 class="section-title">Your Bag</h1>

      <div v-if="cart.loading" class="loading">
        <div class="spinner" />
      </div>

      <div v-else-if="cart.items.length === 0" class="empty-cart">
        <div class="empty-icon">🛍</div>
        <p class="empty-title">Your bag is empty.</p>
        <p class="empty-sub">Add some products to get started.</p>
        <RouterLink to="/" class="btn-primary">Continue Shopping</RouterLink>
      </div>

      <div v-else class="cart-layout">
        <div class="cart-items">
          <CartItemRow v-for="item in cart.items" :key="item.id" :item="item" />
        </div>

        <div class="cart-summary">
          <div class="summary-card">
            <h2 class="summary-title">Order Summary</h2>
            <div class="summary-row">
              <span>Subtotal ({{ cart.itemCount }} items)</span>
              <span>{{ formatPrice(cart.subtotal) }}</span>
            </div>
            <div class="summary-row">
              <span>Shipping</span>
              <span class="free">Free</span>
            </div>
            <div class="summary-divider" />
            <div class="summary-row total">
              <span>Total</span>
              <span>{{ formatPrice(cart.subtotal) }}</span>
            </div>
            <p v-if="error" class="error-msg">{{ error }}</p>
            <button
              class="btn-primary checkout-btn"
              :disabled="placing"
              @click="placeOrder"
            >
              {{ auth.isAuthenticated ? (placing ? 'Placing Order…' : 'Place Order') : 'Sign In to Checkout' }}
            </button>
            <button class="btn-secondary clear-btn" @click="cart.clearCart()">Clear Bag</button>
          </div>
        </div>
      </div>
    </div>
  </div>
</template>

<style scoped>
.cart-page {
  padding: 48px 0 80px;
}

.section-title {
  margin-bottom: 40px;
}

.loading {
  display: flex;
  justify-content: center;
  padding: 80px 0;
}

.spinner {
  width: 36px;
  height: 36px;
  border: 3px solid var(--apple-border);
  border-top-color: var(--apple-blue);
  border-radius: 50%;
  animation: spin 0.7s linear infinite;
}

@keyframes spin {
  to { transform: rotate(360deg); }
}

.empty-cart {
  text-align: center;
  padding: 80px 0;
}

.empty-icon {
  font-size: 4rem;
  margin-bottom: 16px;
}

.empty-title {
  font-size: 1.5rem;
  font-weight: 600;
  color: var(--apple-dark);
  margin: 0 0 8px;
}

.empty-sub {
  color: var(--apple-mid);
  margin: 0 0 24px;
}

.cart-layout {
  display: grid;
  grid-template-columns: 1fr 340px;
  gap: 40px;
  align-items: start;
}

.summary-card {
  background: var(--apple-light);
  border-radius: 18px;
  padding: 28px;
  position: sticky;
  top: 72px;
}

.summary-title {
  font-size: 1.125rem;
  font-weight: 600;
  color: var(--apple-dark);
  margin: 0 0 20px;
}

.summary-row {
  display: flex;
  justify-content: space-between;
  font-size: 0.9375rem;
  color: var(--apple-dark);
  margin-bottom: 12px;
}

.free {
  color: var(--apple-green);
  font-weight: 500;
}

.summary-divider {
  border: none;
  border-top: 1px solid var(--apple-border);
  margin: 16px 0;
}

.summary-row.total {
  font-weight: 600;
  font-size: 1.0625rem;
  margin-bottom: 20px;
}

.error-msg {
  color: var(--apple-red);
  font-size: 0.875rem;
  margin-bottom: 12px;
}

.checkout-btn {
  width: 100%;
  padding: 14px;
  font-size: 1rem;
  margin-bottom: 10px;
}

.clear-btn {
  width: 100%;
  padding: 13px;
  font-size: 0.875rem;
}

@media (max-width: 768px) {
  .cart-layout {
    grid-template-columns: 1fr;
  }
}
</style>
