<template>
  <div class="space-y-6">
    <!-- Filters -->
    <div class="flex flex-wrap items-center gap-3">
      <UInput v-model="date" type="date" class="w-48" @update:model-value="load" />
      <UInput v-model="q" placeholder="Search across all dates…" class="w-64" @input="queueSearch" />
      <USelect v-model="sort" :options="sortOpts" class="w-40" @change="load" />
      <USelect v-model="dir"  :options="dirOpts"  class="w-28" @change="load" />

      <div class="ms-auto flex gap-2">
        <UButton icon="i-heroicons-chevron-left-20-solid" variant="soft" @click="shift(-1)">Prev</UButton>
        <UButton icon="i-heroicons-chevron-right-20-solid" variant="soft" @click="shift(1)">Next</UButton>
      </div>
    </div>

    <!-- Composer -->
    <UCard>
      <TaskComposer :date="date" />
    </UCard>

    <!-- Loading -->
    <div v-if="tasks.loading">
      <USkeleton class="h-12 mb-2" v-for="n in 3" :key="n" />
    </div>

    <!-- Empty -->
    <div v-else-if="!tasks.items.length">
      <UAlert
        icon="i-heroicons-inbox-20-solid"
        title="No tasks yet"
        description="Add your first task for this date."
      />
    </div>

    <!-- List -->
    <div v-else class="grid gap-2">
      <TaskItem v-for="t in tasks.items" :key="t.id" :task="t" />
    </div>
  </div>
</template>

<script setup lang="ts">
import { useTasks } from '@/stores/tasks'
import TaskComposer from '@/components/TaskComposer.vue'
import TaskItem from '@/components/TaskItem.vue'

definePageMeta({ layout: 'default' })

const tasks = useTasks()

// date + filters
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

// debounce search (client-only)
let timer: ReturnType<typeof setTimeout> | null = null
function queueSearch() {
  if (timer) clearTimeout(timer)
  timer = setTimeout(async () => {
    if (q.value.trim()) {
      await tasks.search(q.value.trim())
    } else {
      await load()
    }
  }, 250)
}

function shift(days: number) {
  const d = new Date(date.value)
  d.setDate(d.getDate() + days)
  date.value = d.toISOString().slice(0, 10)
  load()
}

onMounted(load)
</script>