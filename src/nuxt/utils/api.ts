import { defu } from 'defu'
import { stringify, parse } from 'qs'

export function $api(request: Parameters<typeof $fetch>[0], options?: Parameters<typeof $fetch>[1]): ReturnType<typeof $fetch> {
  const config = useRuntimeConfig()
  const token = useAuthStore().access_token

  const defaultHeaders: HeadersInit = {
    Accept: 'application/json',
  }

  if (token) {
    defaultHeaders['Authorization'] = `Bearer ${token}`
  }

  const defaultOptions: Parameters<typeof $fetch>[1] = {
    baseURL: config.public.apiBase,
    headers: defaultHeaders,
    onResponse: async (context) => {
      const headers: HeadersInit = {}

      Object.entries(context.options.headers || {}).forEach(([key, value]) => {
        headers[key] = value
      })

      if (context.response?.status === 401 && headers['X-Refresh-Access-Token'] != 'true' && headers['X-Retry-After-Refresh-Access-Token'] != 'true') {
        await useAuthStore().refreshAccessToken()

        headers['X-Retry-After-Refresh-Access-Token'] = 'true'
        headers['Authorization'] = `Bearer ${useAuthStore().access_token}`

        const options = context.options
        options.headers = headers
        options.onResponse = (ctx) => {
          Object.assign(context, ctx);
        }

        await $api(context.request, options as Parameters<typeof $fetch>[1])
      }
    },
  }

  // merge query from options because `params` is alias for `query` so `query` has higher priority
  const queryFromOptions = defu(options?.query || {}, options?.params || {})

  // remove params & query from options so they don't get proccessed by $fetch
  if (options) {
    options.params = undefined
    options.query = undefined
  }

  // create url from request
  // if request contains api base (like when retry request call), remove it
  const url = new URL(request.toString().replace(config.public.apiBase, ''), defaultOptions.baseURL)

  // parse query from request
  const queryFromRequest = parse(url.search, { ignoreQueryPrefix: true })

  // merge query, queryFromOptions has higher priority
  const queryMerged = defu(queryFromOptions, queryFromRequest)

  const queryString = stringify(queryMerged)

  // override request with query
  request = url.pathname + (queryString ? `?${queryString}` : '')

  return $fetch(request, defu(options || {}, defaultOptions))
}

export function $apiGet(request: Parameters<typeof $fetch>[0], options?: Parameters<typeof $fetch>[1]): ReturnType<typeof $fetch> {
  return $api(request, { method: 'GET', ...options })
}

export function $apiPost(request: Parameters<typeof $fetch>[0], options?: Parameters<typeof $fetch>[1]): ReturnType<typeof $fetch> {
  return $api(request, { method: 'POST', ...options })
}

export function $apiPut(request: Parameters<typeof $fetch>[0], options?: Parameters<typeof $fetch>[1]): ReturnType<typeof $fetch> {
  return $api(request, { method: 'PUT', ...options })
}

export function $apiDelete(request: Parameters<typeof $fetch>[0], options?: Parameters<typeof $fetch>[1]): ReturnType<typeof $fetch> {
  return $api(request, { method: 'DELETE', ...options })
}
