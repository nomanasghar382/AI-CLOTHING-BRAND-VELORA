import { FiX } from 'react-icons/fi'

export default function AdminModal({ title, children, onClose }) {
  return <div className="admin-modal-backdrop" role="presentation" onMouseDown={onClose}>
    <section className="admin-modal" role="dialog" aria-modal="true" aria-label={title} onMouseDown={(event) => event.stopPropagation()}>
      <header><h2>{title}</h2><button className="admin-icon-button" onClick={onClose} aria-label="Close"><FiX /></button></header>
      {children}
    </section>
  </div>
}
