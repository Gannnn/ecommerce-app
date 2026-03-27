<script setup lang="ts">
import { ref, onMounted, computed } from 'vue'
import { adminProductsService, adminCategoriesService } from '@/api/services/admin.service'
import { ApiError } from '@/api/client'
import AppSpinner from '@/components/AppSpinner.vue'
import type { Product, Category, AdminProductPayload } from '@/api/types'

const products = ref<Product[]>([])
const categories = ref<Category[]>([])
const loading = ref(false)
const saving = ref(false)
const showModal = ref(false)
const editing = ref<Product | null>(null)
const error = ref('')

const emptyForm = (): AdminProductPayload => ({
  category_id: 0,
  name: '',
  description: '',
  price: '',
  stock_quantity: '',
  is_featured: false,
  image: null,
})

const form = ref<AdminProductPayload>(emptyForm())
const imagePreview = ref<string | null>(null)

function openCreate() {
  editing.value = null
  form.value = emptyForm()
  imagePreview.value = null
  error.value = ''
  showModal.value = true
}

function openEdit(p: Product) {
  editing.value = p
  form.value = {
    category_id: p.category_id,
    name: p.name,
    description: p.description,
    price: p.price,
    stock_quantity: p.stock_quantity,
    is_featured: p.is_featured,
    image: null,
  }
  imagePreview.value = p.image_url
  error.value = ''
  showModal.value = true
}

function closeModal() {
  showModal.value = false
}

function onFileChange(e: Event) {
  const file = (e.target as HTMLInputElement).files?.[0]
  if (!file) return
  form.value.image = file
  imagePreview.value = URL.createObjectURL(file)
}

async function fetchAll() {
  loading.value = true
  try {
    products.value = await adminProductsService.getAll()
  } finally {
    loading.value = false
  }
}

async function handleSubmit() {
  error.value = ''
  saving.value = true
  try {
    if (editing.value) {
      await adminProductsService.update(editing.value.id, form.value)
    } else {
      await adminProductsService.create(form.value as AdminProductPayload)
    }
    closeModal()
    await fetchAll()
  } catch (e) {
    error.value = e instanceof ApiError ? e.message : 'Something went wrong.'
  } finally {
    saving.value = false
  }
}

async function handleDelete(p: Product) {
  if (!confirm(`Delete "${p.name}"? This cannot be undone.`)) return
  await adminProductsService.destroy(p.id)
  await fetchAll()
}

function formatPrice(price: number): string {
  return new Intl.NumberFormat('en-US', { style: 'currency', currency: 'USD' }).format(price)
}

const modalTitle = computed(() => (editing.value ? 'Edit Product' : 'New Product'))

onMounted(async () => {
  ;[products.value, categories.value] = await Promise.all([
    adminProductsService.getAll(),
    adminCategoriesService.getAll(),
  ])
})
</script>

<template>
  <div class="page">
    <div class="page-header">
      <div>
        <h1 class="page-title">Products</h1>
        <p class="page-sub">{{ products.length }} total</p>
      </div>
      <button class="btn-primary" @click="openCreate">
        <svg width="13" height="13" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5">
          <line x1="12" y1="5" x2="12" y2="19" /><line x1="5" y1="12" x2="19" y2="12" />
        </svg>
        New Product
      </button>
    </div>

    <div v-if="loading" class="loading">
      <AppSpinner size="28px" />
    </div>

    <div v-else-if="products.length === 0" class="empty-state">
      No products yet.
    </div>

    <div v-else class="table-card">
      <table class="table">
        <thead>
          <tr>
            <th class="th-img"></th>
            <th>Name</th>
            <th>Category</th>
            <th>Price</th>
            <th>Stock</th>
            <th>Featured</th>
            <th></th>
          </tr>
        </thead>
        <tbody>
          <tr v-for="p in products" :key="p.id" class="table-row">
            <td class="td-img">
              <img :src="p.image_url" :alt="p.name" class="product-thumb"
                @error="($event.target as HTMLImageElement).src = 'https://placehold.co/44x44/f5f5f7/1d1d1f?text=?'" />
            </td>
            <td class="td-name">{{ p.name }}</td>
            <td class="td-meta">{{ p.category?.name ?? '—' }}</td>
            <td class="td-meta">{{ formatPrice(p.price) }}</td>
            <td class="td-meta">{{ p.stock_quantity }}</td>
            <td class="td-meta">
              <span class="badge" :class="p.is_featured ? 'badge-blue' : 'badge-grey'">
                {{ p.is_featured ? 'Featured' : 'Standard' }}
              </span>
            </td>
            <td class="td-actions">
              <button class="icon-btn" title="Edit" @click="openEdit(p)">
                <svg width="15" height="15" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.75">
                  <path d="M11 4H4a2 2 0 00-2 2v14a2 2 0 002 2h14a2 2 0 002-2v-7" />
                  <path d="M18.5 2.5a2.121 2.121 0 013 3L12 15l-4 1 1-4 9.5-9.5z" />
                </svg>
              </button>
              <button class="icon-btn danger" title="Delete" @click="handleDelete(p)">
                <svg width="15" height="15" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.75">
                  <polyline points="3 6 5 6 21 6" />
                  <path d="M19 6l-1 14a2 2 0 01-2 2H8a2 2 0 01-2-2L5 6" />
                  <path d="M10 11v6M14 11v6M9 6V4a1 1 0 011-1h4a1 1 0 011 1v2" />
                </svg>
              </button>
            </td>
          </tr>
        </tbody>
      </table>
    </div>

    <!-- Modal -->
    <Teleport to="body">
      <Transition name="modal">
        <div v-if="showModal" class="overlay" @click.self="closeModal">
          <div class="modal">
            <div class="modal-header">
              <h2 class="modal-title">{{ modalTitle }}</h2>
              <button class="close-btn" @click="closeModal">
                <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5">
                  <line x1="18" y1="6" x2="6" y2="18" /><line x1="6" y1="6" x2="18" y2="18" />
                </svg>
              </button>
            </div>

            <form class="modal-form" @submit.prevent="handleSubmit">
              <div class="image-upload-area" @click="($refs.fileInput as HTMLInputElement).click()">
                <img v-if="imagePreview" :src="imagePreview" class="image-preview" />
                <div v-else class="image-placeholder">
                  <svg width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="#aeaeb2" stroke-width="1.25">
                    <rect x="3" y="3" width="18" height="18" rx="3" />
                    <circle cx="8.5" cy="8.5" r="1.5" fill="#aeaeb2" stroke="none" />
                    <path d="M21 15l-5-5L5 21" />
                  </svg>
                  <span>Click to upload photo</span>
                </div>
                <input ref="fileInput" type="file" accept="image/*" class="file-input" @change="onFileChange" />
              </div>

              <div class="field-group">
                <div class="field">
                  <label>Name</label>
                  <input v-model="form.name" type="text" placeholder="Product name" required />
                </div>
                <div class="field-divider" />
                <div class="field">
                  <label>Category</label>
                  <select v-model="form.category_id" required>
                    <option value="0" disabled>Select category</option>
                    <option v-for="cat in categories" :key="cat.id" :value="cat.id">{{ cat.name }}</option>
                  </select>
                </div>
                <div class="field-divider" />
                <div class="field">
                  <label>Description</label>
                  <textarea v-model="form.description" rows="3" placeholder="Short description" required />
                </div>
              </div>

              <div class="field-group">
                <div class="form-row">
                  <div class="field">
                    <label>Price (USD)</label>
                    <input v-model="form.price" type="number" min="0" step="0.01" placeholder="0.00" required />
                  </div>
                  <div class="field">
                    <label>Stock Quantity</label>
                    <input v-model="form.stock_quantity" type="number" min="0" placeholder="0" required />
                  </div>
                </div>
              </div>

              <label class="toggle-row">
                <div class="toggle-info">
                  <span class="toggle-label">Featured</span>
                  <span class="toggle-hint">Shows "New" badge on this product</span>
                </div>
                <div class="toggle-switch">
                  <input v-model="form.is_featured" type="checkbox" />
                  <span class="toggle-slider" />
                </div>
              </label>

              <p v-if="error" class="error-msg">{{ error }}</p>

              <div class="modal-footer">
                <button type="button" class="btn-ghost" @click="closeModal">Cancel</button>
                <button type="submit" class="btn-primary" :disabled="saving">
                  {{ saving ? 'Saving…' : (editing ? 'Save Changes' : 'Create Product') }}
                </button>
              </div>
            </form>
          </div>
        </div>
      </Transition>
    </Teleport>
  </div>
</template>

<style scoped>
.page-header {
  display: flex;
  align-items: flex-start;
  justify-content: space-between;
  padding-bottom: 24px;
  margin-bottom: 32px;
  border-bottom: 1px solid #d2d2d7;
}

.page-title {
  font-size: 1.75rem;
  font-weight: 600;
  color: #1d1d1f;
  margin: 0 0 2px;
  letter-spacing: -0.02em;
}

.page-sub {
  font-size: 0.8125rem;
  color: #6e6e73;
  margin: 0;
}

.btn-primary {
  display: flex;
  align-items: center;
  gap: 6px;
  background: #0071e3;
  color: #fff;
  border: none;
  border-radius: 980px;
  padding: 9px 18px;
  font-size: 0.875rem;
  font-family: inherit;
  cursor: pointer;
  font-weight: 400;
  white-space: nowrap;
  flex-shrink: 0;
}

.btn-primary:hover:not(:disabled) { background: #0077ed; }
.btn-primary:disabled { opacity: 0.6; cursor: not-allowed; }

.btn-ghost {
  background: none;
  border: 1px solid #d2d2d7;
  border-radius: 980px;
  padding: 9px 20px;
  font-size: 0.875rem;
  font-family: inherit;
  color: #1d1d1f;
  cursor: pointer;
  transition: background 0.15s;
}

.btn-ghost:hover { background: #f5f5f7; }

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

.table-card {
  background: #fff;
  border-radius: 16px;
  overflow: hidden;
  border: 1px solid #d2d2d7;
}

.table {
  width: 100%;
  border-collapse: collapse;
}

.table thead {
  background: #f5f5f7;
}

.table th {
  padding: 11px 16px;
  text-align: left;
  font-size: 0.6875rem;
  font-weight: 600;
  color: #6e6e73;
  text-transform: uppercase;
  letter-spacing: 0.06em;
  border-bottom: 1px solid #d2d2d7;
}

.th-img { width: 56px; }

.table td {
  padding: 14px 16px;
  border-bottom: 1px solid #f0f0f5;
  vertical-align: middle;
}

.table-row:last-child td { border-bottom: none; }

.table-row:hover td { background: #fafafa; }

.td-img { width: 56px; }

.product-thumb {
  width: 40px;
  height: 40px;
  object-fit: contain;
  border-radius: 8px;
  background: #f5f5f7;
  padding: 4px;
}

.td-name {
  font-size: 0.9375rem;
  font-weight: 500;
  color: #1d1d1f;
}

.td-meta {
  font-size: 0.875rem;
  color: #6e6e73;
}

.badge {
  display: inline-block;
  padding: 3px 10px;
  border-radius: 980px;
  font-size: 0.6875rem;
  font-weight: 600;
  letter-spacing: 0.02em;
}

.badge-blue { background: rgba(0,113,227,0.1); color: #0071e3; }
.badge-grey { background: #f0f0f5; color: #6e6e73; }

.td-actions {
  display: flex;
  gap: 4px;
  justify-content: flex-end;
}

.icon-btn {
  display: flex;
  align-items: center;
  justify-content: center;
  width: 32px;
  height: 32px;
  background: none;
  border: none;
  border-radius: 8px;
  color: #6e6e73;
  cursor: pointer;
  transition: background 0.15s, color 0.15s;
}

.icon-btn:hover {
  background: #f0f0f5;
  color: #1d1d1f;
}

.icon-btn.danger:hover {
  background: rgba(255, 59, 48, 0.08);
  color: #ff3b30;
}

/* Modal transition */
.modal-enter-active { transition: opacity 0.22s ease; }
.modal-leave-active { transition: opacity 0.16s ease; }
.modal-enter-from, .modal-leave-to { opacity: 0; }
.modal-enter-active .modal { transition: transform 0.26s cubic-bezier(0.34, 1.4, 0.64, 1); }
.modal-leave-active .modal { transition: transform 0.16s ease; }
.modal-enter-from .modal { transform: scale(0.93) translateY(10px); }
.modal-leave-to .modal { transform: scale(0.96) translateY(4px); }

.overlay {
  position: fixed;
  inset: 0;
  background: rgba(0, 0, 0, 0.35);
  backdrop-filter: blur(6px);
  -webkit-backdrop-filter: blur(6px);
  display: flex;
  align-items: center;
  justify-content: center;
  z-index: 200;
  padding: 24px;
}

.modal {
  background: #fff;
  border-radius: 22px;
  width: 100%;
  max-width: 560px;
  max-height: 90vh;
  overflow-y: auto;
  box-shadow: 0 40px 100px rgba(0, 0, 0, 0.2), 0 0 0 1px rgba(0,0,0,0.06);
}

.modal-header {
  display: flex;
  align-items: center;
  justify-content: space-between;
  padding: 22px 24px 20px;
  border-bottom: 1px solid #f0f0f5;
}

.modal-title {
  font-size: 1rem;
  font-weight: 600;
  color: #1d1d1f;
  margin: 0;
  letter-spacing: -0.01em;
}

.close-btn {
  display: flex;
  align-items: center;
  justify-content: center;
  width: 26px;
  height: 26px;
  background: #e5e5ea;
  border: none;
  border-radius: 50%;
  color: #3a3a3c;
  cursor: pointer;
  transition: background 0.15s;
  flex-shrink: 0;
}

.close-btn:hover { background: #d2d2d7; }

.modal-form {
  padding: 20px 24px 24px;
  display: flex;
  flex-direction: column;
  gap: 14px;
}

/* Image upload */
.image-upload-area {
  border: 1.5px dashed #d2d2d7;
  border-radius: 14px;
  height: 140px;
  display: flex;
  align-items: center;
  justify-content: center;
  cursor: pointer;
  overflow: hidden;
  transition: border-color 0.15s, background 0.15s;
  position: relative;
  background: #fafafa;
}

.image-upload-area:hover { border-color: #0071e3; background: #f0f7ff; }

.image-preview {
  width: 100%;
  height: 100%;
  object-fit: contain;
  padding: 10px;
}

.image-placeholder {
  display: flex;
  flex-direction: column;
  align-items: center;
  gap: 8px;
  color: #aeaeb2;
  font-size: 0.8125rem;
}

.file-input {
  position: absolute;
  inset: 0;
  opacity: 0;
  cursor: pointer;
}

/* Field groups */
.field-group {
  background: #f5f5f7;
  border-radius: 14px;
  padding: 0 14px;
  overflow: hidden;
}

.field-group .field {
  padding: 12px 0;
}

.field-divider {
  height: 1px;
  background: #e5e5ea;
  margin: 0;
}

.field-group .form-row {
  display: grid;
  grid-template-columns: 1fr 1fr;
  gap: 0 16px;
  padding: 12px 0;
}

.field-group .form-row .field {
  padding: 0;
}

.field {
  display: flex;
  flex-direction: column;
  gap: 5px;
}

.field label {
  font-size: 0.75rem;
  font-weight: 500;
  color: #6e6e73;
  letter-spacing: 0.01em;
}

.field input,
.field select,
.field textarea {
  padding: 0;
  border: none;
  border-radius: 0;
  font-size: 0.9375rem;
  font-family: inherit;
  color: #1d1d1f;
  background: transparent;
  outline: none;
  resize: none;
  width: 100%;
  box-sizing: border-box;
}

.field input::placeholder,
.field textarea::placeholder { color: #aeaeb2; }

.field textarea { resize: vertical; min-height: 60px; }

/* Toggle */
.toggle-row {
  display: flex;
  align-items: center;
  justify-content: space-between;
  background: #f5f5f7;
  border-radius: 14px;
  padding: 14px;
  cursor: pointer;
  gap: 16px;
}

.toggle-info {
  display: flex;
  flex-direction: column;
  gap: 2px;
}

.toggle-label {
  font-size: 0.9375rem;
  font-weight: 500;
  color: #1d1d1f;
}

.toggle-hint {
  font-size: 0.8125rem;
  color: #6e6e73;
}

.toggle-switch {
  position: relative;
  width: 44px;
  height: 26px;
  flex-shrink: 0;
}

.toggle-switch input {
  opacity: 0;
  width: 0;
  height: 0;
  position: absolute;
}

.toggle-slider {
  position: absolute;
  inset: 0;
  background: #c7c7cc;
  border-radius: 13px;
  transition: background 0.2s;
  cursor: pointer;
}

.toggle-slider::before {
  content: '';
  position: absolute;
  width: 22px;
  height: 22px;
  left: 2px;
  top: 2px;
  background: #fff;
  border-radius: 50%;
  transition: transform 0.22s cubic-bezier(0.34, 1.4, 0.64, 1);
  box-shadow: 0 1px 4px rgba(0, 0, 0, 0.22);
}

.toggle-switch input:checked + .toggle-slider { background: #0071e3; }
.toggle-switch input:checked + .toggle-slider::before { transform: translateX(18px); }

.error-msg {
  color: #ff3b30;
  font-size: 0.875rem;
  margin: 0;
  padding: 0 2px;
}

.modal-footer {
  display: flex;
  justify-content: flex-end;
  gap: 10px;
  padding-top: 6px;
  border-top: 1px solid #f0f0f5;
  margin-top: 2px;
}
</style>
