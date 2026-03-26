import { ref } from 'vue'
import { defineStore } from 'pinia'
import { productsService } from '@/api/services/products.service'
import type { Product, GetProductsParams } from '@/api/types'

export type { Product }
export type { Category as ProductCategory } from '@/api/types'

export const useProductStore = defineStore('products', () => {
  const products = ref<Product[]>([])
  const loading = ref(false)

  async function fetchProducts(params?: GetProductsParams) {
    loading.value = true
    try {
      products.value = await productsService.getAll(params)
    } finally {
      loading.value = false
    }
  }

  async function fetchProduct(id: number): Promise<Product> {
    return productsService.getById(id)
  }

  return { products, loading, fetchProducts, fetchProduct }
})
