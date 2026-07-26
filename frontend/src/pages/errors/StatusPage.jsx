import { Link } from 'react-router-dom'

export default function StatusPage({ code, title, description }) {
  return <section className="container py-5 py-lg-6 text-center"><p className="eyebrow">{code}</p><h1 className="display-hero fs-1">{title}</h1><p className="hero-copy mx-auto">{description}</p><Link className="btn btn-velora-primary" to="/">Return home</Link></section>
}
