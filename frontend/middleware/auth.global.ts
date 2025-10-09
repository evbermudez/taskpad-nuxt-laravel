import { useAuth } from '@/stores/auth'

export default defineNuxtRouteMiddleware(async (to) => {
  const auth = useAuth()
  if (auth.user === null) {
    await auth.fetchMe()
  }
  const publicRoutes = ['/login']
  if (!publicRoutes.includes(to.path) && !auth.user) {
    return navigateTo('/login')
  }
})