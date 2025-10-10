import { useAuth } from '@/stores/auth'

export default defineNuxtRouteMiddleware(async (to) => {
  const auth = useAuth()

  if (!auth.ready) {
    try {
      await auth.fetchMe()
    } catch (e) {
      console.warn('fetchMe failed:', e)
    }
  }

  console.log('[middleware] after fetch, user:', auth.user)

  const isPublic = to.path === '/login'

  console.log('[mw] path:', to.path, 'user?', !!auth.user)
  
  if (!auth.user && !isPublic) {
    console.warn('[middleware] redirect → /login')
    return navigateTo('/login', { replace: true })
  }

  if (auth.user && isPublic) {
    console.warn('[middleware] redirect → /')
    return navigateTo('/', { replace: true })
  }
})