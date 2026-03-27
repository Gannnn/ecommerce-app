<script setup lang="ts">
import { ref, onMounted, computed } from 'vue'
import { adminCategoriesService } from '@/api/services/admin.service'
import { ApiError } from '@/api/client'
import AppSpinner from '@/components/AppSpinner.vue'
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
      <div>
        <h1 class="page-title">Categories</h1>
        <p class="page-sub">{{ categories.length }} total</p>
      </div>
      <button class="btn-primary" @click="openCreate">
        <svg width="13" height="13" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5">
          <line x1="12" y1="5" x2="12" y2="19" /><line x1="5" y1="12" x2="19" y2="12" />
        </svg>
        New Category
      </button>
    </div>

    <div v-if="loading" class="loading">
      <AppSpinner size="28px" />
    </div>

    <div v-else-if="categories.length === 0" class="empty-state">
      No categories yet.
    </div>

    <div v-else class="table-card">
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
          <tr v-for="cat in categories" :key="cat.id" class="table-row">
            <td class="td-name">{{ cat.name }}</td>
            <td class="td-meta"><code class="slug">{{ cat.slug }}</code></td>
            <td class="td-meta">{{ cat.sort_order }}</td>
            <td class="td-meta">{{ cat.products_count ?? '—' }}</td>
            <td class="td-actions">
              <button class="icon-btn" title="Edit" @click="openEdit(cat)">
                <svg width="15" height="15" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.75">
                  <path d="M11 4H4a2 2 0 00-2 2v14a2 2 0 002 2h14a2 2 0 002-2v-7" />
                  <path d="M18.5 2.5a2.121 2.121 0 013 3L12 15l-4 1 1-4 9.5-9.5z" />
                </svg>
              </button>
              <button class="icon-btn danger" title="Delete" @click="handleDelete(cat)">
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
              <div class="field-group">
                <div class="field">
                  <label>Name</label>
                  <input v-model="form.name" type="text" placeholder="Category name" required />
                </div>
                <div class="field-divider" />
                <div class="field">
                  <label>Description</label>
                  <textarea v-model="form.description" rows="2" placeholder="Optional description" />
                </div>
                <div class="field-divider" />
                <div class="field">
                  <label>Sort Order</label>
                  <input v-model="form.sort_order" type="number" min="0" placeholder="0" />
                </div>
              </div>

              <p v-if="error" class="error-msg">{{ error }}</p>

              <div class="modal-footer">
                <button type="button" class="btn-ghost" @click="closeModal">Cancel</button>
                <button type="submit" class="btn-primary" :disabled="saving">
                  {{ saving ? 'Saving…' : (editing ? 'Save Changes' : 'Create Category') }}
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

.table thead { background: #f5f5f7; }

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

.table td {
  padding: 14px 16px;
  border-bottom: 1px solid #f0f0f5;
  vertical-align: middle;
}

.table-row:last-child td { border-bottom: none; }
.table-row:hover td { background: #fafafa; }

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
  font-family: 'SF Mono', monospace;
  color: #3a3a3c;
}

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

.icon-btn:hover { background: #f0f0f5; color: #1d1d1f; }
.icon-btn.danger:hover { background: rgba(255,59,48,0.08); color: #ff3b30; }

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
  max-width: 440px;
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

.field-group {
  background: #f5f5f7;
  border-radius: 14px;
  padding: 0 14px;
}

.field-group .field {
  padding: 12px 0;
}

.field-divider {
  height: 1px;
  background: #e5e5ea;
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

.field textarea { resize: vertical; min-height: 48px; }

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
