<template>
  <div class="grid lg:grid-cols-[280px,1fr] min-h-[calc(100dvh-3.5rem)]">
    <!-- Sidebar -->
    <aside class="hidden lg:block border-r border-black/10 dark:border-white/10 p-4">
      <SidebarDates :date="date" @select="setDate" />
    </aside>

    <!-- Main -->
    <section class="flex flex-col">
      <!-- Top controls -->
      <div class="p-4 border-b border-black/10 dark:border-white/10 flex flex-wrap items-center gap-3">
        <UInput type="date" v-model="date" class="w-48" />

        <!-- Sort -->
        <SelectRoot v-model="sort">
          <SelectTrigger class="border rounded px-3 py-1.5 text-sm bg-white dark:bg-gray-800">
            <SelectValue placeholder="Sort by" />
          </SelectTrigger>
          <SelectContent class="border rounded-md bg-white dark:bg-gray-800 shadow-md" :position="'popper'" :side-offset="4">
            <SelectViewport>
              <SelectGroup>
                <SelectLabel class="px-2 py-1 text-xs text-gray-500">Sort</SelectLabel>
                <SelectItem value="position" class="px-3 py-1.5 cursor-pointer hover:bg-gray-100 dark:hover:bg-gray-700">
                  Position
                </SelectItem>
                <SelectItem value="priority" class="px-3 py-1.5 cursor-pointer hover:bg-gray-100 dark:hover:bg-gray-700">
                  Priority
                </SelectItem>
                <SelectItem value="created_at" class="px-3 py-1.5 cursor-pointer hover:bg-gray-100 dark:hover:bg-gray-700">
                  Created
                </SelectItem>
              </SelectGroup>
            </SelectViewport>
          </SelectContent>
        </SelectRoot>

        <!-- Direction -->
        <SelectRoot v-model="dir">
          <SelectTrigger class="border rounded px-3 py-1.5 text-sm bg-white dark:bg-gray-800">
            <SelectValue placeholder="Direction" />
          </SelectTrigger>
          <SelectContent class="border rounded-md bg-white dark:bg-gray-800 shadow-md" :position="'popper'" :side-offset="4">
            <SelectViewport>
              <SelectGroup>
                <SelectLabel class="px-2 py-1 text-xs text-gray-500">Order</SelectLabel>
                <SelectItem value="asc" class="px-3 py-1.5 cursor-pointer hover:bg-gray-100 dark:hover:bg-gray-700">
                  Asc
                </SelectItem>
                <SelectItem value="desc" class="px-3 py-1.5 cursor-pointer hover:bg-gray-100 dark:hover:bg-gray-700">
                  Desc
                </SelectItem>
              </SelectGroup>
            </SelectViewport>
          </SelectContent>
        </SelectRoot>

        <div class="ms-auto text-sm opacity-60" v-if="q">
          Showing {{ tasks.items.length }} result(s) for “{{ q }}”
        </div>
      </div>

      <!-- List -->
      <div class="flex-1 p-4">
        <div v-if="tasks.loading">
          <USkeleton class="h-12 mb-2" v-for="n in 4" :key="n" />
        </div>

        <div v-else-if="!tasks.items.length" class="p-8">
          <UAlert
            icon="i-heroicons-inbox-20-solid"
            title="No tasks"
            description="Add your first task below."
          />
        </div>

        <div v-else class="space-y-2">
          <TaskItem v-for="t in tasks.items" :key="t.id" :task="t" />
        </div>
      </div>

      <!-- Composer -->
      <div class="p-4 border-t border-black/10 dark:border-white/10 bg-white/80 dark:bg-gray-900/80 backdrop-blur sticky bottom-0">
        <TaskComposer :date="date" />
      </div>
    </section>
  </div>
</template>

<script setup lang="ts">
import {
  SelectRoot,
  SelectTrigger,
  SelectValue,
  SelectContent,
  SelectViewport,
  SelectGroup,
  SelectItem,
  SelectLabel,
} from 'reka-ui'
import { useEventBus, watchDebounced } from '@vueuse/core'
import { useTasks } from '@/stores/tasks'
import TaskComposer from '@/components/TaskComposer.vue'
import TaskItem from '@/components/TaskItem.vue'
import SidebarDates from '@/components/SidebarDates.vue'

definePageMeta({ layout: 'default' })

const tasks = useTasks()

// filters
const today = new Date().toISOString().slice(0, 10)
const date = ref(today)
const q = ref('')

const sort = ref<'position' | 'priority' | 'created_at'>('position')
const dir  = ref<'asc' | 'desc'>('asc')

const sortOpts = [
  { label: 'Position', value: 'position' },
  { label: 'Priority', value: 'priority' },
  { label: 'Created',  value: 'created_at' },
]
const dirOpts = [
  { label: 'Asc', value: 'asc' },
  { label: 'Desc', value: 'desc' },
]

async function load() {
  if (q.value.trim()) {
    await tasks.search(q.value.trim())
  } else {
    await tasks.fetchByDate(date.value, { sort: sort.value, dir: dir.value })
  }
}

function setDate(d: string) {
  date.value = d
  q.value = ''
}


watch([date, sort, dir], load, { immediate: true })

// global search bus (from header)
const bus = useEventBus<string>('global-search')
bus.on((value) => { q.value = value ?? '' })

watchDebounced(q, load, { debounce: 250, maxWait: 600 })
</script>