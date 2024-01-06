import type { UseFetchOptions } from 'nuxt/app'
import { defu } from 'defu'
import { useAuthStore } from '~/stores/auth'

export const useApi = <T>(url: string, options: UseFetchOptions<T> = {}) => {
  const config = useRuntimeConfig()
  const token = useAuthStore().access_token
  const defaultHeaders: HeadersInit = {
    Accept: 'application/json',
  }

  if (token) {
    defaultHeaders['Authorization'] = `Bearer ${token}`
  }

  const removeEmptyQuery = (obj: typeof options.query) => {
    obj && (Object.keys(obj) as Array<keyof typeof options.query>).forEach(key => {
      const value = isRef(obj[key]) ? (obj[key] as { value?: any })?.value : obj[key]

      if (value === null || value === undefined) {
        delete obj[key]
      }
    })
  }

  removeEmptyQuery(options.query)
  removeEmptyQuery(options.params)

  const defaults: UseFetchOptions<T> = {
    baseURL: config.public.apiBase,
    retry: false,
    key: url,
    watch: false,
    headers: {
      ...defaultHeaders,
      ...options.headers || {},
    },
  }

  // for nice deep defaults, please use unjs/defu
  const params = defu(options, defaults)

  return useFetch(url, params)
}

export const useApiGet: typeof useApi = (url, options = {}) => {
  options.method = 'GET'

  return useApi(url, options)
}

export const useApiPost: typeof useApi = (url, options = {}) => {
  options.method = 'POST'

  return useApi(url, options)
}

export const useApiPut: typeof useApi = (url, options = {}) => {
  options.method = 'PUT'

  return useApi(url, options)
}

export const useApiDelete: typeof useApi = (url, options = {}) => {
  options.method = 'DELETE'

  return useApi(url, options)
}