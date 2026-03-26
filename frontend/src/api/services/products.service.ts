import apiClient from '@/api/client'
import type { Product, GetProductsParams } from '@/api/types'

export const productsService = {
  getAll(params?: GetProductsParams): Promise<Product[]> {
    return apiClient.get<Product[]>('/products', { params }).then((r) => r.data)
  },

  getById(id: number): Promise<Product> {
    return apiClient.get<Product>(`/products/${id}`).then((r) => r.data)
  },
}
