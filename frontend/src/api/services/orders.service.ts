import apiClient from '@/api/client'
import type { Order, PlaceOrderPayload, UpdateOrderStatusPayload } from '@/api/types'

export const ordersService = {
  getAll(): Promise<Order[]> {
    return apiClient.get<Order[]>('/orders').then((r) => r.data)
  },

  getById(id: number): Promise<Order> {
    return apiClient.get<Order>(`/orders/${id}`).then((r) => r.data)
  },

  placeOrder(payload: PlaceOrderPayload = {}): Promise<Order> {
    return apiClient.post<Order>('/orders', payload).then((r) => r.data)
  },

  updateStatus(id: number, payload: UpdateOrderStatusPayload): Promise<Order> {
    return apiClient.patch<Order>(`/orders/${id}/status`, payload).then((r) => r.data)
  },
}
