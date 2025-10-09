<template>
  <main class="min-h-screen grid place-items-center p-6">
    <form @submit.prevent="onSubmit" class="w-full max-w-sm space-y-4 p-6 rounded-2xl shadow">
      <h1 class="text-2xl font-bold text-center">TaskPad Login</h1>

      <label class="block">
        <span class="text-sm">Email</span>
        <input v-model="email" type="email" required class="mt-1 w-full rounded border p-2" placeholder="matt@example.com" />
      </label>

      <label class="block">
        <span class="text-sm">Password</span>
        <input v-model="password" type="password" required class="mt-1 w-full rounded border p-2" placeholder="password" />
      </label>

      <button :disabled="auth.loading" class="w-full rounded bg-black text-white py-2">
        {{ auth.loading ? 'Logging in…' : 'Login' }}
      </button>

      <p v-if="auth.error" class="text-red-600 text-sm text-center">{{ auth.error }}</p>
    </form>
  </main>
</template>

<script setup lang="ts">
import { useAuth } from '@/stores/auth'
const auth = useAuth()
const email = ref('matt@example.com')
const password = ref('password')
const router = useRouter()

async function onSubmit() {
  await auth.login(email.value, password.value)
  if (auth.user) router.push('/')
}
</script>