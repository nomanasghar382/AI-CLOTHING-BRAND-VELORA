import { memo, useCallback, useEffect, useState } from 'react'
import { useSearchParams } from 'react-router-dom'
import Pagination from '../../components/common/Pagination'
import InstantSearchBox from '../../components/catalog/InstantSearchBox'
import CatalogFilterPanel from '../../components/catalog/CatalogFilterPanel'
import ProductCard from '../../components/catalog/ProductCard'
import Loader from '../../components/feedback/Loader'
import EmptyState from '../../components/feedback/EmptyState'
import ErrorState from '../../components/feedback/ErrorState'
import Seo from '../../components/system/Seo'
import { catalogService } from '../../services/catalogService'
import { searchService } from '../../services/searchService'

function pageTitle(params) {
  if (params.get('sale') === '1') return 'Sale'
  if (params.get('line') === 'footwear') return 'Shoes'
  if (params.get('line') === 'apparel') return 'Clothing'
  if (params.get('new') === '1') return 'New arrivals'
  if (params.get('featured') === '1') return 'Featured'
  if (params.get('brand')) return params.get('brand').split('-').map((p) => p.charAt(0).toUpperCase() + p.slice(1)).join(' ')
  return "Men's Sport"
}

function CatalogPage() {
  const [params, setParams] = useSearchParams()
  const [data, setData] = useState({ items: [], meta: null })
  const [filters, setFilters] = useState(null)
  const [loading, setLoading] = useState(true)
  const [error, setError] = useState(false)
  const query = params.get('q') || ''

  useEffect(() => {
    if (!params.get('gender')) {
      const next = new URLSearchParams(params)
      next.set('gender', 'men')
      setParams(next, { replace: true })
    }
  }, [params, setParams])

  useEffect(() => {
    catalogService.filters().then(({ data }) => setFilters(data.data)).catch(() => setFilters(null))
  }, [])

  const loadCatalog = useCallback(() => {
    if (!params.get('gender')) return Promise.resolve()
    setLoading(true)
    setError(false)
    const catalogParams = { ...Object.fromEntries(params), gender: 'men' }
    const request = query ? searchService.search(catalogParams) : catalogService.products(catalogParams)

    return request
      .then(({ data: response }) => setData(response.data))
      .catch(() => setError(true))
      .finally(() => setLoading(false))
  }, [params, query])

  useEffect(() => {
    loadCatalog()
  }, [loadCatalog])

  const update = useCallback((key, value) => {
    const next = new URLSearchParams(params)
    if (value) next.set(key, value)
    else next.delete(key)
    if (key !== 'page') next.delete('page')
    next.set('gender', 'men')
    setParams(next)
  }, [params, setParams])

  const clearFilters = useCallback(() => {
    setParams({ gender: 'men' })
  }, [setParams])

  const title = pageTitle(params)

  return (
    <>
      <Seo title={`Shop ${title}`} description="Nike, Adidas, Gymshark and more — training fits with matching sneakers." />
      <section className="container py-4 py-lg-5">
        <div className="retail-plp-head mb-4">
          <div>
            <p className="retail-eyebrow mb-1">MEN</p>
            <h1 className="retail-heading h2 mb-1">{title}</h1>
            <p className="text-slate-300 mb-0">{data.meta?.total ? `${data.meta.total} products` : 'Training + streetwear'}</p>
          </div>
          <div className="retail-plp-tools">
            <div className="catalog-search" style={{ maxWidth: '18rem' }}>
              <InstantSearchBox initial={query} onSubmit={(term) => update('q', term)} />
            </div>
            <select className="form-select velora-input retail-sort" value={params.get('sort') || 'newest'} onChange={(e) => update('sort', e.target.value)} aria-label="Sort products">
              <option value="newest">Newest</option>
              <option value="popular">Popular</option>
              <option value="price_asc">Price: low to high</option>
              <option value="price_desc">Price: high to low</option>
            </select>
          </div>
        </div>

        <div className="row g-4">
          {filters && (
            <div className="col-lg-3 d-none d-lg-block">
              <CatalogFilterPanel filters={filters} params={params} onChange={update} onClear={clearFilters} />
            </div>
          )}
          <div className={filters ? 'col-lg-9' : 'col-12'}>
            {loading ? <Loader label="Loading products..." /> : error ? <ErrorState onRetry={loadCatalog} /> : data.items.length ? (
              <>
                <div className="row g-3">
                  {data.items.map((product) => (
                    <div className="col-6 col-md-4 col-xl-3" key={product.id}>
                      <ProductCard product={product} />
                    </div>
                  ))}
                </div>
                <div className="mt-4 d-flex justify-content-center">
                  <Pagination page={data.meta.current_page} pageCount={data.meta.last_page} onPageChange={(page) => update('page', page)} />
                </div>
              </>
            ) : (
              <EmptyState title="No products yet." message="Your backend is probably not running. Use START-VELORA.ps1 on Windows." />
            )}
          </div>
        </div>
      </section>
    </>
  )
}

export default memo(CatalogPage)
