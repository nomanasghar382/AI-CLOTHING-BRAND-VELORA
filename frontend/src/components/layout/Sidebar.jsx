import { NavLink } from 'react-router-dom'

export default function Sidebar({ items = [] }) {
  return <aside className="velora-card p-3" aria-label="Section navigation">{items.map((item) => <NavLink className="nav-link-velora d-block py-2" key={item.to} to={item.to}>{item.label}</NavLink>)}</aside>
}
