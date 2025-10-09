export function useApi() {
  const { public: { apiBase, webBase } } = useRuntimeConfig()

  const client = $fetch.create({
    baseURL: apiBase as string,
    credentials: 'include',
    headers: {
      'X-Requested-With': 'XMLHttpRequest',
      'Accept': 'application/json'
    }
  })

  async function csrf() {
    await $fetch(`${webBase as string}/sanctum/csrf-cookie`, { 
      credentials: 'include',
      headers: { 'X-Requested-With': 'XMLHttpRequest', 'Accept': 'application/json' }
    })
  }

  return { client, csrf, webBase: webBase as string }
}