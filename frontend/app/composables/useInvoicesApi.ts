import type { UseFetchOptions } from 'nuxt/app'

export interface Invoice {
  id: string
  number: string
  supplier_name: string
  net_amount: number
  vat_amount: number
  gross_amount: number
  currency: string
  issue_date: string
  due_date: string
  status: 'pending' | 'approved' | 'rejected'
}

export interface PaginationMeta {
  current_page: number
  last_page: number
  total: number
  per_page: number
}

export interface InvoicesResponse {
  data: Invoice[]
  meta: PaginationMeta
}

export interface InvoiceDetailResponse {
  data: Invoice
}

export const useInvoicesApi = () => {
  return {
    getInvoices: (query?: any, opts?: UseFetchOptions<InvoicesResponse>) => {
      return useApiFetch<InvoicesResponse>('/invoices', {
        query,
        ...opts
      })
    },
    
    getInvoice: (id: string, opts?: UseFetchOptions<InvoiceDetailResponse>) => {
      return useApiFetch<InvoiceDetailResponse>(`/invoices/${id}`, opts)
    },

    createInvoice: (payload: any) => {
      const config = useRuntimeConfig()
      const baseURL = import.meta.server ? config.apiLocal : config.public.apiBase
      
      return $fetch<InvoiceDetailResponse>('/invoices', {
        baseURL,
        method: 'POST',
        body: payload
      })
    },

    updateInvoice: (id: string, payload: Partial<Invoice>) => {
      const config = useRuntimeConfig()
      const baseURL = import.meta.server ? config.apiLocal : config.public.apiBase
      
      return $fetch<InvoiceDetailResponse>(`/invoices/${id}`, {
        baseURL,
        method: 'PUT',
        body: payload
      })
    }
  }
}
