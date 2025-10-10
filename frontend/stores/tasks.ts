import { defineStore } from 'pinia'
import { useApi } from '@/composables/useApi'

export type Task = {
  id: number
  statement: string
  task_date: string  // YYYY-MM-DD
  priority: 1|2|3
  position: number
  is_done: boolean
  created_at?: string
  updated_at?: string
}

type SortKey = 'position' | 'priority' | 'created_at'
type Dir = 'asc' | 'desc'

export const useTasks = defineStore('tasks', {
  state: () => ({
    items: [] as Task[],
    loading: false,
    error: '' as string,
    date: new Date().toISOString().slice(0, 10), // today
    sort: 'position' as SortKey,
    dir: 'asc' as Dir,
    q: '' as string,
  }),
  actions: {
    async fetchByDate() {
      const { api } = useApi()
      this.loading = true; this.error = ''
      try {
        const data = await api<Task[]>('/tasks', {
          query: { date: this.date, sort: this.sort, dir: this.dir }
        })
        this.items = data
      } catch (e: any) {
        this.error = e?.data?.message || 'Failed to load tasks'
      } finally {
        this.loading = false
      }
    },

    async add(statement: string, priority: 1|2|3 = 2) {
      const { api } = useApi()
      const body = { statement, task_date: this.date, priority }
      await api<Task>('/tasks', { method: 'POST', body })
      
      await this.fetchByDate()
    },

    async toggle(id: number) {
      const { api } = useApi()
      const t = await api<Task>(`/tasks/${id}/toggle`, { method: 'POST' })
      const i = this.items.findIndex(x => x.id === id)
      if (i >= 0) this.items[i] = t
    },

    async update(id: number, patch: Partial<Pick<Task, 'statement'|'task_date'|'priority'|'is_done'>>) {
      const { api } = useApi()
      const t = await api<Task>(`/tasks/${id}`, { method: 'PATCH', body: patch })
      const i = this.items.findIndex(x => x.id === id)
      if (i >= 0) this.items[i] = t
    },

    async remove(id: number) {
      const { api } = useApi()
      await api(`/tasks/${id}`, { method: 'DELETE' })
      this.items = this.items.filter(x => x.id !== id)
    },

    async reorder(newOrder: { id: number; position: number }[]) {
      const { api } = useApi()
      await api('/tasks/reorder', { method: 'POST', body: { date: this.date, orders: newOrder } })
      await this.fetchByDate()
    },

    async searchAll(q: string) {
      const { api } = useApi()
      this.q = q
      if (!q) return this.fetchByDate()
      this.loading = true; this.error = ''
      try {
        const data = await api<Task[]>('/search', { query: { q } })
        this.items = data
      } catch (e: any) {
        this.error = e?.data?.message || 'Search failed'
      } finally {
        this.loading = false
      }
    }
  }
})