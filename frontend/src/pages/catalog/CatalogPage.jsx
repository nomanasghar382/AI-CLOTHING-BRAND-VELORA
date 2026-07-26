import { memo, useCallback, useEffect, useState } from 'react'
import { useSearchParams } from 'react-router-dom'
import Breadcrumb from '../../components/common/Breadcrumb'
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

function CatalogPage() {
  const [params, setParams] = useSearchParams()
  const [data, setData] = useState({ items: [], meta: null })
  const [filters, setFilters] = useState({ brands: [], colors: [], sizes: [], materials: [], fabrics: [], men_categories: [], catalog_lines: [], occasions: [], price_range: { min: 0, max: 0 } })
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

  const loadCatalog = useCallback(() => {
    if (!params.get('gender')) return Promise.resolve()
    setLoading(true)
    setError(false)
    const catalogParams = { ...Object.fromEntries(params), gender: 'men' }
    const request = query
      ? searchService.search(catalogParams)
      : catalogService.products(catalogParams)

    return request
      .then(({ data: response }) => setData(response.data))
      .catch(() => setError(true))
      .finally(() => setLoading(false))
  }, [params, query])

  useEffect(() => {
    catalogService.filters().then(({ data: response }) => setFilters(response.data)).catch(() => {})
  }, [])

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
    const next = new URLSearchParams(query ? { q: query, gender: 'men' } : { gender: 'men' })
    setParams(next)
  }, [query, setParams])

  const activeLine = params.get('line') || ''
  const setLine = useCallback((line) => {
    const next = new URLSearchParams(params)
    if (line) next.set('line', line)
    else next.delete('line')
    next.set('gender', 'men')
    next.delete('page')
    setParams(next)
  }, [params, setParams])

  const heading = activeLine === 'footwear'
    ? "Men's sneakers & kicks."
    : activeLine === 'apparel'
      ? "Men's sportswear drops."
      : "Gen Z men's sportswear."

  return (
    <>
      <Seo title="Sportswear Catalog" description="Shop Nike, Adidas, Puma, Jordan and more — men's sportswear built for Gen Z athletes." />
      <section className="container py-4 py-lg-5">
        <Breadcrumb items={[{ label: 'Home', to: '/' }, { label: 'Sportswear' }]} />
        <div className="d-flex flex-column flex-lg-row align-items-lg-end justify-content-between gap-3 mb-3">
          <div>
            <p className="eyebrow">VELORA SPORT</p>
            <h1 className="h2 mb-0">{heading}</h1>
            <p className="text-slate-300 small mb-0 mt-2">Nike · Adidas · Puma · Jordan · Gymshark &amp; 40+ brands · Ages 16–35</p>
          </div>
          <div className="catalog-search">
            <InstantSearchBox initial={query} onSubmit={(term) => update('q', term)} />
          </div>
        </div>
        <div className="catalog-gender-tabs mb-4" role="tablist" aria-label="Shop by line">
          <button type="button" role="tab" aria-selected={activeLine === ''} className={activeLine === '' ? 'active' : ''} onClick={() => setLine('')}>All sport</button>
          <button type="button" role="tab" aria-selected={activeLine === 'apparel'} className={activeLine === 'apparel' ? 'active' : ''} onClick={() => setLine('apparel')}>Apparel</button>
          <button type="button" role="tab" aria-selected={activeLine === 'footwear'} className={activeLine === 'footwear' ? 'active' : ''} onClick={() => setLine('footwear')}>Sneakers</button>
        </div>
        <div className="row g-4">
          <aside className="col-lg-3">
            <CatalogFilterPanel filters={filters} params={params} onChange={update} onClear={clearFilters} />
          </aside>
          <div className="col-lg-9">
            {loading ? <Loader label="Loading sport drops..." /> : error ? <ErrorState onRetry={loadCatalog} /> : data.items.length ? (
              <>
                <div className="row g-3">
                  {data.items.map((product) => (
                    <div className="col-6 col-md-4" key={product.id}>
                      <ProductCard product={product} />
                    </div>
                  ))}
                </div>
                <div className="mt-4 d-flex justify-content-center">
                  <Pagination page={data.meta.current_page} pageCount={data.meta.last_page} onPageChange={(page) => update('page', page)} />
                </div>
              </>
            ) : <EmptyState title="No sport pieces matched." message="Try voice search, image search, or adjust your filters." />}
          </div>
        </div>
      </section>
    </>
  )
}

export default memo(CatalogPage)
