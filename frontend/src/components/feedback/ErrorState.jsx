import { FiAlertCircle } from 'react-icons/fi'
import RetryButton from '../system/RetryButton'

export default function ErrorState({
  title = 'Something went wrong',
  message = 'Please refresh the page or try again shortly.',
  onRetry,
  retryLabel = 'Try again',
}) {
  return (
    <div className="state-panel state-panel-error text-center" role="alert">
      <FiAlertCircle size={30} aria-hidden="true" />
      <h2 className="h5 mt-3">{title}</h2>
      <p className="mb-0 text-slate-300">{message}</p>
      {onRetry && <div className="mt-3"><RetryButton onRetry={onRetry} label={retryLabel} /></div>}
    </div>
  )
}
