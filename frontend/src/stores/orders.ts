import { ref } from 'vue'
import { defineStore } from 'pinia'
import { ordersService } from '@/api/services/orders.service'
import { useCurrencyStore } from '@/stores/currency'
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
    const currencyStore = useCurrencyStore()
    const cur = currencyStore.selected
    const order = await ordersService.placeOrder({
      notes,
      currency_code: cur?.code ?? 'MYR',
      currency_rate: cur?.middle_rate ?? null,
      currency_unit: cur?.unit ?? 1,
    })
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
