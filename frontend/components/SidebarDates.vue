<template>
  <div class="p-3 space-y-2">
    <h3 class="px-2 text-xs uppercase tracking-wide opacity-60">Dates</h3>

    <nav class="space-y-1">
      <UButton
        v-for="d in dates"
        :key="d.value"
        class="w-full justify-start rounded-full"
        :color="date === d.value ? 'primary' : 'neutral'"
        :variant="date === d.value ? 'solid' : 'soft'"
        :label="d.label"
        @click="$emit('select', d.value)"
      />
    </nav>
  </div>
</template>

<script setup lang="ts">
type DateItem = { label: string; value: string }

const props = defineProps<{ date: string }>()
defineEmits<{ (e: 'select', value: string): void }>()

const today = new Date()
const iso = (d: Date) => d.toISOString().slice(0, 10)

const dates = computed<DateItem[]>(() => {
  const out: DateItem[] = []
  for (let i = 0; i < 10; i++) {
    const d = new Date(today)
    d.setDate(today.getDate() - i)
    out.push({
      value: iso(d),
      label:
        i === 0
          ? 'Today'
          : i === 1
            ? 'Yesterday'
            : d.toLocaleDateString(undefined, {
                weekday: 'long',
                month: 'long',
                day: 'numeric'
              })
    })
  }
  return out
})
</script>