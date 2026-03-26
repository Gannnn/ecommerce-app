<script setup lang="ts">
import { ref, onMounted, computed } from 'vue'
import { adminCategoriesService } from '@/api/services/admin.service'
import { ApiError } from '@/api/client'
import type { Category, AdminCategoryPayload } from '@/api/types'

interface CategoryWithCount extends Category {
  products_count?: number
}

const categories = ref<CategoryWithCount[]>([])
const loading = ref(false)
const saving = ref(false)
const showModal = ref(false)
const editing = ref<Category | null>(null)
const error = ref('')

const emptyForm = (): AdminCategoryPayload => ({ name: '', description: '', sort_order: 0 })
const form = ref<AdminCategoryPayload>(emptyForm())

function openCreate() {
  editing.value = null
  form.value = emptyForm()
  error.value = ''
  showModal.value = true
}

function openEdit(cat: CategoryWithCount) {
  editing.value = cat
  form.value = { name: cat.name, description: cat.description ?? '', sort_order: cat.sort_order }
  error.value = ''
  showModal.value = true
}

function closeModal() { showModal.value = false }

async function fetchAll() {
  loading.value = true
  try {
    categories.value = await adminCategoriesService.getAll() as CategoryWithCount[]
  } finally {
    loading.value = false
  }
}

async function handleSubmit() {
  error.value = ''
  saving.value = true
  try {
    if (editing.value) {
      await adminCategoriesService.update(editing.value.id, form.value)
    } else {
      await adminCategoriesService.create(form.value)
    }
    closeModal()
    await fetchAll()
  } catch (e) {
    error.value = e instanceof ApiError ? e.message : 'Something went wrong.'
  } finally {
    saving.value = false
  }
}

async function handleDelete(cat: CategoryWithCount) {
  if (!confirm(`Delete "${cat.name}"? This cannot be undone.`)) return
  await adminCategoriesService.destroy(cat.id)
  await fetchAll()
}

const modalTitle = computed(() => (editing.value ? 'Edit Category' : 'New Category'))

onMounted(fetchAll)
</script>

<template>
  <div class="page">
    <div class="page-header">
      <h1 class="page-title">Categories</h1>
      <button class="btn-primary" @click="openCreate">+ New Category</button>
    </div>

    <div v-if="loading" class="loading">
      <div class="spinner" />
    </div>

    <div v-else class="card">
      <table class="table">
        <thead>
          <tr>
            <th>Name</th>
            <th>Slug</th>
            <th>Sort Order</th>
            <th>Products</th>
            <th></th>
          </tr>
        </thead>
        <tbody>
          <tr v-for="cat in categories" :key="cat.id">
            <td class="td-name">{{ cat.name }}</td>
            <td class="td-meta"><code class="slug">{{ cat.slug }}</code></td>
            <td class="td-meta">{{ cat.sort_order }}</td>
            <td class="td-meta">{{ cat.products_count ?? '—' }}</td>
            <td class="td-actions">
              <button class="action-btn" @click="openEdit(cat)">Edit</button>
              <button class="action-btn danger" @click="handleDelete(cat)">Delete</button>
            </td>
          </tr>
        </tbody>
      </table>
    </div>

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
            <div class="field">
              <label>Name</label>
              <input v-model="form.name" type="text" required />
            </div>
            <div class="field">
              <label>Description</label>
              <textarea v-model="form.description" rows="2" />
            </div>
            <div class="field">
              <label>Sort Order</label>
              <input v-model="form.sort_order" type="number" min="0" />
            </div>

            <p v-if="error" class="error-msg">{{ error }}</p>

            <div class="modal-footer">
              <button type="button" class="btn-secondary" @click="closeModal">Cancel</button>
              <button type="submit" class="btn-primary" :disabled="saving">
                {{ saving ? 'Saving…' : (editing ? 'Save Changes' : 'Create Category') }}
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

.table thead { background: #f5f5f7; }

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

.table td {
  padding: 14px 16px;
  border-bottom: 1px solid #f0f0f0;
  vertical-align: middle;
}

.table tr:last-child td { border-bottom: none; }

.td-name {
  font-size: 0.9375rem;
  font-weight: 500;
  color: #1d1d1f;
}

.td-meta { font-size: 0.875rem; color: #6e6e73; }

.slug {
  font-size: 0.8125rem;
  background: #f5f5f7;
  padding: 2px 8px;
  border-radius: 6px;
  font-family: monospace;
  color: #1d1d1f;
}

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
  max-width: 480px;
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
.field textarea {
  padding: 9px 12px;
  border: 1px solid #d2d2d7;
  border-radius: 10px;
  font-size: 0.9375rem;
  font-family: inherit;
  color: #1d1d1f;
  outline: none;
  resize: vertical;
}

.field input:focus,
.field textarea:focus { border-color: #0071e3; }

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
