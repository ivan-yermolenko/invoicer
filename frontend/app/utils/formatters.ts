export const formatCurrency = (amount: number, currency: string) => {
  const formattedNumber = new Intl.NumberFormat('uk-UA', { 
    style: 'decimal',
    minimumFractionDigits: 2,
    maximumFractionDigits: 2
  }).format(amount)
  
  return `${formattedNumber} ${currency}`
}

export const formatDate = (dateString: string) => {
  return new Intl.DateTimeFormat('uk-UA', { 
    year: 'numeric', 
    month: 'short', 
    day: 'numeric',
    timeZone: 'UTC'
  }).format(new Date(dateString))
}
