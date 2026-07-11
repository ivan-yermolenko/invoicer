export const formatCurrency = (amount: number, currency: string) => {
  return new Intl.NumberFormat('uk-UA', { style: 'currency', currency }).format(amount)
}

export const formatDate = (dateString: string) => {
  return new Intl.DateTimeFormat('uk-UA', { 
    year: 'numeric', 
    month: 'short', 
    day: 'numeric',
    timeZone: 'UTC'
  }).format(new Date(dateString))
}
