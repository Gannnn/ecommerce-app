<script setup lang="ts">
import { ref } from 'vue'
import type { CartItem } from '@/stores/cart'
import { useCartStore } from '@/stores/cart'
import { useCurrencyStore } from '@/stores/currency'

const props = defineProps<{ item: CartItem }>()

const cart = useCartStore()
const currency = useCurrencyStore()
const updating = ref(false)
const removing = ref(false)

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
  removing.value = true
  try {
    await cart.removeItem(props.item.id)
  } finally {
    removing.value = false
  }
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
      <p class="cart-unit-price">{{ currency.formatPrice(item.product.price) }} each</p>
    </div>
    <div class="qty-stepper" :class="{ 'is-updating': updating }">
      <template v-if="updating">
        <span class="stepper-spinner" />
      </template>
      <template v-else>
        <button class="qty-btn" :disabled="item.quantity <= 1" @click="changeQty(-1)">−</button>
        <span class="qty-value">{{ item.quantity }}</span>
        <button
          class="qty-btn"
          :disabled="item.quantity >= item.product.stock_quantity"
          @click="changeQty(1)"
        >+</button>
      </template>
    </div>
    <p class="cart-subtotal">{{ currency.formatPrice(item.product.price * item.quantity) }}</p>
    <button class="remove-btn" :disabled="removing" title="Remove" @click="remove">
      <span v-if="removing" class="remove-spinner" />
      <svg v-else width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
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
  gap: 20px;
  padding: 20px 0;
  border-bottom: 1px solid var(--apple-border);
}

.cart-image {
  width: 80px;
  height: 80px;
  object-fit: contain;
  background: #f5f5f7;
  border-radius: 14px;
  padding: 10px;
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

.remove-btn:hover:not(:disabled) {
  color: var(--apple-red);
  background: rgba(255, 59, 48, 0.08);
}

.remove-btn:disabled {
  cursor: not-allowed;
}

@media (max-width: 767px) {
  .cart-row {
    display: grid;
    grid-template-columns: 72px 1fr auto;
    grid-template-rows: auto auto;
    column-gap: 12px;
    row-gap: 10px;
    padding: 16px 0;
  }

  .cart-image {
    grid-column: 1;
    grid-row: 1 / 3;
    width: 72px;
    height: 72px;
    align-self: center;
  }

  .cart-details {
    grid-column: 2;
    grid-row: 1;
    align-self: center;
  }

  .remove-btn {
    grid-column: 3;
    grid-row: 1;
    align-self: start;
    margin-top: 2px;
  }

  .qty-stepper {
    grid-column: 2;
    grid-row: 2;
    align-self: center;
    width: fit-content;
  }

  .cart-subtotal {
    grid-column: 3;
    grid-row: 2;
    align-self: center;
    width: auto;
    text-align: right;
  }
}

.qty-stepper.is-updating {
  min-width: 80px;
  justify-content: center;
}

.stepper-spinner,
.remove-spinner {
  display: inline-block;
  border-radius: 50%;
  animation: row-spin 0.6s linear infinite;
}

.stepper-spinner {
  width: 14px;
  height: 14px;
  border: 1.5px solid var(--apple-border);
  border-top-color: var(--apple-dark);
}

.remove-spinner {
  width: 13px;
  height: 13px;
  border: 1.5px solid rgba(0, 0, 0, 0.15);
  border-top-color: var(--apple-mid);
}

@keyframes row-spin {
  to { transform: rotate(360deg); }
}
</style>
