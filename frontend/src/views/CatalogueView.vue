<script setup lang="ts">
import { ref, computed, onMounted, onUnmounted, watch } from 'vue'
import { useRoute, useRouter } from 'vue-router'
import { useProductStore } from '@/stores/products'
import { categoriesService } from '@/api/services/categories.service'
import type { Category, GetProductsParams } from '@/api/types'
import ProductCard from '@/components/ProductCard.vue'
import AppSpinner from '@/components/AppSpinner.vue'

const route = useRoute()
const router = useRouter()
const productStore = useProductStore()
const categories = ref<Category[]>([])
const search = ref('')
const sort = ref<GetProductsParams['sort']>('newest')
const filterOpen = ref(false)
const sortOpen = ref(false)

const sortOptions: { label: string; value: GetProductsParams['sort'] }[] = [
  { label: 'Newest',             value: 'newest' },
  { label: 'Price: Low to High', value: 'price-asc' },
  { label: 'Price: High to Low', value: 'price-desc' },
]

const sortLabel = computed(() => sortOptions.find((o) => o.value === sort.value)?.label ?? 'Newest')

const selectedCategory = ref((route.query.category as string) || '')

function fetchWithParams() {
  productStore.fetchProducts({
    search: search.value || undefined,
    category: selectedCategory.value || undefined,
    sort: sort.value,
  })
}

onMounted(async () => {
  const [cats] = await Promise.all([categoriesService.getAll(), fetchWithParams()])
  categories.value = cats
  document.addEventListener('click', handleOutsideClick)
})

onUnmounted(() => {
  document.removeEventListener('click', handleOutsideClick)
})

function handleOutsideClick(e: MouseEvent) {
  const target = e.target as HTMLElement
  if (!target.closest('.sort-dropdown')) sortOpen.value = false
}

watch(
  () => route.query.category,
  (val) => {
    selectedCategory.value = (val as string) || ''
    fetchWithParams()
  },
)

let searchTimer: ReturnType<typeof setTimeout> | null = null
watch(search, () => {
  if (searchTimer) clearTimeout(searchTimer)
  searchTimer = setTimeout(fetchWithParams, 300)
})
watch(sort, fetchWithParams)

function selectCategory(slug: string) {
  selectedCategory.value = slug
  router.replace({ query: slug ? { category: slug } : {} })
  fetchWithParams()
}

function selectSort(value: GetProductsParams['sort']) {
  sort.value = value
  sortOpen.value = false
}

const pageTitle = computed(() => {
  if (search.value) return `Results for "${search.value}"`
  const cat = categories.value.find((c) => c.slug === selectedCategory.value)
  return cat ? cat.name : 'Store'
})
</script>

<template>
  <div class="catalogue-page">

    <!-- Page title -->
    <div class="page-hero">
      <div class="hero-inner">
        <h1 class="hero-title">{{ pageTitle }}</h1>
      </div>
    </div>

    <!-- Filter + Sort bar -->
    <div class="controls-bar">
      <div class="controls-inner">
        <button class="filter-toggle" @click="filterOpen = !filterOpen">
          <svg width="14" height="12" viewBox="0 0 16 14" fill="none" stroke="currentColor" stroke-width="1.5">
            <line x1="0" y1="2" x2="16" y2="2" />
            <line x1="0" y1="7" x2="16" y2="7" />
            <line x1="0" y1="12" x2="16" y2="12" />
            <circle cx="5" cy="2" r="1.5" fill="currentColor" stroke="none" />
            <circle cx="11" cy="7" r="1.5" fill="currentColor" stroke="none" />
            <circle cx="5" cy="12" r="1.5" fill="currentColor" stroke="none" />
          </svg>
          Filter
          <span v-if="selectedCategory || search" class="filter-dot" />
        </button>

        <!-- Custom sort dropdown -->
        <div class="sort-group">
          <span class="sort-label">Sort By</span>
          <div class="sort-dropdown">
            <button class="sort-btn" @click="sortOpen = !sortOpen">
              {{ sortLabel }}
              <svg
                class="sort-chevron"
                :class="{ open: sortOpen }"
                width="10" height="10" viewBox="0 0 24 24"
                fill="none" stroke="currentColor" stroke-width="2.5"
              >
                <path d="M6 9l6 6 6-6" />
              </svg>
            </button>
            <Transition name="dropdown">
              <div v-if="sortOpen" class="sort-menu">
                <button
                  v-for="opt in sortOptions"
                  :key="opt.value"
                  class="sort-option"
                  :class="{ selected: sort === opt.value }"
                  @click="selectSort(opt.value)"
                >
                  <svg
                    v-if="sort === opt.value"
                    width="12" height="12" viewBox="0 0 24 24"
                    fill="none" stroke="currentColor" stroke-width="2.5"
                  >
                    <path d="M20 6L9 17l-5-5" />
                  </svg>
                  <span v-else class="sort-spacer" />
                  {{ opt.label }}
                </button>
              </div>
            </Transition>
          </div>
        </div>
      </div>
    </div>

    <!-- Mobile filter backdrop -->
    <Transition name="backdrop">
      <div v-if="filterOpen" class="filter-backdrop" @click="filterOpen = false" />
    </Transition>

    <!-- Body: sidebar + grid -->
    <div class="body-layout" :class="{ 'filter-open': filterOpen }">

      <!-- Filter panel -->
      <div class="filter-panel-wrap" :class="{ 'is-open': filterOpen }">
        <aside class="filter-panel">
          <div class="search-wrap">
            <svg class="search-icon" width="13" height="13" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
              <circle cx="11" cy="11" r="8" /><line x1="21" y1="21" x2="16.65" y2="16.65" />
            </svg>
            <input v-model="search" type="text" placeholder="Search" class="search-input" />
          </div>

          <p class="panel-heading">Category</p>
          <nav class="category-list">
            <button
              class="cat-btn"
              :class="{ active: selectedCategory === '' }"
              @click="selectCategory('')"
            >All Products</button>
            <button
              v-for="cat in categories"
              :key="cat.id"
              class="cat-btn"
              :class="{ active: selectedCategory === cat.slug }"
              @click="selectCategory(cat.slug)"
            >{{ cat.name }}</button>
          </nav>
        </aside>
      </div>

      <!-- Product grid -->
      <main class="grid-area">
        <p class="result-count">{{ productStore.products.length }} Products</p>

        <div v-if="productStore.loading" class="loading">
          <AppSpinner size="28px" />
        </div>

        <div v-else-if="productStore.products.length === 0" class="empty-state">
          No products found.
        </div>

        <div v-else class="product-grid">
          <ProductCard
            v-for="product in productStore.products"
            :key="product.id"
            :product="product"
          />
        </div>
      </main>
    </div>

  </div>
</template>

<style scoped>
.catalogue-page {
  /* App.vue main has padding-top: 44px */
}

/* Hero */
.page-hero {
  padding: 32px 0 28px;
  border-bottom: 1px solid #d2d2d7;
}

.hero-inner {
  max-width: 1200px;
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

/* Controls bar */
.controls-bar {
  border-bottom: 1px solid #d2d2d7;
}

.controls-inner {
  max-width: 1200px;
  margin: 0 auto;
  padding: 0 48px;
  height: 52px;
  display: flex;
  align-items: center;
  justify-content: space-between;
}

.filter-toggle {
  display: flex;
  align-items: center;
  gap: 8px;
  background: none;
  border: none;
  font-size: 0.875rem;
  font-family: inherit;
  color: #1d1d1f;
  cursor: pointer;
  padding: 0;
  position: relative;
}

.filter-toggle:hover { color: #0071e3; }

.filter-dot {
  width: 6px;
  height: 6px;
  background: #0071e3;
  border-radius: 50%;
  position: absolute;
  top: -2px;
  right: -10px;
}

/* Sort dropdown */
.sort-group {
  display: flex;
  align-items: center;
  gap: 8px;
}

.sort-label {
  font-size: 0.875rem;
  color: #6e6e73;
}

.sort-dropdown {
  position: relative;
}

.sort-btn {
  display: flex;
  align-items: center;
  gap: 6px;
  background: none;
  border: 1px solid #d2d2d7;
  border-radius: 980px;
  padding: 6px 14px;
  font-size: 0.875rem;
  font-family: inherit;
  color: #1d1d1f;
  cursor: pointer;
  transition: border-color 0.15s, background 0.15s;
  white-space: nowrap;
}

.sort-btn:hover {
  border-color: #1d1d1f;
  background: #f5f5f7;
}

.sort-chevron {
  transition: transform 0.2s ease;
  color: #6e6e73;
}

.sort-chevron.open {
  transform: rotate(180deg);
}

.sort-menu {
  position: absolute;
  right: 0;
  top: calc(100% + 8px);
  background: #fff;
  border: 1px solid #d2d2d7;
  border-radius: 14px;
  box-shadow: 0 8px 32px rgba(0, 0, 0, 0.12);
  overflow: hidden;
  min-width: 200px;
  z-index: 50;
}

.sort-option {
  display: flex;
  align-items: center;
  gap: 10px;
  width: 100%;
  padding: 11px 16px;
  background: none;
  border: none;
  font-size: 0.9375rem;
  font-family: inherit;
  color: #1d1d1f;
  cursor: pointer;
  text-align: left;
  transition: background 0.1s;
}

.sort-option:hover { background: #f5f5f7; }

.sort-option.selected {
  color: #0071e3;
  font-weight: 500;
}

.sort-spacer {
  width: 12px;
  display: inline-block;
}

/* Dropdown transition */
.dropdown-enter-active {
  transition: opacity 0.15s ease, transform 0.15s ease;
}
.dropdown-leave-active {
  transition: opacity 0.1s ease, transform 0.1s ease;
}
.dropdown-enter-from {
  opacity: 0;
  transform: translateY(-6px) scale(0.97);
}
.dropdown-leave-to {
  opacity: 0;
  transform: translateY(-4px) scale(0.98);
}

/* Body layout */
.body-layout {
  max-width: 1200px;
  margin: 0 auto;
  padding: 0 48px 80px;
  display: flex;
  align-items: flex-start;
}

/* Filter panel */
.filter-panel-wrap {
  flex-shrink: 0;
  max-width: 0;
  overflow: hidden;
  opacity: 0;
  transition:
    max-width 0.38s cubic-bezier(0.4, 0, 0.2, 1),
    opacity 0.3s ease;
}

.filter-panel-wrap.is-open {
  max-width: 265px;
  opacity: 1;
}

.filter-panel {
  width: 240px;
  padding: 24px 24px 24px 0;
  border-right: 1px solid #d2d2d7;
  position: sticky;
  top: 52px;
}

.search-wrap {
  position: relative;
  margin-bottom: 20px;
}

.search-icon {
  position: absolute;
  left: 10px;
  top: 50%;
  transform: translateY(-50%);
  color: #6e6e73;
  pointer-events: none;
}

.search-input {
  width: 100%;
  padding: 7px 12px 7px 30px;
  border: 1px solid #d2d2d7;
  border-radius: 8px;
  font-size: 0.8125rem;
  font-family: inherit;
  color: #1d1d1f;
  background: #f5f5f7;
  outline: none;
  box-sizing: border-box;
}

.search-input:focus {
  border-color: #0071e3;
  background: #fff;
}

.panel-heading {
  font-size: 0.75rem;
  font-weight: 600;
  text-transform: uppercase;
  letter-spacing: 0.06em;
  color: #6e6e73;
  margin: 0 0 8px;
}

.category-list {
  display: flex;
  flex-direction: column;
}

.cat-btn {
  text-align: left;
  padding: 8px 0;
  background: none;
  border: none;
  border-bottom: 1px solid #f0f0f0;
  font-size: 0.9375rem;
  font-family: inherit;
  color: #1d1d1f;
  cursor: pointer;
  transition: color 0.15s;
  width: 100%;
  white-space: nowrap;
}

.cat-btn:last-child { border-bottom: none; }
.cat-btn:hover { color: #0071e3; }
.cat-btn.active { color: #0071e3; font-weight: 500; }

/* Grid area */
.grid-area {
  flex: 1;
  min-width: 0;
  padding: 20px 0 0 32px;
}

.result-count {
  font-size: 0.8125rem;
  color: #6e6e73;
  margin: 0 0 16px;
}

.product-grid {
  display: grid;
  grid-template-columns: repeat(3, 1fr);
  border-top: 1px solid #d2d2d7;
  border-left: 1px solid #d2d2d7;
}

.product-grid :deep(.product-card) {
  border-right: 1px solid #d2d2d7;
  border-bottom: 1px solid #d2d2d7;
}

.loading {
  display: flex;
  justify-content: center;
  padding: 80px 0;
}

.empty-state {
  text-align: center;
  padding: 80px 0;
  color: #6e6e73;
  font-size: 0.9375rem;
}

/* Mobile filter backdrop */
.filter-backdrop {
  display: none;
}

.backdrop-enter-active { transition: opacity 0.25s ease; }
.backdrop-leave-active { transition: opacity 0.2s ease; }
.backdrop-enter-from, .backdrop-leave-to { opacity: 0; }

@media (max-width: 767px) {
  .hero-inner,
  .controls-inner {
    padding: 0 20px;
  }

  .hero-title {
    font-size: 1.75rem;
  }

  .body-layout {
    padding: 0 20px 60px;
  }

  /* Side drawer */
  .filter-panel-wrap {
    position: fixed;
    top: 44px;
    left: 0;
    bottom: 0;
    width: 280px;
    max-width: none;
    max-height: none;
    opacity: 1;
    overflow-y: auto;
    background: #fff;
    z-index: 90;
    transform: translateX(-100%);
    transition: transform 0.35s cubic-bezier(0.4, 0, 0.2, 1);
    box-shadow: 4px 0 24px rgba(0, 0, 0, 0.08);
  }

  .filter-panel-wrap.is-open {
    max-width: none;
    transform: translateX(0);
    opacity: 1;
  }

  .filter-panel {
    width: 100%;
    padding: 24px 20px;
    border-right: none;
    position: static;
  }

  .filter-backdrop {
    display: block;
    position: fixed;
    inset: 44px 0 0 0;
    background: rgba(0, 0, 0, 0.3);
    z-index: 89;
  }

  .grid-area {
    padding: 20px 0 0;
  }

  .product-grid {
    grid-template-columns: repeat(2, 1fr);
  }
}

@media (max-width: 400px) {
  .product-grid {
    grid-template-columns: 1fr;
  }
}
</style>
