import { useCallback, useState } from 'react'
import { getApiErrorMessage } from '../utils/getApiErrorMessage'

export default function useAsyncAction(fallbackMessage = 'Something went wrong. Please try again.') {
  const [busy, setBusy] = useState(false)
  const [error, setError] = useState('')

  const run = useCallback(async (action) => {
    setBusy(true)
    setError('')
    try {
      return await action()
    } catch (requestError) {
      setError(getApiErrorMessage(requestError, fallbackMessage))
      throw requestError
    } finally {
      setBusy(false)
    }
  }, [fallbackMessage])

  return { busy, error, setError, run }
}
