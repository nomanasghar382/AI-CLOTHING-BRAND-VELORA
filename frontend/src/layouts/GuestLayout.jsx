import { Link, Outlet } from 'react-router-dom'

export default function GuestLayout() {
  return (
    <main className="auth-shell">
      <Link className="brand-mark auth-brand" to="/">VELORA<span>.</span></Link>
      <section className="auth-panel"><Outlet /></section>
    </main>
  )
}
