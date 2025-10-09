import { defineStore } from 'pinia'
import { useApi } from '@/composables/useApi'

type User = { id:number; name:string; email:string } | null

export const useAuth = defineStore('auth', {
  state: () => ({
    user: null as User,
    loading: false,
    error: '',
    ready: false,
  }),
  actions: {
    async fetchMe() {
      const { api } = useApi()
      try {
        const res = await api('/me') as any
        this.user = res.user ?? null
      } catch {
        this.user = null
      }
    },
    async hydrate() {
      if (this.ready) return
      await this.fetchMe()
      this.ready = true
    },
    async login(email: string, password: string) {
      const { login } = useApi()
      this.loading = true; this.error = ''
      try {
        await login(email, password)
        await this.fetchMe()
        this.ready = true
      } catch (e: any) {
        this.error = e?.data?.message || 'Login failed'
        this.user = null
        this.ready = true
      } finally {
        this.loading = false
      }
    },
    async logout() {
      const { logout } = useApi()
      try {
        await logout()
      } catch (e) {
        console.error('Logout failed', e)
      } finally {
        this.user = null
        this.ready = true
      }
    }
  }
})