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
import { CATALOG_TABS, GYM_MOMENTS, NICHE_TAGLINE, PRIORITY_BRANDS } from '../../constants/gymToStreet'
import { catalogService } from '../../services/catalogService'
import { searchService } from '../../services/searchService'

function CatalogPage() {
  const [params, setParams] = useSearchParams()
  const [data, setData] = useState({ items: [], meta: null })
  const [filters, setFilters] = useState({ brands: [], colors: [], sizes: [], materials: [], men_categories: [], price_range: { min: 0, max: 0 } })
  const [loading, setLoading] = useState(true)
  const [error, setError] = useState(false)
  const query = params.get('q') || ''
  const activeMoment = params.get('moment') || ''
  const activeLine = params.get('line') || ''

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
    const request = query ? searchService.search(catalogParams) : catalogService.products(catalogParams)

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
    setParams(new URLSearchParams(query ? { q: query, gender: 'men' } : { gender: 'men' }))
  }, [query, setParams])

  const setMoment = useCallback((moment) => {
    const next = new URLSearchParams(params)
    if (moment) next.set('moment', moment)
    else next.delete('moment')
    next.delete('line')
    next.delete('category')
    next.delete('page')
    next.set('gender', 'men')
    setParams(next)
  }, [params, setParams])

  const setLine = useCallback((line) => {
    const next = new URLSearchParams(params)
    if (line) next.set('line', line)
    else next.delete('line')
    next.delete('moment')
    next.delete('page')
    next.set('gender', 'men')
    setParams(next)
  }, [params, setParams])

  const momentCopy = GYM_MOMENTS.find((item) => item.id === activeMoment)
  const heading = momentCopy
    ? momentCopy.label
    : activeLine === 'footwear'
      ? 'Street kicks'
      : activeLine === 'apparel'
        ? 'Training gear'
        : 'Gym-to-street catalog'

  return (
    <>
      <Seo title="Gym-to-Street Catalog" description="Training fits and matching street sneakers from Nike, Gymshark, Adidas and more." />
      <section className="container py-4 py-lg-5">
        <Breadcrumb items={[{ label: 'Home', to: '/' }, { label: NICHE_TAGLINE }]} />
        <div className="d-flex flex-column flex-lg-row align-items-lg-end justify-content-between gap-3 mb-3">
          <div>
            <p className="eyebrow">VELORA {NICHE_TAGLINE.toUpperCase()}</p>
            <h1 className="h2 mb-0">{heading}</h1>
            <p className="text-slate-300 small mb-0 mt-2">
              {momentCopy?.copy || `${PRIORITY_BRANDS.join(' · ')} · Every fit ships with matching kicks`}
            </p>
          </div>
          <div className="catalog-search">
            <InstantSearchBox initial={query} onSubmit={(term) => update('q', term)} />
          </div>
        </div>

        <div className="catalog-gender-tabs mb-3" role="tablist" aria-label="Gym moments">
          <button type="button" role="tab" aria-selected={activeMoment === ''} className={activeMoment === '' ? 'active' : ''} onClick={() => setMoment('')}>All</button>
          {GYM_MOMENTS.map((moment) => (
            <button key={moment.id} type="button" role="tab" aria-selected={activeMoment === moment.id} className={activeMoment === moment.id ? 'active' : ''} onClick={() => setMoment(moment.id)}>{moment.label}</button>
          ))}
        </div>

        <div className="catalog-gender-tabs mb-4" role="tablist" aria-label="Product line">
          {CATALOG_TABS.map((tab) => (
            <button
              key={tab.id || 'all'}
              type="button"
              role="tab"
              aria-selected={(tab.line || '') === activeLine}
              className={(tab.line || '') === activeLine ? 'active' : ''}
              onClick={() => setLine(tab.line || '')}
            >
              {tab.label}
            </button>
          ))}
        </div>

        <div className="row g-4">
          <aside className="col-lg-3">
            <CatalogFilterPanel filters={filters} params={params} onChange={update} onClear={clearFilters} />
          </aside>
          <div className="col-lg-9">
            {loading ? <Loader label="Loading gym-to-street fits..." /> : error ? <ErrorState onRetry={loadCatalog} /> : data.items.length ? (
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
            ) : <EmptyState title="No fits in this moment." message="Try another gym moment or build a full fit with the AI designer at /ai/occasion." />}
          </div>
        </div>
      </section>
    </>
  )
}

export default memo(CatalogPage)
