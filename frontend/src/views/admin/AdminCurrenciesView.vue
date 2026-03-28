<script setup lang="ts">
import { ref, onMounted } from 'vue'
import { adminCurrenciesService } from '@/api/services/currencies.service'
import { ApiError } from '@/api/client'
import AppSpinner from '@/components/AppSpinner.vue'
import type { Currency } from '@/api/types'

const currencies = ref<Currency[]>([])
const loading = ref(false)
const syncing = ref(false)
const error = ref('')
const syncMsg = ref('')

onMounted(fetchAll)

async function fetchAll() {
  loading.value = true
  try {
    currencies.value = await adminCurrenciesService.getAll()
  } finally {
    loading.value = false
  }
}

async function toggleActive(c: Currency) {
  error.value = ''
  try {
    const updated = await adminCurrenciesService.update(c.id, { is_active: !c.is_active })
    const idx = currencies.value.findIndex((x) => x.id === c.id)
    if (idx >= 0) currencies.value[idx] = updated
  } catch (e) {
    error.value = e instanceof ApiError ? e.message : 'Update failed.'
  }
}

async function syncRates() {
  syncing.value = true
  syncMsg.value = ''
  error.value = ''
  try {
    const res = await adminCurrenciesService.sync()
    currencies.value = res.currencies
    syncMsg.value = res.message
  } catch (e) {
    error.value = e instanceof ApiError ? e.message : 'Sync failed.'
  } finally {
    syncing.value = false
  }
}

function formatRate(c: Currency): string {
  if (!c.middle_rate) return '—'
  return `${c.unit} ${c.code} = RM ${c.middle_rate.toFixed(4)}`
}

function formatDate(str: string | null): string {
  if (!str) return '—'
  return new Date(str).toLocaleString('en-MY', {
    timeZone: 'Asia/Kuala_Lumpur',
    year: 'numeric',
    month: 'short',
    day: 'numeric',
    hour: '2-digit',
    minute: '2-digit',
    hour12: false,
  })
}
</script>

<template>
  <div class="page">
    <div class="page-header">
      <div>
        <h1 class="page-title">Currencies</h1>
        <p class="page-sub">{{ currencies.length }} total · Enable currencies to show in the storefront</p>
      </div>
      <button class="sync-btn" :disabled="syncing" @click="syncRates">
        <svg v-if="!syncing" width="13" height="13" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
          <path d="M1 4v6h6M23 20v-6h-6" />
          <path d="M20.49 9A9 9 0 005.64 5.64L1 10m22 4l-4.64 4.36A9 9 0 013.51 15" />
        </svg>
        <span v-else class="btn-spinner" />
        {{ syncing ? 'Syncing…' : 'Sync from BNM' }}
      </button>
    </div>

    <p v-if="syncMsg" class="feedback-success">{{ syncMsg }}</p>
    <p v-if="error" class="feedback-error">{{ error }}</p>

    <div v-if="loading" class="loading">
      <AppSpinner size="28px" />
    </div>

    <div v-else class="table-card">
      <table class="table">
        <thead>
          <tr>
            <th>Code</th>
            <th>Name</th>
            <th>Rate (vs MYR)</th>
            <th>Last Updated</th>
            <th>Visible</th>
          </tr>
        </thead>
        <tbody>
          <tr v-for="c in currencies" :key="c.id" class="table-row">
            <td class="td-code">
              {{ c.code }}
              <span v-if="c.is_default" class="base-badge">base</span>
            </td>
            <td class="td-name">{{ c.name }}</td>
            <td class="td-meta">{{ formatRate(c) }}</td>
            <td class="td-meta">{{ formatDate(c.updated_at) }}</td>
            <td>
              <button
                class="toggle"
                :class="{ 'toggle-on': c.is_active }"
                :disabled="c.is_default"
                :title="c.is_default ? 'Base currency — always visible' : (c.is_active ? 'Disable' : 'Enable')"
                @click="toggleActive(c)"
              >
                <span class="toggle-knob" />
              </button>
            </td>
          </tr>
        </tbody>
      </table>
    </div>
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

.sync-btn {
  display: flex;
  align-items: center;
  gap: 7px;
  padding: 8px 16px;
  background: #0071e3;
  color: white;
  border: none;
  border-radius: 980px;
  font-size: 0.875rem;
  font-family: inherit;
  cursor: pointer;
  white-space: nowrap;
  flex-shrink: 0;
  transition: background 0.2s;
}
.sync-btn:hover:not(:disabled) { background: #0077ed; }
.sync-btn:disabled { opacity: 0.5; cursor: not-allowed; }

.btn-spinner {
  display: inline-block;
  width: 12px;
  height: 12px;
  border: 1.5px solid rgba(255, 255, 255, 0.4);
  border-top-color: white;
  border-radius: 50%;
  animation: spin 0.6s linear infinite;
}
@keyframes spin { to { transform: rotate(360deg); } }

.feedback-success {
  font-size: 0.875rem;
  color: #28cd41;
  margin: -20px 0 20px;
}
.feedback-error {
  font-size: 0.875rem;
  color: #ff3b30;
  margin: -20px 0 20px;
}

.loading {
  display: flex;
  justify-content: center;
  padding: 80px 0;
}

/* Table */
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

.td-code {
  display: flex;
  align-items: center;
  gap: 8px;
  font-size: 0.9375rem;
  font-weight: 600;
  color: #1d1d1f;
  font-variant-numeric: tabular-nums;
}

.base-badge {
  font-size: 0.6rem;
  font-weight: 700;
  text-transform: uppercase;
  letter-spacing: 0.06em;
  color: #0071e3;
  background: rgba(0, 113, 227, 0.1);
  border-radius: 4px;
  padding: 2px 5px;
}

.td-name {
  font-size: 0.9375rem;
  color: #1d1d1f;
}

.td-meta {
  font-size: 0.875rem;
  color: #6e6e73;
}

/* Toggle */
.toggle {
  width: 40px;
  height: 24px;
  border-radius: 12px;
  background: #d2d2d7;
  border: none;
  cursor: pointer;
  position: relative;
  transition: background 0.2s;
  padding: 0;
  flex-shrink: 0;
}
.toggle:disabled { cursor: not-allowed; opacity: 0.5; }
.toggle-on { background: #28cd41; }

.toggle-knob {
  position: absolute;
  top: 2px;
  left: 2px;
  width: 20px;
  height: 20px;
  border-radius: 50%;
  background: white;
  box-shadow: 0 1px 3px rgba(0, 0, 0, 0.2);
  transition: transform 0.2s;
  display: block;
}
.toggle-on .toggle-knob { transform: translateX(16px); }
</style>
