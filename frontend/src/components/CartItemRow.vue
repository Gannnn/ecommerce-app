<script setup lang="ts">
import { ref } from 'vue'
import type { CartItem } from '@/stores/cart'
import { useCartStore } from '@/stores/cart'

const props = defineProps<{ item: CartItem }>()

const cart = useCartStore()
const updating = ref(false)

function formatPrice(price: number): string {
  return new Intl.NumberFormat('en-US', { style: 'currency', currency: 'USD' }).format(price)
}

async function changeQty(delta: number) {
  const newQty = props.item.quantity + delta
  if (newQty < 1) return
  updating.value = true
  try {
    await cart.updateItem(props.item.id, newQty)
  } finally {
    updating.value = false
  }
}

async function remove() {
  await cart.removeItem(props.item.id)
}
</script>

<template>
  <div class="cart-row">
    <img
      :src="item.product.image_url"
      :alt="item.product.name"
      class="cart-image"
      @error="($event.target as HTMLImageElement).src = `https://placehold.co/80x80/f5f5f7/1d1d1f?text=?`"
    />
    <div class="cart-details">
      <p class="cart-name">{{ item.product.name }}</p>
      <p class="cart-unit-price">{{ formatPrice(item.product.price) }} each</p>
    </div>
    <div class="qty-stepper">
      <button class="qty-btn" :disabled="item.quantity <= 1 || updating" @click="changeQty(-1)">−</button>
      <span class="qty-value">{{ item.quantity }}</span>
      <button
        class="qty-btn"
        :disabled="item.quantity >= item.product.stock_quantity || updating"
        @click="changeQty(1)"
      >+</button>
    </div>
    <p class="cart-subtotal">{{ formatPrice(item.product.price * item.quantity) }}</p>
    <button class="remove-btn" title="Remove" @click="remove">
      <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
        <line x1="18" y1="6" x2="6" y2="18" />
        <line x1="6" y1="6" x2="18" y2="18" />
      </svg>
    </button>
  </div>
</template>

<style scoped>
.cart-row {
  display: flex;
  align-items: center;
  gap: 16px;
  padding: 16px 0;
  border-bottom: 1px solid var(--apple-border);
}

.cart-image {
  width: 72px;
  height: 72px;
  object-fit: contain;
  background: var(--apple-light);
  border-radius: 12px;
  padding: 8px;
  flex-shrink: 0;
}

.cart-details {
  flex: 1;
  min-width: 0;
}

.cart-name {
  font-size: 0.9375rem;
  font-weight: 500;
  color: var(--apple-dark);
  margin: 0 0 4px;
  white-space: nowrap;
  overflow: hidden;
  text-overflow: ellipsis;
}

.cart-unit-price {
  font-size: 0.8125rem;
  color: var(--apple-mid);
  margin: 0;
}

.qty-stepper {
  display: flex;
  align-items: center;
  gap: 12px;
  border: 1px solid var(--apple-border);
  border-radius: 980px;
  padding: 4px 8px;
}

.qty-btn {
  background: none;
  border: none;
  cursor: pointer;
  font-size: 1.1rem;
  color: var(--apple-dark);
  padding: 0 4px;
  line-height: 1;
  transition: color 0.2s;
}

.qty-btn:hover:not(:disabled) {
  color: var(--apple-blue);
}

.qty-btn:disabled {
  color: var(--apple-border);
  cursor: not-allowed;
}

.qty-value {
  font-size: 0.9375rem;
  font-weight: 500;
  min-width: 20px;
  text-align: center;
}

.cart-subtotal {
  font-size: 0.9375rem;
  font-weight: 500;
  color: var(--apple-dark);
  width: 80px;
  text-align: right;
  margin: 0;
  flex-shrink: 0;
}

.remove-btn {
  background: none;
  border: none;
  cursor: pointer;
  color: var(--apple-mid);
  padding: 4px;
  border-radius: 50%;
  display: flex;
  transition: color 0.2s, background 0.2s;
}

.remove-btn:hover {
  color: var(--apple-red);
  background: rgba(255, 59, 48, 0.08);
}
</style>
