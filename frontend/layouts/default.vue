<template>
  <div class="min-h-dvh bg-white text-gray-900 transition-colors duration-300 dark:bg-gray-900 dark:text-gray-100">
    <header class="border-b border-gray-200 dark:border-gray-800">
      <UContainer class="flex items-center justify-between py-3">
        <NuxtLink to="/" class="font-bold text-lg">TaskPad</NuxtLink>

        <ClientOnly>
          <div class="flex items-center gap-3">
            <template v-if="auth.user">
              <span class="text-sm opacity-80">👋 {{ auth.user.name }}</span>
              <UButton color="error" @click="handleLogout">Logout</UButton>
            </template>
            <template v-else>
              <UButton to="/login" variant="ghost">Login</UButton>
            </template>

            <UButton
                variant="ghost"
                size="sm"
                :icon="isDark ? 'i-heroicons-sun-20-solid' : 'i-heroicons-moon-20-solid'"
                :aria-label="isDark ? 'Switch to light mode' : 'Switch to dark mode'"
                @click="toggleDark()"
            />
          </div>
          <template #fallback><USkeleton class="h-6 w-40" /></template>
        </ClientOnly>
      </UContainer>
    </header>

    <main>
      <UContainer class="py-6">
        <slot />
      </UContainer>
    </main>
  </div>
</template>

<script setup lang="ts">
import { useAuth } from '@/stores/auth'
import { useDark, useToggle } from '@vueuse/core'

const auth = useAuth()
const isDark = useDark({
  selector: 'html',
  attribute: 'class',
  valueDark: 'dark',
  valueLight: '',
  storageKey: 'theme-preference',
  initialValue: 'light',
})
const toggleDark = useToggle(isDark)

async function handleLogout() {
  await auth.logout()
  await navigateTo('/login')
}
</script>