import { memo, useCallback, useEffect, useState } from 'react'
import { useSearchParams } from 'react-router-dom'
import Pagination from '../../components/common/Pagination'
import InstantSearchBox from '../../components/catalog/InstantSearchBox'
import ProductCard from '../../components/catalog/ProductCard'
import Loader from '../../components/feedback/Loader'
import EmptyState from '../../components/feedback/EmptyState'
import ErrorState from '../../components/feedback/ErrorState'
import Seo from '../../components/system/Seo'
import { GYM_MOMENTS } from '../../constants/gymToStreet'
import { catalogService } from '../../services/catalogService'
import { searchService } from '../../services/searchService'

function CatalogPage() {
  const [params, setParams] = useSearchParams()
  const [data, setData] = useState({ items: [], meta: null })
  const [loading, setLoading] = useState(true)
  const [error, setError] = useState(false)
  const query = params.get('q') || ''
  const activeMoment = params.get('moment') || ''

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

  const setMoment = useCallback((moment) => {
    const next = new URLSearchParams(params)
    if (moment) next.set('moment', moment)
    else next.delete('moment')
    next.delete('page')
    next.set('gender', 'men')
    setParams(next)
  }, [params, setParams])

  const momentLabel = GYM_MOMENTS.find((item) => item.id === activeMoment)?.label || 'All gym fits'

  return (
    <>
      <Seo title="Shop gym-to-street" description="Training fits and matching sneakers for Gen Z guys." />
      <section className="container py-4 py-lg-5">
        <div className="text-center mb-4">
          <h1 className="h2 mb-2">Shop {momentLabel.toLowerCase()}</h1>
          <p className="text-slate-300 mb-3">Tap a moment. Every fit includes matching sneakers.</p>
          <div className="catalog-search mx-auto" style={{ maxWidth: '28rem' }}>
            <InstantSearchBox initial={query} onSubmit={(term) => update('q', term)} />
          </div>
        </div>

        <div className="catalog-gender-tabs mb-4 justify-content-center" role="tablist">
          <button type="button" role="tab" aria-selected={activeMoment === ''} className={activeMoment === '' ? 'active' : ''} onClick={() => setMoment('')}>All</button>
          {GYM_MOMENTS.map((moment) => (
            <button key={moment.id} type="button" role="tab" aria-selected={activeMoment === moment.id} className={activeMoment === moment.id ? 'active' : ''} onClick={() => setMoment(moment.id)}>{moment.label}</button>
          ))}
        </div>

        {loading ? <Loader label="Loading fits..." /> : error ? <ErrorState onRetry={loadCatalog} /> : data.items.length ? (
          <>
            <div className="row g-3">
              {data.items.map((product) => (
                <div className="col-6 col-md-4 col-lg-3" key={product.id}>
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
      </section>
    </>
  )
}

export default memo(CatalogPage)
