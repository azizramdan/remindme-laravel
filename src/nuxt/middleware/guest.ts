import { useAuthStore } from "~/stores/auth"

export default defineNuxtRouteMiddleware((to, from) => {
  if (useAuthStore().isAuthenticated) {
    if (to.name == 'login' && to.query.redirectTo) {
      return navigateTo(to.query.redirectTo as string)
    }
    
    return navigateTo('/reminders')
  }
})
