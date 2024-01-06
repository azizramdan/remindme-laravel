import { useAuthStore, accessTokenCookieKey, refreshTokenCookieKey, userCookieKey } from "~/stores/auth";

export default defineNuxtPlugin(async () => {
  const store = useAuthStore()
  const accessToken = useCookie(accessTokenCookieKey)
  const refreshToken = useCookie(refreshTokenCookieKey)
  const user = useCookie(userCookieKey)
  const config = useRuntimeConfig()

  // Refresh access token 5 seconds before it expires
  setInterval(function () {
    if (accessToken.value && refreshToken.value && user.value) {
      store.refreshAccessToken()
    }
  }, 1000 * (config.public.accessTokenExpiration - 5))
})
