import { defineStore } from 'pinia'
import type { Ref } from 'vue'
import { useApi } from '@/composables/useApi'

export type Task = {
  id: number
  statement: string
  task_date: string
  position: number
  is_done: boolean
}

export const useTasks = defineStore('tasks', () => {
  const { api } = useApi()
  const items: Ref<Task[]> = ref([])
  const loading = ref(false)
  const error = ref<string | null>(null)

  function unwrap<T>(r: T | { data: T }): T {
    return (r as any)?.data ?? (r as T)
  }

  async function fetchByDate(
    date: string,
    params: { q?: string; sort?: string; dir?: string } = {}
  ) {
    loading.value = true
    error.value = null
    try {
      const res = await api<{ data: Task[] }>('/tasks', { params: { date, ...params } })
      items.value = unwrap(res)
    } catch (e: any) {
      error.value = e?.data?.message || e?.message || 'Failed to load tasks'
    } finally {
      loading.value = false
    }
  }

  async function create(payload: { statement: string; task_date: string }) {
    try {
      const res = await api<Task | { data: Task }>('/tasks', { method: 'POST', body: payload })
      items.value.unshift(unwrap(res))
    } catch (e: any) {
      error.value = e?.data?.message || e?.message || 'Failed to create task'
    }
  }

  async function toggle(id: number) {
    const res = await api<Task | { data: Task }>(`/tasks/${id}/toggle`, { method: 'POST' })
    const t = unwrap(res)
    const i = items.value.findIndex(x => x.id === id)
    if (i >= 0) items.value[i] = t
  }

  async function update(id: number, patch: Partial<Omit<Task, 'id'>>) {
    const res = await api<Task | { data: Task }>(`/tasks/${id}`, { method: 'PATCH', body: patch })
    const t = unwrap(res)
    const i = items.value.findIndex(x => x.id === id)
    if (i >= 0) items.value[i] = t
  }

  async function remove(id: number) {
    await api(`/tasks/${id}`, { method: 'DELETE' })
    items.value = items.value.filter(t => t.id !== id)
  }

  async function search(q: string) {
    loading.value = true
    error.value = null
    try {
      const res = await api<{ data: Task[] }>('/search', { params: { q } })
      items.value = [...unwrap(res)]
    } catch (e: any) {
      error.value = e?.data?.message || e?.message || 'Search failed'
    } finally {
      loading.value = false
    }
  }

  async function reorder(
    orders: { id: number; position: number }[],
    date: string
  ) {
    await api('/tasks/reorder', {
      method: 'POST',
      body: { date, orders },
    })
  }

  function resequence() {
    items.value = items.value.map((t, i) => ({ ...t, position: i + 1 }))
  }

  async function move(id: number, dir: -1 | 1, date: string) {
    const idx = items.value.findIndex(t => t.id === id)
    if (idx < 0) return

    const newIdx = idx + dir
    if (newIdx < 0 || newIdx >= items.value.length) return

    const copy = [...items.value]
    const moved = copy.splice(idx, 1)[0]
    if (!moved) return
    copy.splice(newIdx, 0, moved)
    items.value = copy
    resequence()

    await reorder(
      items.value.map(t => ({ id: t.id, position: t.position })),
      date
    )
  }


  return { items, loading, error, fetchByDate, create, toggle, update, remove, search, reorder, move }
})