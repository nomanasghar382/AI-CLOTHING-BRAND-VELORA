import { FiMinus, FiPlus, FiTrash2 } from 'react-icons/fi'

const money = (value, currency = 'USD') => new Intl.NumberFormat('en-US', { style: 'currency', currency }).format(Number(value))

export default function CartItemCard({ item, currency, onQuantityChange, onRemove, busy }) {
  return <article className="velora-card cart-item-card p-3">
    <div className="d-flex align-items-start justify-content-between gap-3">
      <div><p className="eyebrow mb-1">{item.sku}</p><h2 className="h5 mb-1">{item.name}</h2><p className="text-slate-300 mb-0">{money(item.unit_price, currency)} each</p></div>
      <button className="btn btn-velora-ghost p-2" type="button" aria-label={`Remove ${item.name}`} disabled={busy} onClick={() => onRemove(item.id)}><FiTrash2 /></button>
    </div>
    <div className="d-flex align-items-center justify-content-between mt-3">
      <div className="quantity-control"><button type="button" aria-label={`Decrease ${item.name} quantity`} disabled={busy || item.quantity <= 1} onClick={() => onQuantityChange(item.id, item.quantity - 1)}><FiMinus /></button><span>{item.quantity}</span><button type="button" aria-label={`Increase ${item.name} quantity`} disabled={busy} onClick={() => onQuantityChange(item.id, item.quantity + 1)}><FiPlus /></button></div>
      <strong>{money(item.line_total, currency)}</strong>
    </div>
  </article>
}
