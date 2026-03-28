/**
 * Admin API Client
 *
 * Separate axios instance for admin requests.
 * Uses admin_auth_token from localStorage instead of the regular auth_token.
 */

import axios, { type AxiosError } from 'axios'
import { ApiError } from '@/api/client'

const adminClient = axios.create({
  baseURL: import.meta.env.VITE_API_URL ?? 'http://localhost:8000/api',
  timeout: 15_000,
  headers: {
    'Content-Type': 'application/json',
    Accept: 'application/json',
  },
})

adminClient.interceptors.request.use((config) => {
  const token = localStorage.getItem('admin_auth_token')
  if (token) {
    config.headers['Authorization'] = `Bearer ${token}`
  }
  return config
})

adminClient.interceptors.response.use(
  (response) => response,
  (error: AxiosError<{ message?: string; errors?: Record<string, string[]> }>) => {
    if (error.response?.status === 401) {
      localStorage.removeItem('admin_auth_token')
    }

    const serverErrors = error.response?.data?.errors
    const message =
      serverErrors
        ? (Object.values(serverErrors).flat()[0] ?? 'Validation failed.')
        : (error.response?.data?.message ?? error.message ?? 'Something went wrong.')

    return Promise.reject(new ApiError(message, error.response?.status, serverErrors))
  },
)

export default adminClient
