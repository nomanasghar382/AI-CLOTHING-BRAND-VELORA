import { useCallback, useEffect, useMemo, useState } from 'react'
import { Link, useParams } from 'react-router-dom'
import ProductCard from '../../components/catalog/ProductCard'
import Pagination from '../../components/common/Pagination'
import Loader from '../../components/feedback/Loader'
import EmptyState from '../../components/feedback/EmptyState'
import ErrorState from '../../components/feedback/ErrorState'
import Seo from '../../components/system/Seo'
import { catalogService } from '../../services/catalogService'

const BRAND_COPY = {
  nike: 'Training, running, and street — the full Nike edit for gym-to-street guys.',
  adidas: 'Three stripes energy. Performance tops and lifestyle sneakers.',
  jordan: 'Court heat and street culture. Iconic silhouettes, modern fits.',
  gymshark: 'Lifting fits that actually look good outside the gym.',
  puma: 'Speed, style, and sport — from track to street.',
  'under-armour': 'Compression, training layers, and performance footwear.',
  'new-balance': 'Clean runners and everyday sport style.',
}

export default function BrandShopPage() {
  const { slug } = useParams()
  const [data, setData] = useState({ items: [], meta: null })
  const [loading, setLoading] = useState(true)
  const [error, setError] = useState(false)
  const [page, setPage] = useState(1)
  const brandName = useMemo(() => slug.split('-').map((part) => part.charAt(0).toUpperCase() + part.slice(1)).join(' '), [slug])
  const heroImage = data.items[0]?.images?.[0]?.url

  const load = useCallback(() => {
    setLoading(true)
    setError(false)
    return catalogService.products({ gender: 'men', brand: slug, per_page: 24, sort: 'popular', page })
      .then(({ data: response }) => setData(response.data))
      .catch(() => setError(true))
      .finally(() => setLoading(false))
  }, [slug, page])

  useEffect(() => { load() }, [load])

  return (
    <>
      <Seo title={`${brandName} — Men's Sport`} description={BRAND_COPY[slug] || `Shop ${brandName} training and streetwear at VELORA.`} />
      <section
        className="retail-brand-hero"
        style={heroImage ? { backgroundImage: `linear-gradient(90deg, rgba(0,0,0,.78), rgba(0,0,0,.45)), url(${heroImage})` } : undefined}
      >
        <div className="container py-5">
          <p className="retail-eyebrow">BRAND SHOP</p>
          <h1 className="retail-heading display-5 mb-2">{brandName}</h1>
          <p className="retail-brand-copy mb-4">{BRAND_COPY[slug] || `Every ${brandName} training fit ships with matching kicks.`}</p>
          <div className="d-flex flex-wrap gap-2">
            <Link className="btn btn-retail-primary" to={`/catalog?brand=${slug}&line=footwear`}>Shop shoes</Link>
            <Link className="btn btn-retail-secondary" to={`/catalog?brand=${slug}&line=apparel`}>Shop clothing</Link>
          </div>
        </div>
      </section>
      <section className="container py-4 py-lg-5">
        {loading ? <Loader label={`Loading ${brandName}...`} /> : error ? <ErrorState onRetry={load} /> : data.items.length ? (
          <>
            <div className="row g-3">
              {data.items.map((product) => (
                <div className="col-6 col-md-4 col-lg-3" key={product.id}>
                  <ProductCard product={product} />
                </div>
              ))}
            </div>
            {data.meta?.last_page > 1 && (
              <div className="mt-4 d-flex justify-content-center">
                <Pagination page={data.meta.current_page} pageCount={data.meta.last_page} onPageChange={setPage} />
              </div>
            )}
          </>
        ) : (
          <EmptyState title={`No ${brandName} products yet.`} message="Run php artisan velora:reset-sport-catalog with PHP 8.2+." />
        )}
      </section>
    </>
  )
}
