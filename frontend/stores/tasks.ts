import { defineStore } from 'pinia'
import type { Ref } from 'vue'
import { useApi } from '@/composables/useApi'

export type Task = {
  id: number
  statement: string
  task_date: string   // YYYY-MM-DD
  priority: number    // 1=High,2=Med,3=Low
  position: number
  is_done: boolean
}

export const useTasks = defineStore('tasks', () => {
  const { api } = useApi()
  const items: Ref<Task[]> = ref([])
  const loading = ref(false)
  const error = ref<string | null>(null)

  async function fetchByDate(
    date: string,
    params: { q?: string; sort?: string; dir?: string } = {}
  ) {
    loading.value = true
    error.value = null
    try {
      items.value = await api<Task[]>('/tasks', { params: { date, ...params } })
    } catch (e: any) {
      error.value = e?.data?.message || e?.message || 'Failed to load tasks'
    } finally {
      loading.value = false
    }
  }

  async function create(payload: { statement: string; task_date: string; priority?: number }) {
    const t = await api<Task>('/tasks', { method: 'POST', body: payload })
    items.value.unshift(t)
  }

  async function toggle(id: number) {
    const t = await api<Task>(`/tasks/${id}/toggle`, { method: 'POST' })
    const i = items.value.findIndex(x => x.id === id)
    if (i >= 0) items.value[i] = t
  }

  async function update(id: number, patch: Partial<Omit<Task, 'id'>>) {
    const t = await api<Task>(`/tasks/${id}`, { method: 'PATCH', body: patch })
    const i = items.value.findIndex(x => x.id === id)
    if (i >= 0) items.value[i] = t
  }

  async function remove(id: number) {
    await api(`/tasks/${id}`, { method: 'DELETE' })
    items.value = items.value.filter(t => t.id !== id)
  }

  async function search(q: string) {
    loading.value = true
    try {
      items.value = await api<Task[]>('/search', { params: { q } })
    } finally {
      loading.value = false
    }
  }

  async function reorder(orders: { id: number; position: number }[]) {
    await api('/tasks/reorder', { method: 'POST', body: { orders } })
  }

  return {
    items,
    loading,
    error,
    fetchByDate,
    create,
    toggle,
    update,
    remove,
    search,
    reorder,
  }
})