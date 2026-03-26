/**
 * API Client
 *
 * Single axios instance shared across all services.
 * Handles auth tokens, session IDs, and global error responses in one place.
 * No other file should import axios directly — import `apiClient` from here.
 */

import axios, { type AxiosError } from 'axios'

// ─── Session ID ──────────────────────────────────────────────────────────────
// Guest carts are tied to a stable UUID stored in localStorage.
// This is created once and persists across page loads.

const SESSION_KEY = 'cart_session_id'

export function getSessionId(): string {
  let id = localStorage.getItem(SESSION_KEY)
  if (!id) {
    id = crypto.randomUUID()
    localStorage.setItem(SESSION_KEY, id)
  }
  return id
}

// ─── Client ──────────────────────────────────────────────────────────────────

const apiClient = axios.create({
  baseURL: import.meta.env.VITE_API_URL ?? 'http://localhost:8000/api',
  timeout: 15_000,
  headers: {
    'Content-Type': 'application/json',
    Accept: 'application/json',
  },
})

// ─── Request interceptor ─────────────────────────────────────────────────────
// Attach auth token + session ID on every outgoing request.

apiClient.interceptors.request.use((config) => {
  config.headers['X-Session-ID'] = getSessionId()

  const token = localStorage.getItem('auth_token')
  if (token) {
    config.headers['Authorization'] = `Bearer ${token}`
  }

  return config
})

// ─── Response interceptor ────────────────────────────────────────────────────
// Normalise errors so services always throw a plain { message, errors } object.

apiClient.interceptors.response.use(
  (response) => response,
  (error: AxiosError<{ message?: string; errors?: Record<string, string[]> }>) => {
    // Token expired or revoked — clear credentials and let the router guard redirect.
    if (error.response?.status === 401) {
      localStorage.removeItem('auth_token')
    }

    // Surface the first Laravel validation message or a generic fallback.
    const serverErrors = error.response?.data?.errors
    const message =
      serverErrors
        ? (Object.values(serverErrors).flat()[0] ?? 'Validation failed.')
        : (error.response?.data?.message ?? error.message ?? 'Something went wrong.')

    return Promise.reject(new ApiError(message, error.response?.status, serverErrors))
  },
)

// ─── ApiError ────────────────────────────────────────────────────────────────
// A structured error class so callers can type-narrow instead of inspecting raw axios errors.

export class ApiError extends Error {
  constructor(
    message: string,
    public readonly status?: number,
    public readonly errors?: Record<string, string[]>,
  ) {
    super(message)
    this.name = 'ApiError'
  }
}

export default apiClient
