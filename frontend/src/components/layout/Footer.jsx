import { Link } from 'react-router-dom'

export default function Footer() {
  return (
    <footer className="site-footer">
      <div className="container py-5 d-flex flex-column flex-md-row gap-4 justify-content-between align-items-md-end">
        <div>
          <Link className="brand-mark" to="/">VELORA<span>.</span></Link>
          <p className="text-slate-300 mb-0 mt-2">Style, intelligently yours.</p>
        </div>
        <div className="text-md-end small text-slate-300">
          <p className="mb-2">AI-powered global modest fashion marketplace.</p>
          <p className="mb-0">© {new Date().getFullYear()} Velora. Foundation release.</p>
        </div>
      </div>
    </footer>
  )
}
