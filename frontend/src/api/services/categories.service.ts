import apiClient from '@/api/client'
import type { Category } from '@/api/types'

export const categoriesService = {
  getAll(): Promise<Category[]> {
    return apiClient.get<Category[]>('/categories').then((r) => r.data)
  },
}
