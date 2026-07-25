import { FiAlertCircle } from 'react-icons/fi'

export default function ErrorState({ title = 'Something went wrong', message = 'Please refresh the page or try again shortly.' }) {
  return (
    <div className="state-panel state-panel-error text-center" role="alert">
      <FiAlertCircle size={30} aria-hidden="true" />
      <h2 className="h5 mt-3">{title}</h2>
      <p className="mb-0 text-slate-300">{message}</p>
    </div>
  )
}
