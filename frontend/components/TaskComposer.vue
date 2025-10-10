<template>
  <UForm @submit.prevent="submit" class="grid gap-3 md:grid-cols-[1fr,auto,auto]">
    <UInput v-model="text" placeholder="New task…" :disabled="busy" />
    <USelect v-model="priority" :options="PRIORITY_OPTS" class="md:w-40" :disabled="busy" />
    <UButton type="submit" :loading="busy">Add</UButton>
  </UForm>
</template>

<script setup lang="ts">
import { useTasks } from '@/stores/tasks'
const props = defineProps<{ date: string }>()
const tasks = useTasks()

const text = ref('')
const priority = ref(2)
const busy = ref(false)
const PRIORITY_OPTS = [
  { label: 'High', value: 1 },
  { label: 'Medium', value: 2 },
  { label: 'Low', value: 3 },
]

async function submit() {
  if (!text.value.trim()) return
  busy.value = true
  await tasks.create({ statement: text.value.trim(), task_date: props.date, priority: priority.value })
  text.value = ''
  busy.value = false
}
</script>