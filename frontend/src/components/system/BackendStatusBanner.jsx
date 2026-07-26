import { useEffect, useState } from 'react'
import { getCatalogMode } from '../../services/catalogMode'
import { catalogService } from '../../services/catalogService'

export default function BackendStatusBanner() {
  const [mode, setMode] = useState(getCatalogMode())

  useEffect(() => {
    catalogService.products({ per_page: 1, gender: 'men' }).catch(() => {})
    const sync = () => setMode(getCatalogMode())
    window.addEventListener('velora:catalog-mode', sync)
    const timer = window.setInterval(sync, 1500)
    return () => {
      window.removeEventListener('velora:catalog-mode', sync)
      window.clearInterval(timer)
    }
  }, [])

  if (mode === 'checking' || mode === 'live') return null

  return (
    <div className="backend-status-banner demo-mode-banner" role="status">
      <div className="container py-2">
        <strong>Demo catalog active.</strong> Products and photos load from the frontend so you can browse now.
        <span className="d-block small mt-1">
          For the full store (bag, checkout, 2,000 SKUs): run <code>START-VELORA.ps1</code> with PHP 8.2+ — not XAMPP 8.0.
        </span>
      </div>
    </div>
  )
}
