export function getApiErrorMessage(error, fallback = 'Something went wrong. Please try again.') {
  const validation = error?.response?.data?.errors
  if (validation && typeof validation === 'object') {
    const messages = Object.values(validation).flat().filter(Boolean)
    if (messages.length) return messages.join(' ')
  }
  return error?.response?.data?.message || error?.userMessage || fallback
}
