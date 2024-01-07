// https://nuxt.com/docs/api/configuration/nuxt-config
export default defineNuxtConfig({
  devtools: { enabled: true },

  ssr: false,

  app: {
    head: {
      title: 'RemindMe',
    }
  },

  modules: [
    '@nuxtjs/tailwindcss',
    '@pinia/nuxt',
  ],

  runtimeConfig: {
    public: {
      apiBase: process.env.API_URL || 'http://localhost:8010/api',
      accessTokenExpiration: parseInt(process.env.ACCESS_TOKEN_EXPIRATION || '20'),
    }
  },

  tailwindcss: {
    exposeConfig: true,
    config: {
      plugins: [
        require('flowbite/plugin')
      ],
      content: [
        "./node_modules/flowbite/**/*.{js,ts}"
      ],
      theme: {
        extend: {
          colors: {
            nabitu: {
              '50': '#f1fcf4',
              '100': '#defae9',
              '200': '#bff3d2',
              '300': '#8ce9b0',
              '400': '#53d585',
              '500': '#2cbb64',
              '600': '#1e9b4f',
              '700': '#1b7a41',
              '800': '#195a33',
              '900': '#184f2f',
              '950': '#072c17',
            },
          }
        }
      }
    },
  }
})
