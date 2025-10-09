import { defineStore } from 'pinia'
import { useApi } from '@/composables/useApi'

type User = { id: number; name: string; email: string } | null

export const useAuth = defineStore('auth', {
  state: () => ({
    user: null as User,
    loading: false as boolean,
    error: '' as string
  }),
  actions: {
    async fetchMe() {
      const { client } = useApi()
      try {
        this.user = await client('/me') as any
      } catch {
        this.user = null
      }
    },
    async login(email: string, password: string) {
      const { csrf, webBase } = useApi()
      this.loading = true; this.error = ''
      try {
        await csrf()
        await $fetch(`${webBase}/login`, {
          method: 'POST',
          credentials: 'include',
          headers: {
            'X-Requested-With': 'XMLHttpRequest',
            'Accept': 'application/json',
            'Content-Type': 'application/json'
          },
          body: { email, password }
        })
        await this.fetchMe()
      } catch (e: any) {
        this.error = e?.data?.message || 'Login failed'
        this.user = null
      } finally {
        this.loading = false
      }
    },
    async logout() {
      const { webBase } = useApi()
      try {
        await $fetch(`${webBase}/logout`, {
          method: 'POST',
          credentials: 'include',
          headers: { 'X-Requested-With': 'XMLHttpRequest', 'Accept': 'application/json' }
        })
      } finally {
        this.user = null
      }
    }
  }
})