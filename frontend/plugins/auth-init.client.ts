import { useAuth } from '@/stores/auth'

export default defineNuxtPlugin(async () => {
  const auth = useAuth()
if (!auth.ready) await auth.restore()
})