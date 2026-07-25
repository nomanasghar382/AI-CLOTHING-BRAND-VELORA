import Button from '../common/Button'

export default function RetryButton({ onRetry, label = 'Try again' }) {
  return <Button onClick={onRetry}>{label}</Button>
}
