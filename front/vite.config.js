import { fileURLToPath, URL } from 'node:url'

import { defineConfig } from 'vite'
import vue from '@vitejs/plugin-vue'
import Components from 'unplugin-vue-components/vite'
import { PrimeVueResolver } from 'unplugin-vue-components/resolvers'

// https://vite.dev/config/
export default defineConfig(async () => {
  const plugins = [
    vue(),
    Components({
      resolvers: [PrimeVueResolver()],
    }),
  ]

  // Use dynamic import so the module is never evaluated unless explicitly enabled.
  if (process.env.ENABLE_VUE_DEVTOOLS === 'true') {
    const { default: vueDevTools } = await import('vite-plugin-vue-devtools')
    plugins.splice(1, 0, vueDevTools())
  }

  return {
    plugins,
    envPrefix: ['VITE_', 'API_'],
    resolve: {
      alias: {
        '@': fileURLToPath(new URL('./src', import.meta.url))
      },
    },
  }
})
