<script setup lang="ts">
import { ref, onMounted } from 'vue'
import { useRoute, useRouter } from 'vue-router'
import { productsService } from '@/api/services/products.service'
import { useCartStore } from '@/stores/cart'
import AppSpinner from '@/components/AppSpinner.vue'
import type { Product } from '@/api/types'

const route = useRoute()
const router = useRouter()
const cart = useCartStore()

const product = ref<Product | null>(null)
const loading = ref(true)
const adding = ref(false)
const added = ref(false)
const error = ref('')

onMounted(async () => {
  try {
    product.value = await productsService.getBySlug(route.params.slug as string)
  } catch {
    error.value = 'Product not found.'
  } finally {
    loading.value = false
  }
})

async function addToCart() {
  if (!product.value || adding.value || product.value.stock_quantity === 0) return
  adding.value = true
  try {
    await cart.addItem(product.value.id, 1)
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
  <div class="detail-page">
    <!-- Loading -->
    <div v-if="loading" class="state-center">
      <AppSpinner size="28px" />
    </div>

    <!-- Error -->
    <div v-else-if="error" class="state-center">
      <p class="error-text">{{ error }}</p>
      <button class="btn-link" @click="router.back()">Go back</button>
    </div>

    <!-- Product -->
    <div v-else-if="product" class="product-layout">
      <!-- Breadcrumb -->
      <nav class="breadcrumb">
        <button class="crumb-link" @click="router.push('/')">Store</button>
        <span class="crumb-sep">/</span>
        <button
          class="crumb-link"
          @click="router.push(`/?category=${product.category?.slug}`)"
        >{{ product.category?.name }}</button>
        <span class="crumb-sep">/</span>
        <span class="crumb-current">{{ product.name }}</span>
      </nav>

      <div class="two-col">
        <!-- Image -->
        <div class="image-col">
          <div class="image-wrap">
            <img
              :src="product.image_url"
              :alt="product.name"
              class="product-img"
              @error="($event.target as HTMLImageElement).src =
                `https://placehold.co/800x800/ffffff/1d1d1f?text=${encodeURIComponent(product.name)}`"
            />
          </div>
        </div>

        <!-- Info -->
        <div class="info-col">
          <p v-if="product.is_featured" class="label-new">New</p>

          <h1 class="product-name">{{ product.name }}</h1>
          <p class="product-price">{{ formatPrice(product.price) }}</p>

          <p class="product-description">{{ product.description }}</p>

          <div class="stock-row">
            <span v-if="product.stock_quantity > 10" class="stock in-stock">In Stock</span>
            <span v-else-if="product.stock_quantity > 0" class="stock low-stock">
              Only {{ product.stock_quantity }} left
            </span>
            <span v-else class="stock out-of-stock">Out of Stock</span>
          </div>

          <button
            class="add-btn"
            :class="{ added, 'out-of-stock': product.stock_quantity === 0 }"
            :disabled="adding || product.stock_quantity === 0"
            @click="addToCart"
          >
            <span v-if="adding" class="btn-spinner" />
            <template v-else-if="product.stock_quantity === 0">Out of Stock</template>
            <template v-else-if="added">Added to Cart</template>
            <template v-else>Add to Cart</template>
          </button>

          <button class="back-btn" @click="router.back()">
            <svg width="12" height="12" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
              <path d="M19 12H5M12 5l-7 7 7 7" />
            </svg>
            Continue Shopping
          </button>
        </div>
      </div>
    </div>
  </div>
</template>

<style scoped>
.detail-page {
  max-width: 1080px;
  margin: 0 auto;
  padding: 40px 48px 80px;
}

/* Loading / error */
.state-center {
  display: flex;
  flex-direction: column;
  align-items: center;
  justify-content: center;
  padding: 100px 0;
  gap: 16px;
}

.error-text {
  font-size: 1rem;
  color: #6e6e73;
  margin: 0;
}

.btn-link {
  background: none;
  border: none;
  color: #0071e3;
  font-size: 0.9375rem;
  font-family: inherit;
  cursor: pointer;
  padding: 0;
}

/* Breadcrumb */
.breadcrumb {
  display: flex;
  align-items: center;
  gap: 6px;
  margin-bottom: 36px;
  font-size: 0.8125rem;
}

.crumb-link {
  background: none;
  border: none;
  color: #0071e3;
  font-size: 0.8125rem;
  font-family: inherit;
  cursor: pointer;
  padding: 0;
}

.crumb-link:hover { text-decoration: underline; }

.crumb-sep { color: #d2d2d7; }

.crumb-current {
  color: #6e6e73;
  white-space: nowrap;
  overflow: hidden;
  text-overflow: ellipsis;
  max-width: 240px;
}

/* Two-column layout */
.two-col {
  display: grid;
  grid-template-columns: 1fr 1fr;
  gap: 64px;
  align-items: start;
}

/* Image */
.image-col {
  position: sticky;
  top: 80px;
}

.image-wrap {
  background: #fff;
  border: 1px solid #e8e8ed;
  border-radius: 20px;
  aspect-ratio: 1 / 1;
  display: flex;
  align-items: center;
  justify-content: center;
  overflow: hidden;
  padding: 8%;
}

.product-img {
  width: 100%;
  height: 100%;
  object-fit: contain;
  transition: transform 0.5s ease;
}

.image-wrap:hover .product-img {
  transform: scale(1.03);
}

/* Info */
.info-col {
  padding-top: 12px;
}

.label-new {
  font-size: 0.75rem;
  font-weight: 600;
  color: #bf4800;
  text-transform: uppercase;
  letter-spacing: 0.05em;
  margin: 0 0 10px;
}

.product-name {
  font-size: 2rem;
  font-weight: 600;
  color: #1d1d1f;
  letter-spacing: -0.025em;
  line-height: 1.2;
  margin: 0 0 12px;
}

.product-price {
  font-size: 1.25rem;
  font-weight: 400;
  color: #1d1d1f;
  margin: 0 0 28px;
}

.product-description {
  font-size: 0.9375rem;
  color: #6e6e73;
  line-height: 1.6;
  margin: 0 0 28px;
}

/* Stock */
.stock-row {
  margin-bottom: 24px;
}

.stock {
  font-size: 0.875rem;
  font-weight: 500;
}

.in-stock { color: #28cd41; }
.low-stock { color: #ff9500; }
.out-of-stock { color: #ff3b30; }

/* Buttons */
.add-btn {
  display: block;
  width: 100%;
  background: #0071e3;
  color: #fff;
  border: none;
  border-radius: 980px;
  padding: 16px 24px;
  font-size: 1rem;
  font-weight: 400;
  font-family: inherit;
  cursor: pointer;
  transition: background 0.2s;
  margin-bottom: 14px;
  text-align: center;
}

.add-btn:hover:not(:disabled) { background: #0077ed; }

.btn-spinner {
  display: inline-block;
  width: 15px;
  height: 15px;
  border: 1.5px solid rgba(255, 255, 255, 0.35);
  border-top-color: #fff;
  border-radius: 50%;
  animation: btn-spin 0.6s linear infinite;
  vertical-align: middle;
}

@keyframes btn-spin {
  to { transform: rotate(360deg); }
}

.add-btn.added { background: #28cd41; }

.add-btn.out-of-stock {
  background: #d2d2d7;
  color: #6e6e73;
  cursor: not-allowed;
}

.back-btn {
  display: flex;
  align-items: center;
  gap: 6px;
  background: none;
  border: none;
  color: #0071e3;
  font-size: 0.875rem;
  font-family: inherit;
  cursor: pointer;
  padding: 0;
  margin: 0 auto;
  width: fit-content;
}

.back-btn:hover { text-decoration: underline; }
</style>
