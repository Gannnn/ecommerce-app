import apiClient from '@/api/client'
import type { AuthResponse, LoginPayload, RegisterPayload, User } from '@/api/types'

export const authService = {
  register(payload: RegisterPayload): Promise<AuthResponse> {
    return apiClient.post<AuthResponse>('/auth/register', payload).then((r) => r.data)
  },

  login(payload: LoginPayload): Promise<AuthResponse> {
    return apiClient.post<AuthResponse>('/auth/login', payload).then((r) => r.data)
  },

  logout(): Promise<void> {
    return apiClient.post('/auth/logout').then(() => undefined)
  },

  getUser(): Promise<User> {
    return apiClient.get<User>('/auth/user').then((r) => r.data)
  },
}
