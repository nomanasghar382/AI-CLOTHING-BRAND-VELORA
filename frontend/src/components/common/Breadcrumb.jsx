import { Link } from 'react-router-dom'

export default function Breadcrumb({ items = [] }) {
  return <nav aria-label="Breadcrumb"><ol className="breadcrumb small mb-4">{items.map((item, index) => <li className={`breadcrumb-item ${index === items.length - 1 ? 'active text-slate-300' : ''}`} key={item.label}>{item.to && index !== items.length - 1 ? <Link to={item.to}>{item.label}</Link> : item.label}</li>)}</ol></nav>
}
