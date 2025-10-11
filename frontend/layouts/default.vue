<template>
  <div class="min-h-dvh bg-gray-100 dark:bg-gray-950 text-gray-900 dark:text-gray-100">
    <!-- HEADER -->
    <header class="fixed top-0 inset-x-0 z-40 bg-white/90 dark:bg-gray-900/90 backdrop-blur border-b border-black/10">
      <div class="mx-auto max-w-6xl px-4 h-14 flex items-center gap-3">
        <!-- Mobile sidebar toggle -->
        <DialogRoot v-model:open="sidebarOpen">
          <DialogTrigger as-child>
            <UButton variant="ghost" class="md:hidden" aria-label="Toggle sidebar">
              <Menu class="size-5" />
            </UButton>
          </DialogTrigger>

          <DialogPortal>
            <DialogOverlay class="fixed inset-0 bg-black/50 backdrop-blur-sm" />
            <DialogContent
              class="fixed inset-y-0 left-0 w-64 bg-white dark:bg-gray-900 shadow-xl p-4 overflow-y-auto z-50"
              @click.stop
            >
              <DialogTitle class="sr-only">Sidebar</DialogTitle>
              <slot name="sidebar" />
              <DialogClose as-child>
                <UButton variant="ghost" class="absolute top-2 right-2" aria-label="Close sidebar">
                  <X class="size-5" />
                </UButton>
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
                  bg-white/90 dark:bg-gray-800/90 placeholder:text-gray-400 dark:placeholder:text-gray-500"
            @update:model-value="val => bus.emit((val ?? '').trim())"
          >
            <template #leading>
              <Search class="size-4 text-gray-500 dark:text-gray-400" />
            </template>
          </UInput>
        </div>

        <!-- Theme toggle -->
        <ClientOnly>
          <UButton variant="ghost" @click="toggleDark()" class="shrink-0">
            <Sun v-if="isDark" class="size-5" />
            <Moon v-else class="size-5" />
          </UButton>
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

    <!-- BODY -->
    <div class="pt-14"> <!-- 👈 pushes below fixed header -->
      <div class="mx-auto max-w-6xl grid md:grid-cols-[260px_1fr] gap-0">
        <!-- Sidebar (desktop) -->
        <aside class="hidden md:block border-r border-black/10 bg-white/70 dark:bg-gray-900/70">
          <div class="sticky top-0 h-[calc(100dvh-3.5rem)] overflow-y-auto">
            <slot name="sidebar" />
          </div>
        </aside>

        <!-- Main content -->
        <main class="bg-white dark:bg-gray-900 min-h-[calc(100dvh-3.5rem)]">
          <slot />
        </main>
      </div>
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
import { Menu, X, Sun, Moon, Search } from 'lucide-vue-next'

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