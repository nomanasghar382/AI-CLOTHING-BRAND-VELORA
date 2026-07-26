import { useEffect, useState } from 'react'
import { API_BASE_URL } from '../../config/api'

export default function BackendStatusBanner() {
  const [status, setStatus] = useState('checking')

  useEffect(() => {
    fetch(`${API_BASE_URL}/products?per_page=1`)
      .then((response) => {
        if (!response.ok) throw new Error('bad response')
        return response.json()
      })
      .then((data) => {
        const count = data?.data?.meta?.total ?? 0
        setStatus(count > 0 ? 'ok' : 'empty')
      })
      .catch(() => setStatus('down'))
  }, [])

  if (status === 'checking' || status === 'ok') return null

  return (
    <div className="backend-status-banner" role="alert">
      <div className="container py-3">
        {status === 'down' ? (
          <>
            <strong>Backend is not running.</strong> Product photos and shop will not work until you start PHP 8.2+.
            <span className="d-block small mt-1">Windows: right-click <code>START-VELORA.ps1</code> → Run with PowerShell. Or fix PHP — you need 8.2+, not XAMPP 8.0.</span>
          </>
        ) : (
          <>
            <strong>Catalog is empty.</strong> Run <code>php artisan velora:reset-sport-catalog</code> in the backend folder.
          </>
        )}
      </div>
    </div>
  )
}
