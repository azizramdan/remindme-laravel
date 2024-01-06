import { defineStore } from 'pinia'

export type User = {
  id: number
  name: string
  email: string
}

type State = {
  access_token: null|string
  refresh_token: null|string
  user: null|User
}

export const accessTokenCookieKey = 'remindme-access-token'
export const refreshTokenCookieKey = 'remindme-refresh-token'
export const userCookieKey = 'remindme-user'

export const useAuthStore = defineStore({
  id: 'auth-store',
  state: (): State => {
    return {
      access_token: null,
      refresh_token: null,
      user: null,
    }
  },
  actions: {
    setAccessToken(token: string) {
      this.access_token = token

      const cookie = useCookie(accessTokenCookieKey, {sameSite: true})
      cookie.value = token
    },
    setRefreshToken(token: string) {
      this.refresh_token = token

      const cookie = useCookie(refreshTokenCookieKey, {sameSite: true})
      cookie.value = token
    },
    removeAccessToken() {
      this.access_token = null
      
      const cookie = useCookie(accessTokenCookieKey, {sameSite: true})
      cookie.value = null
    },
    removeRefreshToken() {
      this.refresh_token = null
      
      const cookie = useCookie(refreshTokenCookieKey, {sameSite: true})
      cookie.value = null
    },
    setUser(user: User) {
      this.user = user

      const cookie = useCookie(userCookieKey, {sameSite: true})
      cookie.value = JSON.stringify(user)
    },
    removeUser() {
      this.user = null

      const cookie = useCookie(userCookieKey, {sameSite: true})
      cookie.value = null
    },
    logout() {
      this.removeAccessToken()
      this.removeRefreshToken()
      this.removeUser()
    },
    async login(email: string, password: string) {
      const { data, error } = await useApiPost('session', {
        body: {
          email,
          password,
        },
        pick: ['ok', 'data', 'msg']
      })

      if (!data.value?.ok) {
        return {
          success: false,
          message: error.value?.data?.msg || 'Something went wrong'
        }
      }

      this.setAccessToken(data.value.data.access_token)
      this.setRefreshToken(data.value.data.refresh_token)
      this.setUser(data.value.data.user)

      return {
        success: true,
      }
    },
    async refreshAccessToken() {
      const { data, error } = await useApiPut('session', {
        headers: {
          Authorization: `Bearer ${this.refresh_token}`
        },
        pick: ['ok', 'data', 'msg'],
      })

      if (!data.value?.ok) {
        if (error.value?.statusCode === 401) {
          alert('Session expired, please login again')
          this.logout()
          
          return navigateTo('/login')
        }
        
        alert(error.value?.data?.msg || 'Something went wrong')
        return
      }

      this.setAccessToken(data.value.data.access_token)
    },
  },
  getters: {
    isAuthenticated: state => Boolean(state.user),
  },
})