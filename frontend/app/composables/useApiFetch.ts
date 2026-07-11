import type { UseFetchOptions } from 'nuxt/app'

export const useApiFetch = <T>(request: string | (() => string), opts?: UseFetchOptions<T>) => {
  const config = useRuntimeConfig()
  
  return useFetch<T>(request, {
    baseURL: import.meta.server ? config.apiLocal : config.public.apiBase,
    ...opts
  } as any)
}
