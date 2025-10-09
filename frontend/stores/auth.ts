import { defineStore } from 'pinia'
import { useApi } from '@/composables/useApi'

type User = { id:number; name:string; email:string } | null

export const useAuth = defineStore('auth', {
  state: () => ({ user: null as User, loading: false, error: '' }),
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
    async login(email: string, password: string) {
      const { login } = useApi()
      this.loading = true; this.error = ''
      try {
        await login(email, password)     // 👈 calls web client + CSRF
        await this.fetchMe()             // then get user from /api/me
      } catch (e: any) {
        this.error = e?.data?.message || 'Login failed'
        this.user = null
      } finally {
        this.loading = false
      }
    },
    async logout() {
      const { logout } = useApi()
      try { await logout() } finally { this.user = null }
    }
  }
})