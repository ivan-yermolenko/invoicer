import type { UseFetchOptions } from 'nuxt/app';

export const useApiFetch = <T>(request: string | (() => string), opts?: UseFetchOptions<T>) => {
  const config = useRuntimeConfig();
  const reqStr = typeof request === 'string' ? request : request();

  return useFetch<T>(request, {
    key: opts?.key || reqStr,
    baseURL: import.meta.server ? config.apiLocal : config.public.apiBase,
    ...opts
  } as any);
};
