<template>
  <UCard :ui="{ body: 'p-3' }"
    class="task-row group transition-colors rounded-xl border
           border-black/5 dark:border-white/10
           bg-white/90 dark:bg-gray-900/80
           hover:bg-white dark:hover:bg-gray-800/80"
    :class="task.is_done
      ? 'bg-gray-50 dark:bg-gray-800/70'
      : ''"
  >
    <div class="flex items-center gap-3">

      <div
        class="drag-handle flex items-center justify-center size-6 rounded hover:bg-gray-100 dark:hover:bg-gray-800 cursor-grab active:cursor-grabbing"
        aria-label="Drag"
      >
        <GripVertical class="size-4 text-gray-400 hover:text-gray-600 dark:text-gray-500 dark:hover:text-gray-300" />
      </div>

      <!-- Checkbox -->
      <CheckboxRoot
        :checked="props.task.is_done"
        @click.stop="toggleTask"
        class="size-5 shrink-0 grid place-items-center rounded-sm
              border border-gray-400 dark:border-gray-600
              bg-white dark:bg-gray-800
              data-[state=checked]:bg-gray-900
              transition-colors focus-visible:outline-none"
      >
        <CheckboxIndicator>
          <Check class="size-3.5 text-white dark:text-white stroke-[4]" />
        </CheckboxIndicator>
      </CheckboxRoot>

      <!-- Task content -->
      <div class="flex-1">
        <template v-if="editing">
          <div class="flex flex-col sm:flex-row gap-2">
            <UInput v-model="draft" class="flex-1" @keyup.enter="save" autofocus />
          </div>
        </template>

        <template v-else>
          <button
            class="text-left w-full"
            @click="startEdit"
            :disabled="task.is_done"
            :class="['text-left w-full', task.is_done && 'cursor-not-allowed opacity-60']"
          >
            <p :class="task.is_done ? 'line-through opacity-60' : ''">
              {{ task.statement }}
            </p>
          </button>
        </template>
      </div>

      <!-- Delete dialog -->
      <DialogRoot>
        <DialogTrigger as-child>
          <UButton
            color="error"
            variant="ghost"
            class="opacity-0 group-hover:opacity-100 transition-opacity"
            @click.stop
          >
            <Trash2 class="size-5 text-gray-400 hover:text-gray-600 dark:text-gray-500 dark:hover:text-gray-300" />
          </UButton>
        </DialogTrigger>

        <DialogPortal>
          <DialogOverlay class="fixed inset-0 bg-black/50 backdrop-blur-sm" />

          <DialogContent
            class="fixed left-1/2 top-1/2 w-full max-w-sm -translate-x-1/2 -translate-y-1/2 rounded-xl bg-white p-4 shadow-lg ring-1 ring-black/10 dark:bg-gray-900 dark:ring-white/10"
          >
            <DialogTitle class="text-base font-semibold text-gray-600 dark:text-gray-400">
              Delete task?
            </DialogTitle>

            <DialogDescription class="mt-1 text-sm text-gray-600 dark:text-gray-400">
              Remove “{{ task.statement }}” permanently?
            </DialogDescription>

            <div class="mt-4 flex justify-end gap-2">
              <DialogClose as-child>
                <UButton variant="outline" class="text-gray-600 dark:text-gray-400 font-semibold">Cancel</UButton>
              </DialogClose>

              <DialogClose as-child>
                <UButton variant="outline" color="error" class="text-gray-600 dark:text-gray-400 font-semibold" @click="doDelete">Delete</UButton>
              </DialogClose>
            </div>
          </DialogContent>
        </DialogPortal>
      </DialogRoot>
    </div>
  </UCard>
</template>

<script setup lang="ts">
import { useTasks } from '@/stores/tasks'
import type { Task } from '@/stores/tasks'
import {
  CheckboxRoot,
  CheckboxIndicator,
  DialogClose,
  DialogContent,
  DialogDescription,
  DialogOverlay,
  DialogPortal,
  DialogRoot,
  DialogTitle,
  DialogTrigger,
} from 'reka-ui'
import { Check, GripVertical, Trash2 } from 'lucide-vue-next'

const tasks = useTasks()
const props = defineProps<{ task: Task }>()
const editing = ref(false)
const draft = ref(props.task.statement)

async function toggleTask() {
  await tasks.toggle(props.task.id)
}

function startEdit() {
  editing.value = true
  draft.value = props.task.statement
}

async function save() {
  if (!editing.value) return
  await tasks.update(props.task.id, {
    statement: draft.value.trim()
  })
  editing.value = false
}

async function doDelete() {
  await tasks.remove(props.task.id)
}
</script>

<style scoped>
@keyframes fade-in {
  from { opacity: 0 }
  to { opacity: 1 }
}
@keyframes fade-out {
  from { opacity: 1 }
  to { opacity: 0 }
}
.data-\[state\=open\]\:animate-fade-in {
  animation: fade-in 0.2s ease-out;
}
.data-\[state\=closed\]\:animate-fade-out {
  animation: fade-out 0.2s ease-in;
}
</style>