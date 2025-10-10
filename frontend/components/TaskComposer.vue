<template>
  <form @submit.prevent="submit" class="flex flex-wrap items-center gap-3">
    <!-- Statement -->
    <UInput
      v-model="statement"
      placeholder="Add a task…"
      class="flex-1 min-w-[220px]"
      :disabled="pending"
      @keyup.enter="submit"
      autofocus
    />

    <!-- Priority -->
    <SelectRoot v-model="priorityStr" :disabled="pending">
      <SelectTrigger class="border rounded px-3 py-1.5 text-sm bg-white dark:bg-gray-800 min-w-28">
        <SelectValue placeholder="Priority" />
      </SelectTrigger>
      <SelectContent class="border rounded-md bg-white dark:bg-gray-800 shadow-md">
        <SelectViewport>
          <SelectGroup>
            <SelectLabel class="px-2 py-1 text-xs text-gray-500">Priority</SelectLabel>
            <SelectItem value="1" class="px-3 py-1.5 cursor-pointer hover:bg-gray-100 dark:hover:bg-gray-700">High</SelectItem>
            <SelectItem value="2" class="px-3 py-1.5 cursor-pointer hover:bg-gray-100 dark:hover:bg-gray-700">Medium</SelectItem>
            <SelectItem value="3" class="px-3 py-1.5 cursor-pointer hover:bg-gray-100 dark:hover:bg-gray-700">Low</SelectItem>
          </SelectGroup>
        </SelectViewport>
      </SelectContent>
    </SelectRoot>

    <!-- Add -->
    <UButton
      type="submit"
      icon="i-heroicons-plus-20-solid"
      :loading="pending"
    >
      Add
    </UButton>

    <!-- Error -->
    <p v-if="err" class="text-sm text-red-600">{{ err }}</p>
  </form>
</template>

<script setup lang="ts">
import {
  SelectRoot, SelectTrigger, SelectValue,
  SelectContent, SelectViewport, SelectGroup,
  SelectItem, SelectLabel
} from 'reka-ui'
import { useTasks } from '@/stores/tasks'

const props = defineProps<{
  date: string  // YYYY-MM-DD
}>()

const tasks = useTasks()

const statement = ref('')
const priorityStr = ref<'1' | '2' | '3'>('2') // default Medium
const pending = ref(false)
const err = ref('')

async function submit() {
  err.value = ''
  const s = statement.value.trim()
  if (!s) { err.value = 'Please enter a task.'; return }

  pending.value = true
  try {
    await tasks.create({
      statement: s,
      task_date: props.date,
      priority: Number(priorityStr.value)
    })
    // clear
    statement.value = ''
    priorityStr.value = '2'
  } catch (e: any) {
    err.value = e?.data?.message || e?.message || 'Could not add task.'
  } finally {
    pending.value = false
  }
}
</script>