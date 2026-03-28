import apiClient from '@/api/client'
import adminClient from '@/api/adminClient'
import type { Currency } from '@/api/types'

export const currenciesService = {
  getActive(): Promise<Currency[]> {
    return apiClient.get<Currency[]>('/currencies').then((r) => r.data)
  },
}

export const adminCurrenciesService = {
  getAll(): Promise<Currency[]> {
    return adminClient.get<Currency[]>('/admin/currencies').then((r) => r.data)
  },

  update(id: number, payload: { name?: string; symbol?: string | null; is_active?: boolean }): Promise<Currency> {
    return adminClient.patch<Currency>(`/admin/currencies/${id}`, payload).then((r) => r.data)
  },

  sync(): Promise<{ message: string; currencies: Currency[] }> {
    return adminClient
      .post<{ message: string; currencies: Currency[] }>('/admin/currencies/sync')
      .then((r) => r.data)
  },
}
