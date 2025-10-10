<template>
  <NuxtLayout name="default">
    <!-- left column -->
    <template #sidebar>
      <SidebarDates :date="date" @select="setDate" />
    </template>

    <!-- main column -->
    <div class="flex flex-col min-h-[calc(100dvh-3.5rem)]">
      <!-- top controls -->
      <div class="p-4 border-b border-black/10 flex items-center gap-3">
        <UInput type="date" v-model="date" class="w-48" @update:model-value="load" />
        <USelect v-model="sort" :options="sortOpts" class="w-40" @change="load" />
        <USelect v-model="dir"  :options="dirOpts"  class="w-28" @change="load" />
        <div class="ms-auto text-sm opacity-60" v-if="q">
          Showing {{ tasks.items.length }} result(s) for “{{ q }}”
        </div>
      </div>

      <!-- list -->
      <div class="flex-1 p-4">
        <div v-if="tasks.loading">
          <USkeleton class="h-12 mb-2" v-for="n in 4" :key="n" />
        </div>

        <div v-else-if="!tasks.items.length" class="p-8">
          <UAlert icon="i-heroicons-inbox-20-solid" title="No tasks" description="Add your first task below." />
        </div>

        <div v-else class="space-y-2">
          <TaskItem v-for="t in tasks.items" :key="t.id" :task="t" />
        </div>
      </div>

      <div class="p-4 border-t border-black/10 bg-white/80 dark:bg-gray-900/80 backdrop-blur sticky bottom-0">
        <TaskComposer :date="date" />
      </div>
    </div>
  </NuxtLayout>
</template>

<script setup lang="ts">
import { useEventBus } from '@vueuse/core'
import { useTasks } from '@/stores/tasks'
import TaskComposer from '@/components/TaskComposer.vue'
import TaskItem from '@/components/TaskItem.vue'
import SidebarDates from '@/components/SidebarDates.vue'

definePageMeta({ layout: 'default' })

const tasks = useTasks()

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
  load()
}

const bus = useEventBus<string>('global-search')
bus.on(async (value) => {
  q.value = value ?? ''
  await load()
})

onMounted(load)
</script>