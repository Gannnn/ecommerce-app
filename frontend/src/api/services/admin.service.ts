import apiClient from '@/api/client'
import type { Product, Category, AdminProductPayload, AdminCategoryPayload } from '@/api/types'

function productToFormData(payload: Partial<AdminProductPayload>): FormData {
  const fd = new FormData()
  if (payload.category_id !== undefined) fd.append('category_id', String(payload.category_id))
  if (payload.name !== undefined) fd.append('name', payload.name)
  if (payload.description !== undefined) fd.append('description', payload.description)
  if (payload.price !== undefined) fd.append('price', String(payload.price))
  if (payload.stock_quantity !== undefined) fd.append('stock_quantity', String(payload.stock_quantity))
  fd.append('is_featured', payload.is_featured ? '1' : '0')
  if (payload.image) fd.append('image', payload.image)
  return fd
}

export const adminProductsService = {
  getAll(): Promise<Product[]> {
    return apiClient.get<Product[]>('/admin/products').then((r) => r.data)
  },

  create(payload: AdminProductPayload): Promise<Product> {
    return apiClient
      .post<Product>('/admin/products', productToFormData(payload), {
        headers: { 'Content-Type': 'multipart/form-data' },
      })
      .then((r) => r.data)
  },

  // Laravel cannot parse multipart PUT — use POST with _method spoofing
  update(id: number, payload: Partial<AdminProductPayload>): Promise<Product> {
    const fd = productToFormData(payload)
    fd.append('_method', 'PUT')
    return apiClient
      .post<Product>(`/admin/products/${id}`, fd, {
        headers: { 'Content-Type': 'multipart/form-data' },
      })
      .then((r) => r.data)
  },

  destroy(id: number): Promise<void> {
    return apiClient.delete(`/admin/products/${id}`).then(() => undefined)
  },
}

export const adminCategoriesService = {
  getAll(): Promise<Category[]> {
    return apiClient.get<Category[]>('/admin/categories').then((r) => r.data)
  },

  create(payload: AdminCategoryPayload): Promise<Category> {
    return apiClient.post<Category>('/admin/categories', payload).then((r) => r.data)
  },

  update(id: number, payload: Partial<AdminCategoryPayload>): Promise<Category> {
    return apiClient.put<Category>(`/admin/categories/${id}`, payload).then((r) => r.data)
  },

  destroy(id: number): Promise<void> {
    return apiClient.delete(`/admin/categories/${id}`).then(() => undefined)
  },
}
