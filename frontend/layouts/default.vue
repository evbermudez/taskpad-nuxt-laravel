<template>
  <div class="min-h-dvh transition-colors duration-300 bg-white text-gray-900 dark:bg-gray-900 dark:text-gray-100">
    <header class="flex items-center justify-between p-4 shadow bg-white dark:bg-gray-800">
      <NuxtLink to="/" class="font-bold text-lg">TaskPad</NuxtLink>

      <ClientOnly>
        <nav class="flex items-center gap-4">
          <template v-if="auth.user">
            <span>👋 {{ auth.user.name }}</span>
            <button
              @click="handleLogout"
              class="rounded bg-red-600 text-white px-3 py-1"
            >
              Logout
            </button>
          </template>
          <template v-else>
            <NuxtLink to="/login" class="text-blue-500 hover:underline">Login</NuxtLink>
          </template>

          <button
            class="rounded border px-2 py-1 text-sm"
            @click="toggleDark()"
            title="Toggle dark mode"
          >
            {{ isDark ? '🌙' : '☀️' }}
          </button>
        </nav>

        <template #fallback>
          <div class="h-6 w-40 rounded bg-gray-200/60 dark:bg-gray-700/50"></div>
        </template>
      </ClientOnly>
    </header>

    <main class="p-6">
      <slot />
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
  disableTransition: false,
})
const toggleDark = useToggle(isDark)

async function handleLogout() {
  await auth.logout()
  await navigateTo('/login')
}
</script>