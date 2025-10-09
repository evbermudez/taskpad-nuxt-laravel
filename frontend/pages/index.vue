<template>
  <main class="p-6">
    <div v-if="auth.user" class="space-y-2">
      <p>Welcome, <strong>{{ auth.user.name }}</strong></p>
      <button @click="handleLogout" class="rounded bg-red-600 text-white px-4 py-2">
        Logout
      </button>
    </div>

    <div v-else>
      <NuxtLink to="/login" class="text-blue-600 underline">Go to Login</NuxtLink>
    </div>
  </main>
</template>

<script setup lang="ts">
import { useAuth } from '@/stores/auth'
const auth = useAuth()
const router = useRouter()

async function handleLogout() {
  await auth.logout()
  router.push('/login')
}
</script>