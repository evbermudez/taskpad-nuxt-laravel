import { defineStore } from 'pinia'

export const useSearchStore = defineStore('search', {
  state: () => ({
    query: ''
  }),
  actions: {
    setQuery(q: string) {
      this.query = q.trim()
    },
    clear() {
      this.query = ''
    }
  }
})