<script setup lang="ts">
import { onMounted } from 'vue'
import { useOrderStore } from '@/stores/orders'
import type { OrderStatus } from '@/api/types'
import OrderStatusBadge from '@/components/OrderStatusBadge.vue'
import AppSpinner from '@/components/AppSpinner.vue'

const orderStore = useOrderStore()

onMounted(() => orderStore.fetchOrders())

function formatPrice(price: number): string {
  return new Intl.NumberFormat('en-US', { style: 'currency', currency: 'USD' }).format(price)
}

function formatDate(dateStr: string): string {
  return new Date(dateStr).toLocaleDateString('en-US', {
    year: 'numeric',
    month: 'short',
    day: 'numeric',
  })
}

const statuses = ['pending', 'processing', 'shipped', 'delivered', 'cancelled']

async function changeStatus(orderId: number, status: string) {
  await orderStore.updateStatus(orderId, status as OrderStatus)
}
</script>

<template>
  <div class="orders-page">
    <div class="page-container">
      <h1 class="section-title">Your Orders</h1>

      <div v-if="orderStore.loading" class="loading">
        <AppSpinner size="28px" />
      </div>

      <div v-else-if="orderStore.orders.length === 0" class="empty-state">
        <p class="empty-title">No orders yet.</p>
        <RouterLink to="/" class="btn-primary">Start Shopping</RouterLink>
      </div>

      <div v-else class="orders-list">
        <div v-for="order in orderStore.orders" :key="order.id" class="order-card">
          <div class="order-header">
            <div>
              <p class="order-num">Order #{{ order.order_number }}</p>
              <p class="order-date">{{ formatDate(order.created_at) }}</p>
            </div>
            <div class="order-header-right">
              <OrderStatusBadge :status="order.status" />
              <select
                class="status-select"
                :value="order.status"
                @change="changeStatus(order.id, ($event.target as HTMLSelectElement).value)"
              >
                <option v-for="s in statuses" :key="s" :value="s">
                  {{ s.charAt(0).toUpperCase() + s.slice(1) }}
                </option>
              </select>
            </div>
          </div>

          <div class="order-items">
            <div v-for="item in order.items" :key="item.id" class="order-item">
              <span class="item-name">{{ item.product_name }}</span>
              <span class="item-qty">× {{ item.quantity }}</span>
              <span class="item-price">{{ formatPrice(item.subtotal) }}</span>
            </div>
          </div>

          <div class="order-footer">
            <span class="order-total-label">Total</span>
            <span class="order-total">{{ formatPrice(order.total) }}</span>
          </div>
        </div>
      </div>
    </div>
  </div>
</template>

<style scoped>
.orders-page {
  padding: 48px 0 80px;
}

.section-title {
  margin-bottom: 40px;
}

.loading {
  display: flex;
  justify-content: center;
  padding: 80px 0;
}

.empty-state {
  text-align: center;
  padding: 80px 0;
}

.empty-title {
  font-size: 1.25rem;
  color: var(--apple-mid);
  margin-bottom: 24px;
}

.orders-list {
  display: flex;
  flex-direction: column;
  gap: 24px;
}

.order-card {
  background: var(--apple-white);
  border: 1px solid var(--apple-border);
  border-radius: 18px;
  overflow: hidden;
}

.order-header {
  display: flex;
  justify-content: space-between;
  align-items: center;
  padding: 20px 24px;
  background: var(--apple-light);
  border-bottom: 1px solid var(--apple-border);
}

.order-num {
  font-size: 0.9375rem;
  font-weight: 600;
  color: var(--apple-dark);
  margin: 0 0 4px;
}

.order-date {
  font-size: 0.8125rem;
  color: var(--apple-mid);
  margin: 0;
}

.order-header-right {
  display: flex;
  align-items: center;
  gap: 12px;
}

.status-select {
  background: var(--apple-white);
  border: 1px solid var(--apple-border);
  border-radius: 8px;
  padding: 6px 12px;
  font-size: 0.8125rem;
  color: var(--apple-dark);
  cursor: pointer;
  width: auto;
}

.order-items {
  padding: 16px 24px;
}

.order-item {
  display: flex;
  align-items: center;
  gap: 12px;
  padding: 8px 0;
  border-bottom: 1px solid var(--apple-light);
  font-size: 0.9375rem;
}

.order-item:last-child {
  border-bottom: none;
}

.item-name {
  flex: 1;
  color: var(--apple-dark);
}

.item-qty {
  color: var(--apple-mid);
  font-size: 0.875rem;
  min-width: 40px;
}

.item-price {
  font-weight: 500;
  color: var(--apple-dark);
  min-width: 80px;
  text-align: right;
}

.order-footer {
  display: flex;
  justify-content: space-between;
  align-items: center;
  padding: 16px 24px;
  background: var(--apple-light);
  border-top: 1px solid var(--apple-border);
}

.order-total-label {
  font-size: 0.9375rem;
  font-weight: 600;
  color: var(--apple-dark);
}

.order-total {
  font-size: 1.125rem;
  font-weight: 700;
  color: var(--apple-dark);
}
</style>
