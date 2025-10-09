export default defineNuxtConfig({
  compatibilityDate: '2025-10-10',
  devtools: { enabled: true },
  css: ['~/assets/css/tailwind.css'],
  postcss: {
    plugins: {
      '@tailwindcss/postcss': {},
      autoprefixer: {},
    },
  },
  runtimeConfig: {
    public: {
      apiBase: 'http://localhost/api',
      webBase: 'http://localhost',
    }
  }
})
