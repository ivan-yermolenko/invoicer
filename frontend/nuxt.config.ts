import tailwindcss from '@tailwindcss/vite';

// https://nuxt.com/docs/api/configuration/nuxt-config
export default defineNuxtConfig({
  compatibilityDate: '2024-11-01',
  future: {
    compatibilityVersion: 4
  },
  devtools: { enabled: true },
  routeRules: {
    '/': { redirect: '/invoices' }
  },
  runtimeConfig: {
    apiLocal: 'http://webserver/api', // Internal Docker URL for SSR
    public: {
      apiBase: 'http://localhost:8088/api' // External URL for browser
    }
  },
  css: ['~/assets/css/main.css'],
  vite: {
    plugins: [tailwindcss()]
  }
});
