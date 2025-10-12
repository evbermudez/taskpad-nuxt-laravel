<template>
  <main class="min-h-screen flex flex-col items-center justify-center px-4 sm:px-6 bg-white transition-colors">
    <div class="flex items-center justify-center py-6">
        <Notebook class="w-10 h-10 text-primary dark:text-primary/80" />
      </div>
    <form
      class="w-full max-w-sm space-y-4 p-6 rounded-2xl shadow"
      @submit.prevent="onSubmit"
    >
      <h1 class="text-2xl font-bold text-center text-pink">
        TaskPad Login
      </h1>

      <label class="block">
        <span class="text-sm">Email</span>
        <input
          v-model="email"
          type="email"
          required
          class="mt-1 w-full rounded border p-2"
          placeholder="matt@example.com"
        >
      </label>

      <label class="block">
        <span class="text-sm">Password</span>
        <input
          v-model="password"
          type="password"
          required
          class="mt-1 w-full rounded border p-2"
          placeholder="password"
        >
      </label>

      <button
        :disabled="auth.loading"
        class="w-full rounded bg-black text-white py-2"
      >
        {{ auth.loading ? 'Logging in…' : 'Login' }}
      </button>

      <a
        href="#"
        class="block text-center text-sm text-gray-500 hover:underline"
      >Forgot password?</a>

      <p
        v-if="auth.error"
        class="text-red-600 text-sm text-center"
      >
        {{ auth.error }}
      </p>
    </form>
  </main>
</template>

<script setup lang="ts">
import { ref } from 'vue'
import { definePageMeta } from '#imports'
import { useRouter } from 'vue-router'
import { useAuth } from '@/stores/auth'
import { Notebook } from 'lucide-vue-next'


definePageMeta({ layout: false })
const auth = useAuth()
const email = ref('matt@example.com')
const password = ref('password')
const router = useRouter()

async function onSubmit() {
  await auth.login(email.value, password.value)
  if (auth.user) router.push('/')
}
</script>