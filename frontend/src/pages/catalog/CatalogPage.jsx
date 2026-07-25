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
import PageTransition from '../../components/system/PageTransition'
import { catalogService } from '../../services/catalogService'
import { searchService } from '../../services/searchService'

function CatalogPage() {
  const [params, setParams] = useSearchParams()
  const [data, setData] = useState({ items: [], meta: null })
  const [filters, setFilters] = useState({ brands: [], colors: [], sizes: [], materials: [], fabrics: [], coverage_levels: [], occasions: [], price_range: { min: 0, max: 0 } })
  const [loading, setLoading] = useState(true)
  const [error, setError] = useState(false)
  const query = params.get('q') || ''

  useEffect(() => {
    catalogService.filters().then(({ data: response }) => setFilters(response.data)).catch(() => {})
  }, [])

  useEffect(() => {
    setLoading(true)
    setError(false)
    const request = query
      ? searchService.search(Object.fromEntries(params))
      : catalogService.products(Object.fromEntries(params))

    request
      .then(({ data: response }) => setData(response.data))
      .catch(() => setError(true))
      .finally(() => setLoading(false))
  }, [params, query])

  const update = useCallback((key, value) => {
    const next = new URLSearchParams(params)
    if (value) next.set(key, value)
    else next.delete(key)
    if (key !== 'page') next.delete('page')
    setParams(next)
  }, [params, setParams])

  const clearFilters = useCallback(() => setParams(new URLSearchParams(query ? { q: query } : {})), [query, setParams])

  return (
    <PageTransition>
      <Seo title="Catalog" description="Discover curated modest fashion from the VELORA edit." />
      <section className="container py-4 py-lg-5">
        <Breadcrumb items={[{ label: 'Home', to: '/' }, { label: 'Catalog' }]} />
        <div className="d-flex flex-column flex-lg-row align-items-lg-end justify-content-between gap-3 mb-4">
          <div>
            <p className="eyebrow">THE VELORA EDIT</p>
            <h1 className="h2 mb-0">Discover your next expression.</h1>
          </div>
          <div className="catalog-search">
            <InstantSearchBox initial={query} onSubmit={(term) => update('q', term)} />
          </div>
        </div>
        <div className="row g-4">
          <aside className="col-lg-3">
            <CatalogFilterPanel filters={filters} params={params} onChange={update} onClear={clearFilters} />
          </aside>
          <div className="col-lg-9">
            {loading ? <Loader label="Curating the Velora edit..." /> : error ? <ErrorState /> : data.items.length ? (
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
            ) : <EmptyState title="No pieces matched your edit." message="Try adjusting your search or filters." />}
          </div>
        </div>
      </section>
    </PageTransition>
  )
}

export default memo(CatalogPage)
