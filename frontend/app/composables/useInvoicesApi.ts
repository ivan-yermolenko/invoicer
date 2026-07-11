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

export interface InvoicesResponse {
  data: Invoice[]
}

export const useInvoicesApi = () => {
  return {
    getInvoices: (opts?: UseFetchOptions<InvoicesResponse>) => {
      return useApiFetch<InvoicesResponse>('/invoices', opts)
    }
  }
}
