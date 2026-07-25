export default function Toast({ message, onClose, variant = 'info' }) {
  if (!message) return null
  return <div className={`alert alert-${variant} position-fixed bottom-0 end-0 m-4 shadow`} role="status">{message}<button type="button" className="btn-close ms-3" aria-label="Close" onClick={onClose} /></div>
}
