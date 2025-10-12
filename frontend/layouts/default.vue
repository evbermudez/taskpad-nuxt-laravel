<template>
  <div class="min-h-dvh bg-gray-100 dark:bg-gray-950 text-gray-900 dark:text-gray-100">
    <!-- HEADER -->
    <header class="fixed top-0 inset-x-0 z-40 bg-white/90 dark:bg-gray-900/90 backdrop-blur border-b border-black/10">
      <div class="mx-auto max-w-6xl px-4 h-14 flex items-center justify-between gap-3">
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
        <div class="relative flex-1 min-w-[220px] max-w-lg">
          <UInput
            v-model="q"
            id="global-search"
            placeholder="Search tasks…"
            variant="none"
            class="w-full rounded-md border border-black/10 dark:border-gray-700
                  bg-white/90 dark:bg-gray-800/90
                  text-sm placeholder:text-gray-500 dark:placeholder:text-gray-400
                  pl-8 pr-3 py-2
                  focus:outline-none focus:ring-0 focus-visible:ring-0 ring-0 shadow-none"
            @update:model-value="val => bus.emit((val ?? '').trim())"
          >
            <!-- left icon -->
            <template #leading>
              <Search class="pointer-events-none absolute -left-6 top-1/2 -translate-y-1/2 size-4 text-gray-500 dark:text-gray-400" />
            </template>
          </UInput>
        </div>

        <!-- Right Controls -->
        <div class="flex items-center gap-2 ml-auto">
          <PopoverRoot v-model:open="userMenuOpen">
            <PopoverTrigger as-child>
              <button
                class="relative w-8 h-8 rounded-full overflow-hidden border border-black/10 dark:border-white/20
                      bg-gray-200 dark:bg-gray-800 hover:bg-gray-300 dark:hover:bg-gray-700
                      focus:outline-none focus-visible:ring-2 focus-visible:ring-primary/50 transition"
                aria-label="User menu"
              >
                <img
                  v-if="auth.user"
                  src="https://i.pravatar.cc/100?img=12"
                  alt="User avatar"
                  class="object-cover w-full h-full"
                />
                <div
                  v-else
                  class="flex items-center justify-center w-full h-full text-gray-700 dark:text-gray-300 text-sm font-semibold"
                >
                  ?
                </div>
              </button>
            </PopoverTrigger>

            <PopoverPortal>
              <PopoverContent
                side="bottom"
                align="end"
                class="mt-2 w-60 rounded-xl border border-black/10 dark:border-white/10
                      bg-white/95 dark:bg-gray-900/95 backdrop-blur-sm shadow-lg
                      text-black dark:text-gray-100
                      ring-1 ring-black/5 dark:ring-white/10 p-3
                      transition-all duration-150 ease-out z-50"
                :side-offset="8"
                @pointerdown.stop
                @touchstart.stop
                @pointer-down-outside.prevent
                @focus-outside.prevent
                @interact-outside.prevent
              >
                <!-- Header -->
                <div class="flex items-center gap-3 mb-3">
                  <img
                    src="https://i.pravatar.cc/100?img=12"
                    alt="User avatar"
                    class="w-10 h-10 rounded-full border border-black/10 dark:border-white/20"
                  />
                  <div>
                    <p class="font-medium text-sm text-gray-800 dark:text-gray-100">
                      {{ auth.user?.name || 'Guest' }}
                    </p>
                    <p class="text-xs text-gray-500 dark:text-gray-400">
                      {{ auth.user ? 'TaskPad user' : 'Not signed in' }}
                    </p>
                  </div>
                </div>

                <hr class="border-gray-200 dark:border-gray-700 my-2" />

                <!-- Theme toggle -->
                <UButton
                  variant="ghost"
                  class="w-full justify-start text-sm hover:bg-gray-100 dark:hover:bg-gray-800"
                  @click="toggleDark()"
                >
                  <Sun v-if="isDark" class="size-4 mr-2 text-yellow-400" />
                  <Moon v-else class="size-4 mr-2 text-blue-400" />
                  Toggle theme
                </UButton>

                <!-- Login / Logout -->
                <template v-if="auth.user">
                  <UButton
                    variant="ghost"
                    class="w-full justify-start text-sm text-red-600 dark:text-red-400 hover:bg-red-50 dark:hover:bg-red-900/30"
                    @click="handleLogout()"
                  >
                    <Menu class="size-4 mr-2 rotate-180" /> Logout
                  </UButton>
                </template>
                <template v-else>
                  <NuxtLink
                    to="/login"
                    class="block w-full text-left text-sm text-blue-600 dark:text-blue-400 hover:underline"
                  >
                    Login
                  </NuxtLink>
                </template>
              </PopoverContent>
            </PopoverPortal>
          </PopoverRoot>
        </div>
      </div>
    </header>

    <!-- BODY -->
    <div class="pt-14">
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
  DialogTitle,
  PopoverRoot,
  PopoverTrigger,
  PopoverContent,
  PopoverPortal
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
const userMenuOpen = ref(false)

async function handleLogout() {
  await auth.logout()
  await navigateTo('/login')
}
</script>