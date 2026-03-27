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

function formatPrice(price: number): string {
  return new Intl.NumberFormat('en-US', { style: 'currency', currency: 'USD' }).format(price)
}

function formatDate(dateStr: string): string {
  return new Date(dateStr).toLocaleDateString('en-US', { year: 'numeric', month: 'short', day: 'numeric' })
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
            <th>Date</th>
            <th>Items</th>
            <th>Total</th>
            <th>Status</th>
            <th>Update Status</th>
          </tr>
        </thead>
        <tbody>
          <tr v-for="order in orders" :key="order.id" class="table-row">
            <td class="td-name">#{{ order.id }}</td>
            <td class="td-meta">{{ formatDate(order.created_at) }}</td>
            <td class="td-meta">{{ order.items.length }}</td>
            <td class="td-meta">{{ formatPrice(order.total) }}</td>
            <td><OrderStatusBadge :status="order.status" /></td>
            <td>
              <select
                class="status-select"
                :value="order.status"
                @change="changeStatus(order.id, ($event.target as HTMLSelectElement).value)"
              >
                <option v-for="s in statuses" :key="s" :value="s">
                  {{ s.charAt(0).toUpperCase() + s.slice(1) }}
                </option>
              </select>
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

.status-select {
  border: 1px solid #d2d2d7;
  border-radius: 8px;
  padding: 6px 10px;
  font-size: 0.8125rem;
  font-family: inherit;
  color: #1d1d1f;
  background: #fff;
  cursor: pointer;
  outline: none;
  transition: border-color 0.15s;
}

.status-select:focus { border-color: #0071e3; }
.status-select:hover { border-color: #aeaeb2; }
</style>
