<script setup lang="ts">
import { ref, onMounted } from 'vue'
import { adminOrdersService } from '@/api/services/admin.service'
import type { Order, OrderStatus } from '@/api/types'
import OrderStatusBadge from '@/components/OrderStatusBadge.vue'
import AppSpinner from '@/components/AppSpinner.vue'

const orders = ref<Order[]>([])
const loading = ref(false)

onMounted(fetchAll)

async function fetchAll() {
  loading.value = true
  try {
    orders.value = await adminOrdersService.getAll()
  } finally {
    loading.value = false
  }
}

function formatOrderPrice(myrAmount: number, order: Order): string {
  const { currency_code: code, currency_rate: rate, currency_unit: unit } = order
  if (code === 'MYR' || !rate) return `RM ${myrAmount.toFixed(2)}`
  const converted = (myrAmount * (unit ?? 1)) / rate
  try {
    return new Intl.NumberFormat('en-US', {
      style: 'currency',
      currency: code,
      minimumFractionDigits: 2,
      maximumFractionDigits: 2,
    }).format(converted)
  } catch {
    return `${code} ${converted.toFixed(2)}`
  }
}

function formatDate(dateStr: string): string {
  return (
    new Date(dateStr).toLocaleString('en-MY', {
      timeZone: 'Asia/Kuala_Lumpur',
      year: 'numeric',
      month: 'short',
      day: 'numeric',
      hour: '2-digit',
      minute: '2-digit',
      hour12: false,
    }) + ' MYT'
  )
}

function formatRate(order: Order): string | null {
  if (order.currency_code === 'MYR' || !order.currency_rate) return null
  const unit = order.currency_unit ?? 1
  return `${unit} ${order.currency_code} = RM ${order.currency_rate.toFixed(4)}`
}

const statuses: OrderStatus[] = ['pending', 'processing', 'shipped', 'delivered', 'cancelled']

async function changeStatus(orderId: number, status: string) {
  await adminOrdersService.updateStatus(orderId, status as OrderStatus)
  await fetchAll()
}
</script>

<template>
  <div class="page">
    <div class="page-header">
      <div>
        <h1 class="page-title">Orders</h1>
        <p class="page-sub">{{ orders.length }} total</p>
      </div>
    </div>

    <div v-if="loading" class="loading">
      <AppSpinner size="28px" />
    </div>

    <div v-else-if="orders.length === 0" class="empty-state">
      No orders yet.
    </div>

    <div v-else class="table-card">
      <table class="table">
        <thead>
          <tr>
            <th>Order</th>
            <th>Customer</th>
            <th>Date (MYT)</th>
            <th>Items</th>
            <th>Total</th>
            <th>Currency</th>
            <th>Status</th>
            <th>Update Status</th>
          </tr>
        </thead>
        <tbody>
          <tr v-for="order in orders" :key="order.id" class="table-row">
            <td class="td-name">#{{ order.order_number }}</td>
            <td class="td-meta">
              <span>{{ order.user?.name ?? '—' }}</span>
              <span class="td-sub">{{ order.user?.email ?? '' }}</span>
            </td>
            <td class="td-meta td-date">{{ formatDate(order.created_at) }}</td>
            <td class="td-meta">{{ order.items.length }}</td>
            <td class="td-meta">
              <span>{{ formatOrderPrice(order.total, order) }}</span>
              <span v-if="order.currency_code !== 'MYR'" class="td-sub">RM {{ order.total.toFixed(2) }}</span>
            </td>
            <td class="td-meta">
              <span class="currency-badge" :class="{ 'currency-badge-foreign': order.currency_code !== 'MYR' }">
                {{ order.currency_code }}
              </span>
              <span v-if="formatRate(order)" class="td-sub">{{ formatRate(order) }}</span>
            </td>
            <td><OrderStatusBadge :status="order.status" /></td>
            <td>
              <div class="select-wrap">
                <select
                  class="status-select"
                  :value="order.status"
                  @change="changeStatus(order.id, ($event.target as HTMLSelectElement).value)"
                >
                  <option v-for="s in statuses" :key="s" :value="s">
                    {{ s.charAt(0).toUpperCase() + s.slice(1) }}
                  </option>
                </select>
                <svg class="select-chevron" width="10" height="10" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round">
                  <path d="M6 9l6 6 6-6" />
                </svg>
              </div>
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

.td-sub {
  display: block;
  font-size: 0.75rem;
  color: #aeaeb2;
  font-variant-numeric: tabular-nums;
}

.td-date {
  white-space: nowrap;
}

.currency-badge {
  display: inline-block;
  font-size: 0.75rem;
  font-weight: 600;
  letter-spacing: 0.04em;
  color: #6e6e73;
  background: #f5f5f7;
  border-radius: 4px;
  padding: 2px 6px;
}

.currency-badge-foreign {
  color: #0071e3;
  background: rgba(0, 113, 227, 0.08);
}

.select-wrap {
  position: relative;
  display: inline-block;
}

.status-select {
  appearance: none;
  -webkit-appearance: none;
  background: #f5f5f7;
  border: none;
  border-radius: 8px;
  padding: 7px 30px 7px 12px;
  min-width: 130px;
  font-size: 0.8125rem;
  font-family: inherit;
  color: #1d1d1f;
  cursor: pointer;
  outline: none;
  transition: background 0.15s;
}

.status-select:hover { background: #ebebf0; }
.status-select:focus { background: #ebebf0; }

.select-chevron {
  position: absolute;
  right: 10px;
  top: 50%;
  transform: translateY(-50%);
  pointer-events: none;
  color: #6e6e73;
}
</style>
