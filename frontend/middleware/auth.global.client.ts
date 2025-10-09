import { useAuth } from '@/stores/auth'

export default defineNuxtRouteMiddleware(async (to) => {
  const auth = useAuth()

  if (!auth.ready) {
    await auth.hydrate()
  }

  const isPublic = to.path === '/login'
  
  if (!auth.user && !isPublic) {
    return navigateTo('/login')
  }

  if (auth.user && isPublic) {
    return navigateTo('/')
  }
})