<template>
  <div class="min-h-dvh bg-gray-100 dark:bg-gray-950 text-gray-900 dark:text-gray-100">
    <!-- Header -->
    <header class="sticky top-0 z-20 bg-white/90 dark:bg-gray-900/90 backdrop-blur border-b border-black/10">
      <div class="mx-auto max-w-6xl px-4 h-14 flex items-center gap-3">
        <!-- Mobile sidebar toggle -->
        <DialogRoot v-model:open="sidebarOpen">
          <DialogTrigger as-child>
            <UButton
              icon="i-heroicons-bars-3"
              variant="ghost"
              class="md:hidden"
              aria-label="Toggle sidebar"
            />
          </DialogTrigger>

          <DialogPortal>
            <DialogOverlay class="fixed inset-0 bg-black/50 backdrop-blur-sm" />
            <DialogContent
              class="fixed inset-y-0 left-0 w-64 bg-white dark:bg-gray-900 shadow-xl p-4 overflow-y-auto"
              @click.stop
            >
              <DialogTitle class="sr-only">Sidebar</DialogTitle>
              <slot name="sidebar" />
              <DialogClose as-child>
                <UButton
                  icon="i-heroicons-x-mark"
                  variant="ghost"
                  class="absolute top-2 right-2"
                  aria-label="Close sidebar"
                />
              </DialogClose>
            </DialogContent>
          </DialogPortal>
        </DialogRoot>

        <!-- Brand -->
        <NuxtLink to="/" class="font-semibold hidden md:block">TaskPad</NuxtLink>

        <!-- Top Search -->
        <div class="flex-1 min-w-[220px] max-w-lg">
          <UInput
            v-model="q"
            placeholder="Search tasks…"
            variant="none"
            id="global-search"
            class="w-full rounded-md border border-black/10 dark:border-gray-700 px-3 py-2 text-sm
                  bg-white/90 dark:bg-gray-800/90 placeholder:text-gray-400 dark:placeholder:text-gray-500
                  focus-within:ring-2 focus-within:ring-primary/40 focus-within:ring-offset-0
                  [--ui-ring-color:transparent] [--ui-ring-inset:0] transition-colors duration-150"
            @update:model-value="val => bus.emit((val ?? '').trim())"
          />
        </div>

        <!-- Theme toggle -->
        <ClientOnly>
          <UButton
            variant="ghost"
            :icon="isDark ? 'i-heroicons-sun-20-solid' : 'i-heroicons-moon-20-solid'"
            @click="toggleDark()"
            class="shrink-0"
          />
        </ClientOnly>

        <!-- User info -->
        <ClientOnly>
          <template v-if="auth.user">
            <span class="hidden sm:inline text-sm opacity-80">👋 {{ auth.user.name }}</span>
            <UButton
              color="neutral"
              variant="ghost"
              icon="i-heroicons-arrow-right-on-rectangle"
              @click="handleLogout()"
              class="hidden sm:flex"
            >
              Logout
            </UButton>
          </template>
          <template v-else>
            <NuxtLink to="/login" class="text-blue-500 hover:underline text-sm">Login</NuxtLink>
          </template>
        </ClientOnly>
      </div>
    </header>

    <!-- Body -->
    <div class="mx-auto max-w-6xl grid md:grid-cols-[260px_1fr] gap-0">
      <aside class="hidden md:block border-r border-black/10 bg-white/70 dark:bg-gray-900/70">
        <slot name="sidebar" />
      </aside>

      <main class="bg-white dark:bg-gray-900">
        <slot />
      </main>
    </div>
  </div>
</template>

<script setup lang="ts">
import {
  DialogRoot,
  DialogTrigger,
  DialogPortal,
  DialogOverlay,
  DialogContent,
  DialogClose,
  DialogTitle
} from 'reka-ui'
import { useAuth } from '@/stores/auth'
import { useDark, useToggle, useEventBus } from '@vueuse/core'

const auth = useAuth()
const isDark = useDark()
const toggleDark = useToggle(isDark)
const q = ref('')
const sidebarOpen = ref(false)
const bus = useEventBus<string>('global-search')

async function handleLogout() {
  await auth.logout()
  await navigateTo('/login')
}
</script>