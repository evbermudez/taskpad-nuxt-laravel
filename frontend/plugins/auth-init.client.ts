import { useAuth } from '@/stores/auth'
import { defineNuxtPlugin } from '#imports'

export default defineNuxtPlugin(async () => {
  const auth = useAuth()
if (!auth.ready) await auth.restore()
})