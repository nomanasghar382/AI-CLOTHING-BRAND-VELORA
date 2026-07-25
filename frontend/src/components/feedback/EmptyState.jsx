import { FiInbox } from 'react-icons/fi'

export default function EmptyState({ title = 'Nothing here yet', message = 'This space is ready for a future Velora module.' }) {
  return (
    <div className="state-panel text-center">
      <FiInbox size={30} aria-hidden="true" />
      <h2 className="h5 mt-3">{title}</h2>
      <p className="mb-0 text-slate-300">{message}</p>
    </div>
  )
}
