import { useAuth } from '@/stores/auth'
import { defineNuxtRouteMiddleware } from '#imports'
import { navigateTo } from '#app'

export default defineNuxtRouteMiddleware(async (to) => {
  const auth = useAuth()

  // Restore session if not yet ready
  if (!auth.ready) {
    try {
      await auth.restore()
    } catch (e) {
      console.warn('[auth middleware] restore failed:', e)
    }
  }

  const isPublic = to.path === '/login'
  const isAuthenticated = !!auth.user

  console.log('[auth middleware]', { path: to.path, isAuthenticated })

  // Redirect guests away from protected pages
  if (!isAuthenticated && !isPublic) {
    console.warn('[auth middleware] → redirect to /login')
    return navigateTo('/login', { replace: true })
  }

  // Redirect logged-in users away from /login
  if (isAuthenticated && isPublic) {
    console.warn('[auth middleware] → redirect to /')
    return navigateTo('/', { replace: true })
  }
})