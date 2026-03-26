<script setup lang="ts">
import { ref, onMounted, computed } from 'vue'
import { adminProductsService, adminCategoriesService } from '@/api/services/admin.service'
import { ApiError } from '@/api/client'
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
      <h1 class="page-title">Products</h1>
      <button class="btn-primary" @click="openCreate">+ New Product</button>
    </div>

    <div v-if="loading" class="loading">
      <div class="spinner" />
    </div>

    <div v-else class="card">
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
          <tr v-for="p in products" :key="p.id">
            <td class="td-img">
              <img :src="p.image_url" :alt="p.name" class="product-thumb" />
            </td>
            <td class="td-name">{{ p.name }}</td>
            <td class="td-meta">{{ p.category?.name ?? '—' }}</td>
            <td class="td-meta">{{ formatPrice(p.price) }}</td>
            <td class="td-meta">{{ p.stock_quantity }}</td>
            <td class="td-meta">
              <span class="badge" :class="p.is_featured ? 'badge-blue' : 'badge-grey'">
                {{ p.is_featured ? 'Yes' : 'No' }}
              </span>
            </td>
            <td class="td-actions">
              <button class="action-btn" @click="openEdit(p)">Edit</button>
              <button class="action-btn danger" @click="handleDelete(p)">Delete</button>
            </td>
          </tr>
        </tbody>
      </table>
    </div>

    <!-- Modal -->
    <Teleport to="body">
      <div v-if="showModal" class="overlay" @click.self="closeModal">
        <div class="modal">
          <div class="modal-header">
            <h2 class="modal-title">{{ modalTitle }}</h2>
            <button class="close-btn" @click="closeModal">
              <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                <line x1="18" y1="6" x2="6" y2="18" /><line x1="6" y1="6" x2="18" y2="18" />
              </svg>
            </button>
          </div>

          <form class="modal-form" @submit.prevent="handleSubmit">
            <!-- Image upload -->
            <div class="image-upload-area" @click="($refs.fileInput as HTMLInputElement).click()">
              <img v-if="imagePreview" :src="imagePreview" class="image-preview" />
              <div v-else class="image-placeholder">
                <svg width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.5">
                  <rect x="3" y="3" width="18" height="18" rx="2" /><circle cx="8.5" cy="8.5" r="1.5" />
                  <path d="M21 15l-5-5L5 21" />
                </svg>
                <span>Click to upload image</span>
              </div>
              <input ref="fileInput" type="file" accept="image/*" class="file-input" @change="onFileChange" />
            </div>

            <div class="form-row">
              <div class="field">
                <label>Name</label>
                <input v-model="form.name" type="text" required />
              </div>
              <div class="field">
                <label>Category</label>
                <select v-model="form.category_id" required>
                  <option value="0" disabled>Select category</option>
                  <option v-for="cat in categories" :key="cat.id" :value="cat.id">{{ cat.name }}</option>
                </select>
              </div>
            </div>

            <div class="field">
              <label>Description</label>
              <textarea v-model="form.description" rows="3" required />
            </div>

            <div class="form-row">
              <div class="field">
                <label>Price (USD)</label>
                <input v-model="form.price" type="number" min="0" step="0.01" required />
              </div>
              <div class="field">
                <label>Stock Quantity</label>
                <input v-model="form.stock_quantity" type="number" min="0" required />
              </div>
            </div>

            <label class="checkbox-label">
              <input v-model="form.is_featured" type="checkbox" />
              Mark as Featured (shows "New" label)
            </label>

            <p v-if="error" class="error-msg">{{ error }}</p>

            <div class="modal-footer">
              <button type="button" class="btn-secondary" @click="closeModal">Cancel</button>
              <button type="submit" class="btn-primary" :disabled="saving">
                {{ saving ? 'Saving…' : (editing ? 'Save Changes' : 'Create Product') }}
              </button>
            </div>
          </form>
        </div>
      </div>
    </Teleport>
  </div>
</template>

<style scoped>
.page-header {
  display: flex;
  align-items: center;
  justify-content: space-between;
  margin-bottom: 32px;
}

.page-title {
  font-size: 1.75rem;
  font-weight: 600;
  color: #1d1d1f;
  margin: 0;
  letter-spacing: -0.02em;
}

.btn-primary {
  background: #0071e3;
  color: #fff;
  border: none;
  border-radius: 980px;
  padding: 9px 20px;
  font-size: 0.875rem;
  font-family: inherit;
  cursor: pointer;
  font-weight: 400;
  white-space: nowrap;
}

.btn-primary:hover:not(:disabled) { background: #0077ed; }
.btn-primary:disabled { opacity: 0.6; cursor: not-allowed; }

.btn-secondary {
  background: none;
  border: 1px solid #d2d2d7;
  border-radius: 980px;
  padding: 9px 20px;
  font-size: 0.875rem;
  font-family: inherit;
  color: #1d1d1f;
  cursor: pointer;
}

.btn-secondary:hover { background: #f5f5f7; }

.loading {
  display: flex;
  justify-content: center;
  padding: 80px 0;
}

.spinner {
  width: 36px;
  height: 36px;
  border: 3px solid #d2d2d7;
  border-top-color: #0071e3;
  border-radius: 50%;
  animation: spin 0.7s linear infinite;
}

@keyframes spin { to { transform: rotate(360deg); } }

.card {
  background: #fff;
  border-radius: 18px;
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
  padding: 12px 16px;
  text-align: left;
  font-size: 0.75rem;
  font-weight: 600;
  color: #6e6e73;
  text-transform: uppercase;
  letter-spacing: 0.04em;
  border-bottom: 1px solid #d2d2d7;
}

.th-img { width: 64px; }

.table td {
  padding: 14px 16px;
  border-bottom: 1px solid #f0f0f0;
  vertical-align: middle;
}

.table tr:last-child td { border-bottom: none; }

.td-img { width: 64px; }

.product-thumb {
  width: 44px;
  height: 44px;
  object-fit: contain;
  border-radius: 8px;
  background: #f5f5f7;
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
  font-size: 0.75rem;
  font-weight: 500;
}

.badge-blue { background: #e8f0fe; color: #0071e3; }
.badge-grey { background: #f0f0f0; color: #6e6e73; }

.td-actions {
  display: flex;
  gap: 8px;
  justify-content: flex-end;
}

.action-btn {
  background: none;
  border: 1px solid #d2d2d7;
  border-radius: 8px;
  padding: 5px 14px;
  font-size: 0.8125rem;
  font-family: inherit;
  color: #1d1d1f;
  cursor: pointer;
  transition: background 0.15s;
}

.action-btn:hover { background: #f5f5f7; }
.action-btn.danger { color: #ff3b30; border-color: #ffd0ce; }
.action-btn.danger:hover { background: #fff5f4; }

/* Modal */
.overlay {
  position: fixed;
  inset: 0;
  background: rgba(0,0,0,0.45);
  display: flex;
  align-items: center;
  justify-content: center;
  z-index: 200;
  padding: 24px;
}

.modal {
  background: #fff;
  border-radius: 20px;
  width: 100%;
  max-width: 560px;
  max-height: 90vh;
  overflow-y: auto;
  box-shadow: 0 24px 60px rgba(0,0,0,0.2);
}

.modal-header {
  display: flex;
  align-items: center;
  justify-content: space-between;
  padding: 24px 28px 0;
}

.modal-title {
  font-size: 1.25rem;
  font-weight: 600;
  color: #1d1d1f;
  margin: 0;
}

.close-btn {
  background: none;
  border: none;
  color: #6e6e73;
  cursor: pointer;
  padding: 4px;
  border-radius: 50%;
  display: flex;
}

.close-btn:hover { color: #1d1d1f; }

.modal-form {
  padding: 20px 28px 28px;
  display: flex;
  flex-direction: column;
  gap: 16px;
}

/* Image upload */
.image-upload-area {
  border: 2px dashed #d2d2d7;
  border-radius: 12px;
  height: 160px;
  display: flex;
  align-items: center;
  justify-content: center;
  cursor: pointer;
  overflow: hidden;
  transition: border-color 0.15s;
  position: relative;
}

.image-upload-area:hover { border-color: #0071e3; }

.image-preview {
  width: 100%;
  height: 100%;
  object-fit: contain;
  padding: 8px;
}

.image-placeholder {
  display: flex;
  flex-direction: column;
  align-items: center;
  gap: 8px;
  color: #6e6e73;
  font-size: 0.875rem;
}

.file-input {
  position: absolute;
  inset: 0;
  opacity: 0;
  cursor: pointer;
}

.form-row {
  display: grid;
  grid-template-columns: 1fr 1fr;
  gap: 12px;
}

.field {
  display: flex;
  flex-direction: column;
  gap: 6px;
}

.field label {
  font-size: 0.8125rem;
  font-weight: 500;
  color: #1d1d1f;
}

.field input,
.field select,
.field textarea {
  padding: 9px 12px;
  border: 1px solid #d2d2d7;
  border-radius: 10px;
  font-size: 0.9375rem;
  font-family: inherit;
  color: #1d1d1f;
  background: #fff;
  outline: none;
  resize: vertical;
}

.field input:focus,
.field select:focus,
.field textarea:focus {
  border-color: #0071e3;
}

.checkbox-label {
  display: flex;
  align-items: center;
  gap: 8px;
  font-size: 0.9375rem;
  color: #1d1d1f;
  cursor: pointer;
}

.error-msg {
  color: #ff3b30;
  font-size: 0.875rem;
  margin: 0;
}

.modal-footer {
  display: flex;
  justify-content: flex-end;
  gap: 12px;
  padding-top: 8px;
}
</style>
