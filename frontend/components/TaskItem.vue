<template>
  <UCard :ui="{ body: { padding: 'p-3' } }" class="group">
    <div class="flex items-center gap-3">
      <UCheckbox :model-value="task.is_done" @update:model-value="toggle" />

      <div class="flex-1">
        <template v-if="editing">
          <UInput v-model="draft" @keyup.enter="save" @blur="save" autofocus />
        </template>
        <template v-else>
          <button class="text-left w-full" @click="startEdit">
            <p :class="task.is_done ? 'line-through opacity-60' : ''">{{ task.statement }}</p>
            <p class="text-xs text-gray-500">Priority: {{ labelPriority(task.priority) }}</p>
          </button>
        </template>
      </div>

      <UButton color="red" variant="ghost" icon="i-heroicons-trash" @click="askDelete = true" class="opacity-0 group-hover:opacity-100 transition-opacity" />
    </div>

    <UModal v-model="askDelete">
      <UCard>
        <template #header>Delete Task?</template>
        <p>Remove “{{ task.statement }}” permanently?</p>
        <div class="mt-4 flex justify-end gap-2">
          <UButton variant="ghost" @click="askDelete = false">Cancel</UButton>
          <UButton color="red" @click="doDelete">Delete</UButton>
        </div>
      </UCard>
    </UModal>
  </UCard>
</template>

<script setup lang="ts">
import { useTasks } from '@/stores/tasks'
const tasks = useTasks()
const props = defineProps<{ task: any }>()
const editing = ref(false)
const draft = ref(props.task.statement)
const askDelete = ref(false)

function labelPriority(p: number) {
  return p === 1 ? 'High' : p === 2 ? 'Medium' : 'Low'
}

async function toggle(val: boolean) {
  await tasks.toggle(props.task.id)
}

function startEdit() {
  editing.value = true
  draft.value = props.task.statement
}

async function save() {
  if (!editing.value) return
  await tasks.update(props.task.id, { statement: draft.value.trim() })
  editing.value = false
}

async function doDelete() {
  await tasks.remove(props.task.id)
  askDelete.value = false
}
</script>