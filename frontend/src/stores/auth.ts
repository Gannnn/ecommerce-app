import { ref, computed } from 'vue'
import { defineStore } from 'pinia'
import { authService } from '@/api/services/auth.service'
import type { User } from '@/api/types'

export const useAuthStore = defineStore('auth', () => {
  const user = ref<User | null>(null)
  const token = ref<string | null>(localStorage.getItem('auth_token'))

  const isAuthenticated = computed(() => !!token.value)

  function _persistToken(newToken: string) {
    token.value = newToken
    localStorage.setItem('auth_token', newToken)
  }

  function _clearToken() {
    token.value = null
    localStorage.removeItem('auth_token')
  }

  async function register(name: string, email: string, password: string, passwordConfirmation: string) {
    const data = await authService.register({ name, email, password, password_confirmation: passwordConfirmation })
    user.value = data.user
    _persistToken(data.token)
  }

  async function login(email: string, password: string) {
    const data = await authService.login({ email, password })
    user.value = data.user
    _persistToken(data.token)
  }

  async function logout() {
    await authService.logout()
    user.value = null
    _clearToken()
  }

  async function fetchUser() {
    if (!token.value) return
    try {
      user.value = await authService.getUser()
    } catch {
      // Token invalid — clean up silently
      user.value = null
      _clearToken()
    }
  }

  return { user, token, isAuthenticated, register, login, logout, fetchUser }
})
