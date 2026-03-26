import { ref, computed } from 'vue'
import { defineStore } from 'pinia'
import { cartService } from '@/api/services/cart.service'
import type { CartItem } from '@/api/types'

export type { CartItem }

export const useCartStore = defineStore('cart', () => {
  const items = ref<CartItem[]>([])
  const loading = ref(false)

  const itemCount = computed(() => items.value.reduce((sum, item) => sum + item.quantity, 0))
  const subtotal = computed(() =>
    items.value.reduce((sum, item) => sum + item.product.price * item.quantity, 0),
  )

  async function fetchCart() {
    loading.value = true
    try {
      items.value = await cartService.getCart()
    } finally {
      loading.value = false
    }
  }

  async function addItem(productId: number, quantity = 1) {
    const updated = await cartService.addItem({ product_id: productId, quantity })
    const idx = items.value.findIndex((i) => i.product_id === productId)
    if (idx >= 0) {
      items.value[idx] = updated
    } else {
      items.value.push(updated)
    }
  }

  async function updateItem(cartItemId: number, quantity: number) {
    const updated = await cartService.updateItem(cartItemId, { quantity })
    const idx = items.value.findIndex((i) => i.id === cartItemId)
    if (idx >= 0) items.value[idx] = updated
  }

  async function removeItem(cartItemId: number) {
    await cartService.removeItem(cartItemId)
    items.value = items.value.filter((i) => i.id !== cartItemId)
  }

  async function clearCart() {
    await cartService.clearCart()
    items.value = []
  }

  return { items, loading, itemCount, subtotal, fetchCart, addItem, updateItem, removeItem, clearCart }
})
