<template>
  <NuxtLayout name="default">
    <template #sidebar>
      <SidebarDates :date="date" @select="setDate" />
    </template>

    <!-- Main column -->
    <section class="flex flex-col min-h-[calc(100dvh-3.5rem)]">
      <!-- Top controls -->
      <div
        class="p-4 border-b border-black/10 dark:border-white/10 flex flex-wrap items-center gap-3
              sticky top-14 bg-white/80 dark:bg-gray-900/80 backdrop-blur z-10"
      >
        <!-- Date picker -->
        <UInput v-model="date" type="date" class="w-48" />

        <!-- Desktop filters -->
        <div class="hidden sm:flex items-center gap-3">
          <!-- Sort -->
          <SelectRoot v-model="sort">
            <SelectTrigger class="border rounded px-3 py-1.5 text-sm bg-white dark:bg-gray-800">
              <SelectValue placeholder="Sort by" />
            </SelectTrigger>
            <SelectContent class="border rounded-md bg-white dark:bg-gray-800 shadow-md" :position="'popper'" :side-offset="4">
              <SelectViewport>
                <SelectGroup>
                  <SelectLabel class="px-2 py-1 text-xs text-gray-500 dark:text-gray-400">Sort</SelectLabel>
                  <SelectItem
                    v-for="opt in sortOpts"
                    :key="opt.value"
                    :value="opt.value"
                    class="px-3 py-1.5 cursor-pointer hover:bg-gray-100 dark:hover:bg-gray-700"
                  >
                    {{ opt.label }}
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
                  <SelectLabel class="px-2 py-1 text-xs text-gray-500 dark:text-gray-400">Order</SelectLabel>
                  <SelectItem
                    v-for="opt in dirOpts"
                    :key="opt.value"
                    :value="opt.value"
                    class="px-3 py-1.5 cursor-pointer hover:bg-gray-100 dark:hover:bg-gray-700"
                  >
                    {{ opt.label }}
                  </SelectItem>
                </SelectGroup>
              </SelectViewport>
            </SelectContent>
          </SelectRoot>
        </div>

        <!-- Mobile “Options” menu -->
        <div class="sm:hidden">
          <SelectRoot>
            <SelectTrigger class="border rounded px-3 py-1.5 text-sm bg-white dark:bg-gray-800 w-32 flex items-center gap-1.5">
              <span class="iconify i-heroicons-funnel size-4 text-gray-500 dark:text-gray-400" />
              <SelectValue placeholder="Options" />
            </SelectTrigger>
            <SelectContent class="border rounded-md bg-white dark:bg-gray-800 shadow-md" :position="'popper'" :side-offset="4">
              <SelectViewport>
                <SelectGroup>
                  <SelectLabel class="px-2 py-1 text-xs text-gray-500 dark:text-gray-400">Sort</SelectLabel>
                  <SelectItem
                    v-for="opt in sortOpts"
                    :key="opt.value"
                    :value="opt.value"
                    @click="sort = opt.value; load()"
                    class="px-3 py-1.5 cursor-pointer hover:bg-gray-100 dark:hover:bg-gray-700"
                  >
                    {{ opt.label }}
                  </SelectItem>
                </SelectGroup>
                <SelectGroup>
                  <SelectLabel class="px-2 py-1 text-xs text-gray-500 dark:text-gray-400 mt-2">Order</SelectLabel>
                  <SelectItem
                    v-for="opt in dirOpts"
                    :key="opt.value"
                    :value="opt.value"
                    @click="dir = opt.value; load()"
                    class="px-3 py-1.5 cursor-pointer hover:bg-gray-100 dark:hover:bg-gray-700"
                  >
                    {{ opt.label }}
                  </SelectItem>
                </SelectGroup>
              </SelectViewport>
            </SelectContent>
          </SelectRoot>
        </div>

        <!-- Result info -->
        <div class="ms-auto text-sm opacity-60 truncate max-w-full">
          <span v-if="q">Showing {{ tasks.items.length }} result(s) for “{{ q }}”</span>
        </div>
      </div>

      <!-- List -->
      <div class="flex-1 p-4">
        <div v-if="tasks.loading">
          <USkeleton class="h-12 mb-2" v-for="n in 4" :key="n" />
        </div>
        <div v-else-if="!tasks.items.length" class="p-8">
          <UAlert icon="i-heroicons-inbox-20-solid" title="No tasks" description="Add your first task below." />
        </div>
        <div v-else class="space-y-2">
          <div ref="taskList" class="space-y-2">
            <TaskItem v-for="t in tasks.items" :key="t.id" :task="t" />
          </div>
        </div>
      </div>

      <!-- Task composer -->
      <div class="p-4 border-t border-black/10 dark:border-white/10 bg-white/80 dark:bg-gray-900/80 backdrop-blur sticky bottom-0">
        <TaskComposer :date="date" />
      </div>
    </section>
  </NuxtLayout>
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
  SelectLabel
} from 'reka-ui'
import Sortable from 'sortablejs'
import { onMounted, ref, nextTick, watchEffect } from 'vue'
import { useEventBus, watchDebounced } from '@vueuse/core'
import { useTasks } from '@/stores/tasks'
import TaskComposer from '@/components/TaskComposer.vue'
import TaskItem from '@/components/TaskItem.vue'
import SidebarDates from '@/components/SidebarDates.vue'


definePageMeta({ layout: false })

const taskList = ref<HTMLElement | null>(null)
const tasks = useTasks()

const today = new Date().toISOString().slice(0, 10)
const date  = ref(today)
const q     = ref('')

const sort = ref<'position' | 'priority' | 'created_at'>('position')
const dir  = ref<'asc' | 'desc'>('asc')

const sortOpts: { label: string; value: 'position' | 'priority' | 'created_at' }[] = [
  { label: 'Position', value: 'position' },
  { label: 'Priority', value: 'priority' },
  { label: 'Created',  value: 'created_at' },
]

const dirOpts: { label: string; value: 'asc' | 'desc' }[] = [
  { label: 'Asc', value: 'asc' },
  { label: 'Desc', value: 'desc' },
]

async function load () {
  if (q.value.trim()) {
    await tasks.search(q.value.trim())
  } else {
    await tasks.fetchByDate(date.value, { sort: sort.value, dir: dir.value })
  }
}

function setDate(d: string) {
  date.value = d
  q.value = ''
  load()
}

watch([date, sort, dir], load, { immediate: true })

// Global search from header (if you emit on 'global-search')
const bus = useEventBus<string>('global-search')
bus.on((value) => { q.value = value ?? '' })
watchDebounced(q, load, { debounce: 250, maxWait: 600 })

watchEffect(() => {
  if (!taskList.value || !tasks.items.length) return

  if ((taskList.value as any)._sortable) return

  const sortable = Sortable.create(taskList.value, {
    animation: 150,
    draggable: '.task-row',
    handle: '.drag-handle',
    ghostClass: 'sortable-ghost',
    chosenClass: 'sortable-chosen',
    forceFallback: true,
    fallbackOnBody: true,
    onEnd: async (evt: { oldIndex?: number | null; newIndex?: number | null }) => {
      if (evt.oldIndex == null || evt.newIndex == null) return
      const movedItem = tasks.items.splice(evt.oldIndex, 1)[0]
      if (!movedItem) return
      tasks.items.splice(evt.newIndex, 0, movedItem)
      await tasks.reorder(
        tasks.items.map((t, i) => ({ id: t.id, position: i + 1 })),
        date.value
      )
    },
  })

  // Store it so we don’t re-initialize
  ;(taskList.value as any)._sortable = sortable
})

onMounted(async () => {
  await nextTick()
  if (!taskList.value) return

  Sortable.create(taskList.value, {
    animation: 150,
    draggable: '.task-row',
    handle: '.drag-handle',
    ghostClass: 'sortable-ghost',
    chosenClass: 'sortable-chosen',
    forceFallback: true,
    fallbackOnBody: true,
    delayOnTouchOnly: true,
    delay: 120,
    async onEnd(evt: { oldIndex?: number | null; newIndex?: number | null }) {
      if (evt.oldIndex == null || evt.newIndex == null) return

      const moved = tasks.items.splice(evt.oldIndex, 1)[0]
      if (!moved) return
      tasks.items.splice(evt.newIndex, 0, moved)

      await tasks.reorder(
        tasks.items.map((t, i) => ({ id: t.id, position: i + 1 })),
        date.value
      )
    },
  })
})
</script>