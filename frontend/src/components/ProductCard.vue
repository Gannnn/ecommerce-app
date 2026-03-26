<script setup lang="ts">
import { ref } from 'vue'
import { useRouter } from 'vue-router'
import type { Product } from '@/stores/products'
import { useCartStore } from '@/stores/cart'

const props = defineProps<{ product: Product }>()
const router = useRouter()

function goToDetail() {
  router.push({ name: 'product-detail', params: { id: props.product.id } })
}

const cart = useCartStore()
const adding = ref(false)
const added = ref(false)

async function addToBag() {
  if (adding.value || props.product.stock_quantity === 0) return
  adding.value = true
  try {
    await cart.addItem(props.product.id, 1)
    added.value = true
    setTimeout(() => (added.value = false), 1800)
  } finally {
    adding.value = false
  }
}

function formatPrice(price: number): string {
  return new Intl.NumberFormat('en-US', { style: 'currency', currency: 'USD' }).format(price)
}
</script>

<template>
  <div class="product-card">
    <!-- Image area — navigates to detail page -->
    <div class="image-area" @click="goToDetail">
      <img
        :src="product.image_url"
        :alt="product.name"
        class="product-img"
        loading="lazy"
        @error="($event.target as HTMLImageElement).src =
          `https://placehold.co/600x600/f5f5f7/1d1d1f?text=${encodeURIComponent(product.name)}`"
      />
    </div>

    <!-- Info area -->
    <div class="product-info" @click="goToDetail">
      <p v-if="product.is_featured" class="label-new">New</p>
      <p class="product-name">{{ product.name }}</p>
      <p class="product-price">{{ formatPrice(product.price) }}</p>

      <button
        class="add-btn"
        :class="{ added, 'out-of-stock': product.stock_quantity === 0 }"
        :disabled="adding || product.stock_quantity === 0"
        @click.stop="addToBag"
      >
        <template v-if="product.stock_quantity === 0">Out of Stock</template>
        <template v-else-if="added">Added to Bag</template>
        <template v-else>Add to Bag</template>
      </button>
    </div>
  </div>
</template>

<style scoped>
.product-card {
  display: flex;
  flex-direction: column;
  background: #fff;
  cursor: pointer;
}

/* Image section — light grey background, large and square */
.image-area {
  background: #fff;
  aspect-ratio: 1 / 1;
  display: flex;
  align-items: center;
  justify-content: center;
  overflow: hidden;
  padding: 10%;
}

.product-img {
  width: 100%;
  height: 100%;
  object-fit: contain;
  transition: transform 0.4s ease;
}

.product-card:hover .product-img {
  transform: scale(1.04);
}

/* Info section */
.product-info {
  padding: 20px 24px 28px;
  display: flex;
  flex-direction: column;
  align-items: center;
  text-align: center;
  gap: 4px;
  background: #fff;
}

.label-new {
  font-size: 0.6875rem;
  font-weight: 600;
  color: #bf4800;
  text-transform: uppercase;
  letter-spacing: 0.04em;
  margin: 0 0 2px;
}

.product-name {
  font-size: 1rem;
  font-weight: 400;
  color: #1d1d1f;
  margin: 0;
  line-height: 1.4;
}

.product-price {
  font-size: 0.9375rem;
  font-weight: 400;
  color: #6e6e73;
  margin: 0 0 14px;
}

.add-btn {
  display: inline-block;
  background: #0071e3;
  color: #fff;
  border: none;
  border-radius: 980px;
  padding: 9px 22px;
  font-size: 0.875rem;
  font-weight: 400;
  font-family: inherit;
  cursor: pointer;
  transition: background 0.2s;
  white-space: nowrap;
}

.add-btn:hover:not(:disabled) {
  background: #0077ed;
}

.add-btn.added {
  background: #28cd41;
}

.add-btn.out-of-stock {
  background: #d2d2d7;
  color: #6e6e73;
  cursor: not-allowed;
}
</style>
