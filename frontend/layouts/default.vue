<template>
  <div class="min-h-dvh bg-gray-100 dark:bg-gray-950 text-gray-900 dark:text-gray-100">
    <!-- Header -->
    <header
      class="sticky top-0 z-30 bg-white/90 dark:bg-gray-900/90 backdrop-blur border-b border-black/10 dark:border-white/10"
    >
      <div class="mx-auto max-w-6xl px-4 h-14 flex items-center gap-3">
        <!-- Mobile: menu button -->
        <UButton
          icon="i-heroicons-bars-3"
          variant="ghost"
          class="md:hidden"
          @click="sidebarOpen = true"
        />

        <!-- Logo -->
        <NuxtLink to="/" class="font-semibold text-lg">TaskPad</NuxtLink>

        <!-- Top Search -->
        <div class="hidden sm:flex min-w-[220px] max-w-lg flex-1">
          <UInput
            v-model="q"
            placeholder="Search tasks…"
            class="w-full border border-black/10 dark:border-gray-700 p-2"
            @input="broadcastSearch"
            variant="outline"
            id="global-search"
          />
        </div>

        <!-- Theme toggle -->
        <ClientOnly>
          <UButton
            variant="ghost"
            :icon="isDark ? 'i-heroicons-sun-20-solid' : 'i-heroicons-moon-20-solid'"
            @click="toggleDark()"
          />
        </ClientOnly>

        <!-- User -->
        <ClientOnly>
          <template v-if="auth.user">
            <span class="hidden sm:inline">👋 {{ auth.user.name }}</span>
            <UButton
              color="neutral"
              variant="ghost"
              icon="i-heroicons-arrow-right-on-rectangle"
              @click="handleLogout"
            >
              <span class="hidden sm:inline">Logout</span>
            </UButton>
          </template>

          <template v-else>
            <NuxtLink to="/login" class="text-blue-500 hover:underline">Login</NuxtLink>
          </template>
        </ClientOnly>
      </div>
    </header>

    <!-- Main Grid -->
    <div class="mx-auto max-w-6xl grid md:grid-cols-[260px_1fr] min-h-[calc(100dvh-3.5rem)]">
      <!-- Desktop Sidebar -->
      <aside
        class="hidden md:block border-r border-black/10 dark:border-white/10 bg-white/70 dark:bg-gray-900/70"
      >
        <div class="sticky top-14 h-[calc(100dvh-3.5rem)] overflow-y-auto p-4">
          <slot name="sidebar" />
        </div>
      </aside>

      <!-- Page Content -->
      <main class="bg-white dark:bg-gray-900 min-w-0">
        <slot />
      </main>
    </div>

    <!-- Mobile Sidebar Drawer -->
    <UModal v-model="sidebarOpen" fullscreen :overlay="true" :ui="{ base: 'p-4' }">
      <template #body>
        <div class="bg-white dark:bg-gray-900 h-full overflow-y-auto">
          <div class="flex justify-between items-center mb-4">
            <h2 class="text-lg font-semibold">Menu</h2>
            <UButton
              icon="i-heroicons-x-mark"
              variant="ghost"
              @click="sidebarOpen = false"
            />
          </div>

          <!-- Sidebar content -->
          <slot name="sidebar" />

          <!-- Mobile search -->
          <div class="mt-6">
            <UInput
              v-model="q"
              placeholder="Search tasks…"
              class="w-full border border-black/10 dark:border-gray-700 p-2"
              @input="broadcastSearch"
              variant="outline"
              id="global-search-mobile"
            />
          </div>

          <!-- Mobile logout -->
          <div v-if="auth.user" class="mt-6 flex justify-end">
            <UButton
              color="neutral"
              variant="outline"
              icon="i-heroicons-arrow-right-on-rectangle"
              @click="handleLogout"
            >
              Logout
            </UButton>
          </div>
        </div>
      </template>
    </UModal>
  </div>
</template>

<script setup lang="ts">
import { useAuth } from '@/stores/auth'
import { useDark, useToggle, useEventBus } from '@vueuse/core'

const auth = useAuth()
const isDark = useDark()
const toggleDark = useToggle(isDark)
const q = ref('')
const sidebarOpen = ref(false)

const bus = useEventBus<string>('global-search')
function broadcastSearch() {
  bus.emit(q.value)
}

async function handleLogout() {
  await auth.logout()
  await navigateTo('/login')
}
</script>