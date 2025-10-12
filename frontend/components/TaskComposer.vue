<template>
  <form @submit.prevent="onSubmit" class="flex gap-2" :class="mode === 'hero' ? 'flex-col' : 'flex-row'">
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
               focus:ring-1 focus:ring-primary/40 focus:border-primary/40 transition-colors
               p-3 resize-none"
        @keydown.ctrl.enter.prevent="onSubmit"
        @keydown.meta.enter.prevent="onSubmit"
        autofocus
        id="statement-textinput"
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
               focus-within:ring-2 focus-within:ring-primary/40 px-3"
        @keyup.enter.prevent="onSubmit"
        autofocus
        id="statement-input"
      />
    </template>

    <!-- Add -->
    <UButton
      type="submit"
      :loading="pending"
      :disabled="pending || !statement.trim()"
      variant="outline"
      icon="i-lucide-plus"
      class="inline-flex items-center justify-center gap-2 px-4 py-2 text-sm font-medium
         rounded-md border border-gray-300 dark:border-gray-700
         bg-gray-100 text-gray-900 dark:bg-gray-800 dark:text-gray-100
         hover:bg-gray-200 dark:hover:bg-gray-700
         transition-colors duration-150"
      @click="onSubmit"
    >
      Add
    </UButton>

    <!-- Error -->
    <p v-if="err" class="text-sm text-red-600">{{ err }}</p>
  </form>
</template>

<script setup lang="ts">
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
      task_date: props.date
    })
    statement.value = ''
  } catch (e: any) {
    err.value = e?.data?.message || e?.message || 'Could not add task.'
  } finally {
    pending.value = false
  }
}
</script>