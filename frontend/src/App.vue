<script setup lang="ts">
import { computed, onMounted } from 'vue'
import { useRoute } from 'vue-router'
import AppHeader from '@/components/AppHeader.vue'
import AppFooter from '@/components/AppFooter.vue'
import { useAuthStore } from '@/stores/auth'
import { useCartStore } from '@/stores/cart'
import { useCurrencyStore } from '@/stores/currency'

const authStore = useAuthStore()
const cartStore = useCartStore()
const currencyStore = useCurrencyStore()
const route = useRoute()

const isAdmin = computed(() => route.path.startsWith('/admin'))

onMounted(async () => {
  await Promise.all([
    authStore.fetchUser(),
    cartStore.fetchCart(),
    currencyStore.fetchCurrencies(),
  ])
})
</script>

<template>
  <div class="app">
    <template v-if="!isAdmin">
      <AppHeader />
      <main>
        <RouterView />
      </main>
      <AppFooter />
    </template>
    <RouterView v-else />
  </div>
</template>

<style>
.app {
  min-height: 100vh;
  display: flex;
  flex-direction: column;
  background-color: var(--apple-light);
}

main {
  padding-top: 44px;
  flex: 1;
}
</style>
