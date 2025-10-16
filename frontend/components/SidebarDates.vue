<template>
  <div class="p-3 space-y-3">
    <h3 class="px-2 text-xs uppercase tracking-wide text-gray-500 dark:text-gray-400">
      Dates
    </h3>

    <ToggleGroupRoot
      v-model="selected"
      type="single"
      class="flex flex-col gap-1"
      aria-label="Pick a date"
    >
      <ToggleGroupItem
        v-for="d in dates"
        :key="d.value"
        :value="d.value"
        class="w-full justify-start rounded-full px-3 py-1.5 text-sm font-medium
               transition-colors
               data-[state=on]:bg-gray-300 data-[state=on]:text-gray-900
               data-[state=off]:bg-transparent data-[state=off]:text-gray-800
               dark:data-[state=off]:text-gray-200
               hover:data-[state=off]:bg-gray-100 dark:hover:data-[state=off]:bg-gray-800"
      >
        {{ d.label }}
      </ToggleGroupItem>
    </ToggleGroupRoot>

    <!-- Load older days -->
    <UButton
      variant="ghost"
      class="w-full justify-center text-xs"
      @click="showMore"
    >
      Older…
    </UButton>
  </div>
</template>

<script setup lang="ts">
import { ref, watch, computed } from 'vue'
import { ToggleGroupRoot, ToggleGroupItem } from 'reka-ui'

type DateItem = { label: string; value: string }

const props = defineProps<{ date: string }>()
const emit = defineEmits<{ select: [string] }>()

const selected = ref(props.date)
watch(() => props.date, (v) => { if (v !== selected.value) selected.value = v }, { immediate: true })
watch(selected, (v) => { if (v && v !== props.date) emit('select', v) })

const today = new Date()
const iso = (d: Date) => d.toISOString().slice(0, 10)

const visible = ref(14)

const dates = computed<DateItem[]>(() => {
  const out: DateItem[] = []
  for (let i = 0; i < visible.value; i++) {
    const d = new Date(today)
    d.setDate(today.getDate() - i)
    const label =
      i === 0
        ? 'Today'
        : i === 1
          ? 'Yesterday'
          : d.toLocaleDateString(undefined, { weekday: 'long', month: 'long', day: 'numeric' })
    out.push({ value: iso(d), label })
  }
  return out
})

function showMore() {
  visible.value += 14
}
</script>
