<template>
  <section class="p-6 space-y-4">
    <h1 class="text-2xl font-bold">Your Tasks</h1>
    <p class="text-gray-500">This is where your task list will appear soon.</p>

    <div class="p-4 rounded-xl bg-red-500 text-white">
      If this is red, Tailwind utilities are applying correctly.
    </div>
  </section>
</template>

<script setup lang="ts">
definePageMeta({ layout: 'default' })
</script>