export default function Input({ label, error, id, className = '', ...props }) {
  const inputId = id || props.name

  return (
    <div className="mb-3">
      {label && <label className="form-label text-slate-200" htmlFor={inputId}>{label}</label>}
      <input id={inputId} className={`form-control velora-input ${error ? 'is-invalid' : ''} ${className}`} {...props} />
      {error && <div className="invalid-feedback">{error}</div>}
    </div>
  )
}
