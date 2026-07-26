import { useNotifications } from '../../hooks/useNotifications'

const variants = {
  info: 'alert-info',
  success: 'alert-success',
  warning: 'alert-warning',
  danger: 'alert-danger',
}

export default function ToastStack() {
  const { toasts, dismissToast } = useNotifications()
  if (!toasts.length) return null

  return (
    <div className="toast-stack" aria-live="polite" aria-atomic="true">
      {toasts.map((toast) => (
        <div className={`alert ${variants[toast.variant] || variants.info} shadow`} key={toast.id} role="status">
          <span>{toast.message}</span>
          <button type="button" className="btn-close ms-3" aria-label="Dismiss notification" onClick={() => dismissToast(toast.id)} />
        </div>
      ))}
    </div>
  )
}
