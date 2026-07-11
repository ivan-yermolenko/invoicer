export const INVOICE_STATUS_CLASSES = {
  pending: 'bg-amber-100 text-amber-800 border-amber-200',
  approved: 'bg-emerald-100 text-emerald-800 border-emerald-200',
  rejected: 'bg-red-100 text-red-800 border-red-200'
} as const

export const INVOICE_STATUS_LABELS = {
  pending: 'В очікуванні',
  approved: 'Затверджено',
  rejected: 'Відхилено'
} as const

export const INVOICE_CURRENCIES = ['UAH', 'USD', 'EUR'] as const