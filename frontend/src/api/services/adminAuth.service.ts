import adminClient from '@/api/adminClient'
import type { Admin, AdminAuthResponse } from '@/api/types'

export const adminAuthService = {
  login(email: string, password: string): Promise<AdminAuthResponse> {
    return adminClient.post<AdminAuthResponse>('/admin/auth/login', { email, password }).then((r) => r.data)
  },

  logout(): Promise<void> {
    return adminClient.post('/admin/auth/logout').then(() => undefined)
  },

  getMe(): Promise<Admin> {
    return adminClient.get<Admin>('/admin/auth/me').then((r) => r.data)
  },
}
