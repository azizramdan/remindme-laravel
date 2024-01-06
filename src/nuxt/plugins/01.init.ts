import { useAuthStore, accessTokenCookieKey, refreshTokenCookieKey, userCookieKey, type User } from "~/stores/auth";

export default defineNuxtPlugin(async () => {
  const store = useAuthStore()
  const accessToken = useCookie(accessTokenCookieKey)
  const refreshToken = useCookie(refreshTokenCookieKey)
  const user = useCookie(userCookieKey)

  if (accessToken.value && refreshToken.value && user.value) {
    store.setAccessToken(accessToken.value)
    store.setRefreshToken(refreshToken.value)
    store.setUser(user.value as unknown as User)
  }
})
