export default function Loader({ label = 'Loading Velora...' }) {
  return (
    <div className="d-flex align-items-center gap-2 text-slate-300" role="status">
      <span className="spinner-border spinner-border-sm" aria-hidden="true" />
      <span>{label}</span>
    </div>
  )
}
