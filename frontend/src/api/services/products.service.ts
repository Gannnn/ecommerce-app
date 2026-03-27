import apiClient from '@/api/client'
import type { Product, GetProductsParams } from '@/api/types'

export const productsService = {
  getAll(params?: GetProductsParams): Promise<Product[]> {
    return apiClient.get<Product[]>('/products', { params }).then((r) => r.data)
  },

  getBySlug(slug: string): Promise<Product> {
    return apiClient.get<Product>(`/products/${slug}`).then((r) => r.data)
  },
}
