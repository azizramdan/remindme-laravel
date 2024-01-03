import { useAuthStore } from "~/stores/auth"

export default defineNuxtRouteMiddleware((to, from) => {
  const middleware = (Array.isArray(to.meta.middleware || []) ? to.meta.middleware || [] : [to.meta.middleware]) as string[]

  if (!middleware.includes('guest') && !middleware.includes('allow-guest') && !useAuthStore().isAuthenticated) {
    return navigateTo({
      path: '/login',
      query: {
        redirectTo: to.fullPath
      }
    })
  }
})
