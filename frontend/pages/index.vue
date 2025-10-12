<template>
  <NuxtLayout name="default">
    <template #sidebar>
      <SidebarDates
        :date="date"
        @select="setDate"
      />
    </template>

    <!-- Main column -->
    <section class="flex flex-col min-h-[calc(100dvh-3.5rem)]">
      <!-- Top controls -->
      <div
        class="p-4 border-b border-black/10 dark:border-white/10
         flex flex-col sm:flex-row sm:flex-wrap gap-3
         sticky top-14 bg-white/80 dark:bg-gray-900/80 backdrop-blur"
      >
        <!-- Date picker -->
        <UInput
          v-model="date"
          type="date"
          class="w-full sm:w-48 border border-black/10 dark:border-white/10
           outline-none pl-2
           bg-white dark:bg-gray-800 text-gray-900 dark:text-gray-100
           placeholder:text-gray-400 dark:placeholder:text-gray-500"
        />

        <!-- Sort (Reka) -->
        <SelectRoot
          v-model="sort"
          @update:model-value="load"
        >
          <SelectTrigger
            class="w-full sm:w-auto border rounded px-3 py-2 text-sm
             bg-white dark:bg-gray-800
             focus:outline-none focus:ring-0 data-[state=open]:ring-0"
          >
            <SelectValue placeholder="Sort by" />
          </SelectTrigger>

          <SelectContent
            :position="'popper'"
            :side-offset="6"
            class="w-full sm:w-auto border rounded-md bg-white dark:bg-gray-800 shadow-md
             focus:outline-none ring-0"
          >
            <SelectViewport>
              <SelectGroup>
                <SelectLabel class="px-2 py-1 text-xs text-gray-500 dark:text-gray-400">
                  Sort
                </SelectLabel>
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

        <!-- Direction (Reka) -->
        <SelectRoot
          v-model="dir"
          @update:model-value="load"
        >
          <SelectTrigger
            class="w-full sm:w-auto border rounded px-3 py-2 text-sm
             bg-white dark:bg-gray-800
             focus:outline-none focus:ring-0 data-[state=open]:ring-0"
          >
            <SelectValue placeholder="Direction" />
          </SelectTrigger>

          <SelectContent
            :position="'popper'"
            :side-offset="6"
            class="w-full sm:w-auto border rounded-md bg-white dark:bg-gray-800 shadow-md
             focus:outline-none ring-0"
          >
            <SelectViewport>
              <SelectGroup>
                <SelectLabel class="px-2 py-1 text-xs text-gray-500 dark:text-gray-400">
                  Order
                </SelectLabel>
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

        <!-- Result info -->
        <div class="sm:ml-auto text-sm opacity-60 truncate max-w-full">
          <span v-if="q">Showing {{ tasks.items.length }} result(s) for “{{ q }}”</span>
        </div>
      </div>

      <!-- List -->
      <div class="flex-1 p-4">
        <div v-if="tasks.loading">
          <USkeleton
            v-for="n in 4"
            :key="n"
            class="h-12 mb-2"
          />
        </div>

        <!-- EMPTY STATE with big textarea composer -->
        <div
          v-else-if="!tasks.items.length"
          class="py-16"
        >
          <div class="mx-auto max-w-2xl text-center space-y-4">
            <h2 class="text-xl font-semibold">
              Start your day
            </h2>
            <p class="text-sm text-gray-500 dark:text-gray-400">
              Add your first task below.
            </p>
            <div class="mt-6">
              <TaskComposer
                :date="date"
                :has-items="false"
                mode="hero"
              />
            </div>
          </div>
        </div>

        <!-- HAS ITEMS -->
        <div
          v-else
          class="space-y-2"
        >
          <div
            ref="taskList"
            class="space-y-2"
          >
            <TaskItem
              v-for="t in tasks.items"
              :key="t.id"
              :task="t"
            />
          </div>
        </div>
      </div>

      <!-- Sticky composer: only when there are items -->
      <div
        v-if="tasks.items.length"
        class="p-4 border-t border-black/10 dark:border-white/10 bg-white/80 dark:bg-gray-900/80 backdrop-blur sticky bottom-0"
      >
        <TaskComposer
          :date="date"
          :has-items="true"
          mode="inline"
        />
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
import { onMounted, ref, nextTick, watchEffect, watch } from 'vue'
import { useTasks } from '@/stores/tasks'
import TaskComposer from '@/components/TaskComposer.vue'
import TaskItem from '@/components/TaskItem.vue'
import SidebarDates from '@/components/SidebarDates.vue'
import { definePageMeta } from '#imports'
import { useSearchStore } from '@/stores/search'
import { storeToRefs } from 'pinia'
import { watchDebounced } from '@vueuse/core'

definePageMeta({ layout: false })

const taskList = ref<HTMLElement | null>(null)
const tasks = useTasks()

const today = new Date().toISOString().slice(0, 10)
const date  = ref(today)
const q     = ref('')

const sort = ref<'position' | 'created_at'>('position')
const dir  = ref<'asc' | 'desc'>('asc')

const sortOpts: { label: string; value: 'position' | 'created_at' }[] = [
  { label: 'Position', value: 'position' },
  { label: 'Created', value: 'created_at' },
]

const dirOpts: { label: string; value: 'asc' | 'desc' }[] = [
  { label: 'Asc', value: 'asc' },
  { label: 'Desc', value: 'desc' },
]

const search = useSearchStore()
const { query } = storeToRefs(search)

async function load () {
  if (search.query.trim()) {
    await tasks.search(search.query.trim())
  } else {
    await tasks.fetchByDate(date.value, { sort: sort.value, dir: dir.value })
  }
}

function setDate(d: string) {
  date.value = d
  search.clear()
  load()
}

watch([date, sort, dir], load, { immediate: true })

watchDebounced(query, load, { debounce: 250, maxWait: 600 })

watchEffect(() => {
  if (!taskList.value || !tasks.items.length) return

  if (search.query.trim()) return

  if ((taskList.value as any)._sortable) return

  const sortable = Sortable.create(taskList.value, {
    animation: 150,
    draggable: '.task-row',
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

<style scoped>
:deep(input:focus),
:deep(input:active) {
  outline: none !important;
  box-shadow: none !important;
}
</style>