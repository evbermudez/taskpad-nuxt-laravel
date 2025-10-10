<template>
  <UContainer class="space-y-6">
    <div class="flex flex-wrap items-center gap-3">
      <UInput v-model="date" type="date" class="w-48" @update:model-value="load" />
      <UInput v-model="q" placeholder="Search across dates…" class="w-64" @keyup="handleSearch" />
      <USelect v-model="sort" :options="sortOpts" class="w-40" @change="load" />
      <USelect v-model="dir" :options="dirOpts" class="w-28" @change="load" />
    </div>

    <TaskComposer :date="date" />

    <div class="my-4 h-px bg-gray-200 dark:bg-gray-800"></div>

    <div v-if="tasks.loading">
      <USkeleton class="h-12 mb-2" v-for="n in 3" :key="n" />
    </div>

    <div v-else-if="!tasks.items.length">
      <UAlert title="No tasks yet" description="Add your first task for this date." icon="i-heroicons-inbox" />
    </div>

    <div class="grid gap-2">
      <TaskItem v-for="t in tasks.items" :key="t.id" :task="t" />
    </div>
  </UContainer>
</template>

<script setup lang="ts">
import { useTasks } from '@/stores/tasks'
import TaskComposer from '@/components/TaskComposer.vue'
import TaskItem from '@/components/TaskItem.vue'
import { useAuth } from '@/stores/auth'

definePageMeta({ layout: 'default' })

const tasks = useTasks()
const auth = useAuth()
const today = new Date().toISOString().slice(0,10)

const date = ref(today)
const q = ref('')
const sort = ref<'position'|'priority'|'created_at'>('position')
const dir  = ref<'asc'|'desc'>('asc')

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
  await tasks.fetchByDate(date.value, { sort: sort.value, dir: dir.value })
}

async function handleSearch(e: KeyboardEvent) {
  if ((e.target as HTMLInputElement).value.trim().length === 0) return load()
  await tasks.search(q.value.trim())
}

onMounted(load)
</script>