<script setup lang="ts">
import { computed, onMounted } from 'vue'
import { useRoute } from 'vue-router'
import AppHeader from '@/components/AppHeader.vue'
import AppFooter from '@/components/AppFooter.vue'
import { useAuthStore } from '@/stores/auth'
import { useCartStore } from '@/stores/cart'

const authStore = useAuthStore()
const cartStore = useCartStore()
const route = useRoute()

const isAdmin = computed(() => route.path.startsWith('/admin'))

onMounted(async () => {
  await authStore.fetchUser()
  await cartStore.fetchCart()
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
  background-color: var(--apple-white);
}

main {
  padding-top: 44px;
}
</style>
