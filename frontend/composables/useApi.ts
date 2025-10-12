import { useRuntimeConfig, useCookie } from 'nuxt/app'
import { $fetch } from 'ofetch'

export function useApi() {
  const { public: { apiBase, webBase } } = useRuntimeConfig()

  const makeClient = (baseURL: string) => $fetch.create({
    baseURL,
    credentials: 'include',
    headers: { 'X-Requested-With': 'XMLHttpRequest', 'Accept': 'application/json' },
    onRequest({ options }) {
      if (!process.client) return
      const raw = useCookie<string | null>('XSRF-TOKEN').value
      if (!raw) return
      const token = decodeURIComponent(raw)
      const headers = new Headers(options.headers)
      headers.set('X-Requested-With', 'XMLHttpRequest')
      headers.set('Accept', 'application/json')
      headers.set('X-XSRF-TOKEN', token)
      options.headers = headers
      options.credentials = 'include'
    }
  })

  const api = makeClient(apiBase as string)    // for /api/*
  const web = makeClient(webBase as string)    // for /login, /logout, /sanctum/*

  async function csrf() {
    await web('/sanctum/csrf-cookie', { method: 'GET' })
  }

  async function login(email: string, password: string) {
    await csrf()
    return web('/login', { method: 'POST', body: { email, password } })
  }

  async function logout() {
    return web('/logout', { method: 'POST' })
  }

  return { api, web, csrf, login, logout }
}