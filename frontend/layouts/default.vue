<template>
  <div class="min-h-dvh bg-gray-100 dark:bg-gray-950 text-gray-900 dark:text-gray-100">
    <!-- Header -->
    <header class="sticky top-0 z-20 bg-white/90 dark:bg-gray-900/90 backdrop-blur border-b border-black/10">
      <div class="mx-auto max-w-6xl px-4 h-14 flex items-center gap-3">
        <NuxtLink to="/" class="font-semibold">TaskPad</NuxtLink>

        <!-- Top Search -->
        <div class="min-w-[220px] max-w-lg flex-1">
          <UInput
            v-model="q"
            placeholder="Search tasks…"
            class="w-full border border-black/10 dark:border-gray-700 p-2"
            @input="broadcastSearch"
            variant="outline"
            id="global-search"
          />
        </div>

        <!-- Theme + user name -->
         <ClientOnly>
          <UButton
            variant="ghost"
            :icon="isDark ? 'i-heroicons-sun-20-solid' : 'i-heroicons-moon-20-solid'"
            @click="toggleDark()"
          />
        </ClientOnly>
        <div class="text-sm" v-if="auth.user">
        {{ auth.user?.name }}
        </div>
      </div>
    </header>

    <!-- Body: sidebar + content -->
    <div class="mx-auto max-w-6xl grid grid-cols-[260px_1fr] gap-0">
      <aside class="border-r border-black/10 bg-white/70 dark:bg-gray-900/70">
        <slot name="sidebar" />
      </aside>

      <main class="bg-white dark:bg-gray-900">
        <slot />
      </main>
    </div>
  </div>
</template>

<script setup lang="ts">
import { useAuth } from '@/stores/auth'
const auth = useAuth()

import { useDark, useToggle, useEventBus } from '@vueuse/core'

const isDark = useDark()
const toggleDark = useToggle(isDark)

const q = ref('')
const bus = useEventBus<string>('global-search')
function broadcastSearch() {
  bus.emit(q.value)
}
</script>