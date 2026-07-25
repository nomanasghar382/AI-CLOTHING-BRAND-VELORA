import { VitePWA } from 'vite-plugin-pwa'
import { defineConfig } from 'vite'
import react from '@vitejs/plugin-react'

export default defineConfig({
  plugins: [
    react(),
    VitePWA({
      registerType: 'autoUpdate',
      includeAssets: ['favicon.svg', 'robots.txt', 'sitemap.xml'],
      manifest: {
        name: 'VELORA',
        short_name: 'VELORA',
        description: 'Style, intelligently yours.',
        theme_color: '#0B0B0F',
        background_color: '#0B0B0F',
        display: 'standalone',
        start_url: '/',
        icons: [
          { src: '/favicon.svg', sizes: '512x512', type: 'image/svg+xml', purpose: 'any maskable' },
        ],
        shortcuts: [
          { name: 'Catalog', url: '/catalog', description: 'Browse the edit' },
          { name: 'AI Stylist', url: '/ai', description: 'Personal styling' },
          { name: 'Cart', url: '/cart', description: 'Review your bag' },
          { name: 'Wishlist', url: '/wishlist', description: 'Saved pieces' },
        ],
      },
      workbox: {
        navigateFallback: '/offline',
        globPatterns: ['**/*.{js,css,html,ico,png,svg,woff2}'],
        runtimeCaching: [
          { urlPattern: /^https:\/\/fonts\.googleapis\.com\/.*/i, handler: 'StaleWhileRevalidate', options: { cacheName: 'velora-fonts' } },
          { urlPattern: /^https:\/\/fonts\.gstatic\.com\/.*/i, handler: 'CacheFirst', options: { cacheName: 'velora-font-files', expiration: { maxEntries: 20, maxAgeSeconds: 31536000 } } },
          { urlPattern: /^https:\/\/res\.cloudinary\.com\/.*/i, handler: 'CacheFirst', options: { cacheName: 'velora-cloudinary', expiration: { maxEntries: 120, maxAgeSeconds: 604800 } } },
          { urlPattern: /^https:\/\/images\.unsplash\.com\/.*/i, handler: 'CacheFirst', options: { cacheName: 'velora-images', expiration: { maxEntries: 200, maxAgeSeconds: 604800 } } },
          { urlPattern: /^https:\/\/plus\.unsplash\.com\/.*/i, handler: 'CacheFirst', options: { cacheName: 'velora-premium-images', expiration: { maxEntries: 120, maxAgeSeconds: 604800 } } },
          { urlPattern: /^https:\/\/images\.pexels\.com\/.*/i, handler: 'CacheFirst', options: { cacheName: 'velora-pexels-images', expiration: { maxEntries: 200, maxAgeSeconds: 604800 } } },
          { urlPattern: /\/api\/v1\/(products|categories|search|catalog\/filters).*/i, handler: 'NetworkFirst', options: { cacheName: 'velora-catalog-api', networkTimeoutSeconds: 5 } },
          { urlPattern: /\/api\/v1\/(cart|wishlist|orders|style\/conversations).*/i, handler: 'NetworkFirst', options: { cacheName: 'velora-account-api', networkTimeoutSeconds: 8 } },
        ],
      },
      devOptions: { enabled: false },
    }),
  ],
  build: {
    chunkSizeWarningLimit: 700,
    rollupOptions: {
      output: {
        manualChunks(id) {
          if (id.includes('node_modules/react-router') || id.includes('node_modules/react-dom') || id.includes('node_modules/react/')) return 'vendor'
          if (id.includes('node_modules/framer-motion')) return 'motion'
        },
      },
    },
  },
})
