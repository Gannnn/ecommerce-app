import { ref, computed } from 'vue'
import { defineStore } from 'pinia'
import { adminAuthService } from '@/api/services/adminAuth.service'
import type { Admin } from '@/api/types'

export const useAdminAuthStore = defineStore('adminAuth', () => {
  const admin = ref<Admin | null>(null)
  const token = ref<string | null>(localStorage.getItem('admin_auth_token'))

  const isAuthenticated = computed(() => !!token.value)

  function _persistToken(newToken: string) {
    token.value = newToken
    localStorage.setItem('admin_auth_token', newToken)
  }

  function _clearToken() {
    token.value = null
    localStorage.removeItem('admin_auth_token')
  }

  async function login(email: string, password: string) {
    const data = await adminAuthService.login(email, password)
    admin.value = data.admin
    _persistToken(data.token)
  }

  async function logout() {
    await adminAuthService.logout()
    admin.value = null
    _clearToken()
  }

  async function fetchMe() {
    if (!token.value) return
    try {
      admin.value = await adminAuthService.getMe()
    } catch {
      admin.value = null
      _clearToken()
    }
  }

  return { admin, token, isAuthenticated, login, logout, fetchMe }
})
