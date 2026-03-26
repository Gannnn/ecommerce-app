<script setup lang="ts">
import { onMounted } from 'vue'
import { useOrderStore } from '@/stores/orders'
import type { OrderStatus } from '@/api/types'
import OrderStatusBadge from '@/components/OrderStatusBadge.vue'

const orderStore = useOrderStore()

onMounted(() => orderStore.fetchOrders())

function formatPrice(price: number): string {
  return new Intl.NumberFormat('en-US', { style: 'currency', currency: 'USD' }).format(price)
}

function formatDate(dateStr: string): string {
  return new Date(dateStr).toLocaleDateString('en-US', { year: 'numeric', month: 'short', day: 'numeric' })
}

const statuses: OrderStatus[] = ['pending', 'processing', 'shipped', 'delivered', 'cancelled']

async function changeStatus(orderId: number, status: string) {
  await orderStore.updateStatus(orderId, status as OrderStatus)
}
</script>

<template>
  <div class="page">
    <div class="page-header">
      <h1 class="page-title">Orders</h1>
    </div>

    <div v-if="orderStore.loading" class="loading">
      <div class="spinner" />
    </div>

    <div v-else-if="orderStore.orders.length === 0" class="empty">
      No orders yet.
    </div>

    <div v-else class="card">
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
          <tr v-for="order in orderStore.orders" :key="order.id">
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

.empty {
  text-align: center;
  padding: 80px 0;
  color: #6e6e73;
  font-size: 0.9375rem;
}

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

.status-select {
  border: 1px solid #d2d2d7;
  border-radius: 8px;
  padding: 5px 10px;
  font-size: 0.8125rem;
  font-family: inherit;
  color: #1d1d1f;
  background: #fff;
  cursor: pointer;
  outline: none;
}

.status-select:focus { border-color: #0071e3; }
</style>
