<script setup lang="ts">
import { ref } from 'vue'
import { useRouter } from 'vue-router'
import { useAuthStore } from '@/stores/auth'
import { ApiError } from '@/api/client'

const auth = useAuthStore()
const router = useRouter()

const name = ref('')
const email = ref('')
const password = ref('')
const passwordConfirmation = ref('')
const error = ref('')
const loading = ref(false)

async function handleRegister() {
  error.value = ''
  if (password.value !== passwordConfirmation.value) {
    error.value = 'Passwords do not match.'
    return
  }
  loading.value = true
  try {
    await auth.register(name.value, email.value, password.value, passwordConfirmation.value)
    router.push('/')
  } catch (e: unknown) {
    error.value = e instanceof ApiError ? e.message : 'Registration failed.'
  } finally {
    loading.value = false
  }
}
</script>

<template>
  <div class="auth-page">
    <div class="auth-card">
      <div class="auth-logo">
        <svg width="40" height="40" viewBox="0 0 814 1000" fill="var(--apple-dark)">
          <path d="M788.1 340.9c-5.8 4.5-108.2 62.2-108.2 190.5 0 148.4 130.3 200.9 134.2 202.2-.6 3.2-20.7 71.9-68.7 141.9-42.8 61.6-87.5 123.1-155.5 123.1s-85.5-39.5-164-39.5c-76 0-103.7 40.8-165.9 40.8s-105-57.8-155.5-127.4C46 790.7 0 663.4 0 541.8c0-192.5 125.4-294.1 248.7-294.1 66.1 0 121.2 43.4 162.7 43.4 39.5 0 101.1-46 176.3-46 28.5 0 130.9 2.6 198.3 99.2zm-234-181.5c31.1-36.9 53.1-88.1 53.1-139.3 0-7.1-.6-14.3-1.9-20.1-50.6 1.9-110.8 33.7-147.1 75.8-28.5 32.4-55.1 83.6-55.1 135.5 0 7.8 1.3 15.6 1.9 18.1 3.2.6 8.4 1.3 13.6 1.3 45.4 0 102.5-30.4 135.5-71.3z" />
        </svg>
      </div>
      <h1 class="auth-title">Create your account</h1>

      <form class="auth-form" @submit.prevent="handleRegister">
        <div class="field">
          <label>Full name</label>
          <input v-model="name" type="text" autocomplete="name" required />
        </div>
        <div class="field">
          <label>Email address</label>
          <input v-model="email" type="email" autocomplete="email" required />
        </div>
        <div class="field">
          <label>Password</label>
          <input v-model="password" type="password" autocomplete="new-password" required minlength="8" />
        </div>
        <div class="field">
          <label>Confirm password</label>
          <input v-model="passwordConfirmation" type="password" autocomplete="new-password" required />
        </div>
        <p v-if="error" class="error-msg">{{ error }}</p>
        <button type="submit" class="btn-primary submit-btn" :disabled="loading">
          {{ loading ? 'Creating account…' : 'Create Account' }}
        </button>
      </form>

      <p class="auth-switch">
        Already have an account?
        <RouterLink to="/login">Sign in</RouterLink>
      </p>
    </div>
  </div>
</template>

<style scoped>
.auth-page {
  min-height: calc(100vh - 52px);
  display: flex;
  align-items: center;
  justify-content: center;
  background: var(--apple-light);
  padding: 40px 24px;
}

.auth-card {
  background: white;
  border-radius: 20px;
  padding: 48px 40px;
  width: 100%;
  max-width: 420px;
  box-shadow: 0 4px 30px rgba(0, 0, 0, 0.08);
  text-align: center;
}

.auth-logo {
  margin-bottom: 20px;
}

.auth-title {
  font-size: 1.5rem;
  font-weight: 600;
  color: var(--apple-dark);
  margin: 0 0 32px;
}

.auth-form {
  display: flex;
  flex-direction: column;
  gap: 16px;
  text-align: left;
}

.field {
  display: flex;
  flex-direction: column;
  gap: 6px;
}

.field label {
  font-size: 0.875rem;
  font-weight: 500;
  color: var(--apple-dark);
}

.error-msg {
  color: var(--apple-red);
  font-size: 0.875rem;
  margin: 0;
}

.submit-btn {
  width: 100%;
  padding: 14px;
  font-size: 1rem;
  margin-top: 4px;
}

.auth-switch {
  margin-top: 24px;
  font-size: 0.875rem;
  color: var(--apple-mid);
}

.auth-switch a {
  color: var(--apple-blue);
  text-decoration: none;
}

.auth-switch a:hover {
  text-decoration: underline;
}
</style>
