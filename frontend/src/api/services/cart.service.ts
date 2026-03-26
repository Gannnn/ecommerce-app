import apiClient from '@/api/client'
import type { CartItem, AddToCartPayload, UpdateCartItemPayload } from '@/api/types'

export const cartService = {
  getCart(): Promise<CartItem[]> {
    return apiClient.get<CartItem[]>('/cart').then((r) => r.data)
  },

  addItem(payload: AddToCartPayload): Promise<CartItem> {
    return apiClient.post<CartItem>('/cart', payload).then((r) => r.data)
  },

  updateItem(id: number, payload: UpdateCartItemPayload): Promise<CartItem> {
    return apiClient.put<CartItem>(`/cart/${id}`, payload).then((r) => r.data)
  },

  removeItem(id: number): Promise<void> {
    return apiClient.delete(`/cart/${id}`).then(() => undefined)
  },

  clearCart(): Promise<void> {
    return apiClient.delete('/cart').then(() => undefined)
  },
}
