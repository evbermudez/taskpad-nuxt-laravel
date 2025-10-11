<template>
  <form @submit.prevent="onSubmit" class="flex flex-col sm:flex-row flex-wrap items-start sm:items-center gap-3">
    <!-- Statement -->
    <template v-if="mode === 'hero'">
      <textarea
        v-model="statement"
        rows="4"
        placeholder="What do you want to do today?"
        :disabled="pending"
        class="flex-1 w-full min-w-[260px] text-base rounded-md border border-gray-300 dark:border-gray-700
               bg-white dark:bg-gray-800 text-gray-900 dark:text-gray-100
               placeholder:text-gray-400 dark:placeholder:text-gray-500
               focus:ring-2 focus:ring-primary/40 focus:border-primary/40 transition-colors
               px-3 py-3 resize-none"
        @keydown.ctrl.enter.prevent="onSubmit"
        @keydown.meta.enter.prevent="onSubmit"
        autofocus
      />
    </template>
    <template v-else>
      <UInput
        v-model="statement"
        :placeholder="hasItems ? 'What else do you want to do?' : 'Add a task…'"
        :disabled="pending"
        class="flex-1 min-w-[220px] w-full text-sm rounded-md border border-gray-300 dark:border-gray-700
               bg-white dark:bg-gray-800 text-gray-900 dark:text-gray-100
               placeholder:text-gray-400 dark:placeholder:text-gray-500
               focus-within:ring-2 focus-within:ring-primary/40"
        @keyup.enter.prevent="onSubmit"
        autofocus
      />
    </template>

    <!-- Priority -->
    <SelectRoot v-model="priorityStr" :disabled="pending">
      <SelectTrigger
        class="border rounded px-3 py-1.5 text-sm bg-white dark:bg-gray-800 border-gray-300 dark:border-gray-700"
      >
        <SelectValue placeholder="Priority" />
      </SelectTrigger>
      <SelectContent class="border rounded-md bg-white dark:bg-gray-800 shadow-md">
        <SelectViewport>
          <SelectGroup>
            <SelectLabel class="px-2 py-1 text-xs text-gray-500 dark:text-gray-400">Priority</SelectLabel>
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
      :loading="pending"
      :disabled="pending || !statement.trim()"
      class="rounded-md inline-flex items-center gap-1.5 px-4 py-2 text-sm"
      @click="onSubmit"
    >
      <Plus class="size-4" /> Add
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
import { Plus } from 'lucide-vue-next'
import { useTasks } from '@/stores/tasks'

const props = defineProps<{
  date: string      // YYYY-MM-DD
  hasItems?: boolean
  mode?: 'inline' | 'hero'
}>()

const mode = computed(() => props.mode ?? 'inline')
const hasItems = computed(() => props.hasItems ?? true)

const tasks = useTasks()
const statement = ref('')
const priorityStr = ref<'1' | '2' | '3'>('2') // default Medium
const pending = ref(false)
const err = ref('')

async function onSubmit() {
  if (pending.value) return
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
    statement.value = ''
    priorityStr.value = '2'
  } catch (e: any) {
    err.value = e?.data?.message || e?.message || 'Could not add task.'
  } finally {
    pending.value = false
  }
}
</script>