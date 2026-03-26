import { ref } from 'vue'
import { defineStore } from 'pinia'
import { ordersService } from '@/api/services/orders.service'
import type { Order, OrderStatus } from '@/api/types'

export type { Order, OrderStatus }

export const useOrderStore = defineStore('orders', () => {
  const orders = ref<Order[]>([])
  const loading = ref(false)

  async function fetchOrders() {
    loading.value = true
    try {
      orders.value = await ordersService.getAll()
    } finally {
      loading.value = false
    }
  }

  async function placeOrder(notes?: string): Promise<Order> {
    const order = await ordersService.placeOrder({ notes })
    orders.value.unshift(order)
    return order
  }

  async function updateStatus(orderId: number, status: OrderStatus) {
    const updated = await ordersService.updateStatus(orderId, { status })
    const idx = orders.value.findIndex((o) => o.id === orderId)
    if (idx >= 0) orders.value[idx] = updated
  }

  return { orders, loading, fetchOrders, placeOrder, updateStatus }
})
