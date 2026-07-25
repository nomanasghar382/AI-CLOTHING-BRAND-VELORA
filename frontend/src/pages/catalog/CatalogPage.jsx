import { useEffect, useState } from 'react'
import { useSearchParams } from 'react-router-dom'
import Breadcrumb from '../../components/common/Breadcrumb'
import Pagination from '../../components/common/Pagination'
import SearchBox from '../../components/common/SearchBox'
import ProductCard from '../../components/catalog/ProductCard'
import Loader from '../../components/feedback/Loader'
import EmptyState from '../../components/feedback/EmptyState'
import ErrorState from '../../components/feedback/ErrorState'
import { catalogService } from '../../services/catalogService'

export default function CatalogPage() {
  const [params, setParams] = useSearchParams()
  const [data, setData] = useState({ items: [], meta: null })
  const [filters, setFilters] = useState({ brands: [], colors: [], sizes: [] })
  const [loading, setLoading] = useState(true); const [error, setError] = useState(false)
  const query = params.get('q') || ''; const sort = params.get('sort') || 'newest'
  useEffect(() => { catalogService.filters().then(({ data: response }) => setFilters(response.data)).catch(() => {}); }, [])
  useEffect(() => { setLoading(true); setError(false); catalogService.products(Object.fromEntries(params)).then(({ data: response }) => setData(response.data)).catch(() => setError(true)).finally(() => setLoading(false)); }, [params])
  const update = (key, value) => { const next = new URLSearchParams(params); value ? next.set(key, value) : next.delete(key); if (key !== 'page') next.delete('page'); setParams(next) }
  return <section className="container py-4 py-lg-5"><Breadcrumb items={[{ label: 'Home', to: '/' }, { label: 'Catalog' }]} /><div className="d-flex flex-column flex-lg-row align-items-lg-end justify-content-between gap-3 mb-4"><div><p className="eyebrow">THE VELORA EDIT</p><h1 className="h2 mb-0">Discover your next expression.</h1></div><div className="catalog-search"><SearchBox value={query} onChange={(event) => update('q', event.target.value)} placeholder="Search abayas, scarves, linen..." /></div></div>
    <div className="row g-4"><aside className="col-lg-3"><div className="velora-card p-3 filter-panel"><p className="small text-slate-300">REFINE</p><label className="form-label">Sort by</label><select className="form-select velora-input mb-3" value={sort} onChange={(e) => update('sort', e.target.value)}><option value="newest">Newest</option><option value="price_asc">Price: low to high</option><option value="price_desc">Price: high to low</option><option value="popular">Most viewed</option><option value="alphabetical">A–Z</option></select><label className="form-label">Brand</label><select className="form-select velora-input mb-3" value={params.get('brand') || ''} onChange={(e) => update('brand', e.target.value)}><option value="">All brands</option>{filters.brands.map((brand) => <option key={brand.slug} value={brand.slug}>{brand.name}</option>)}</select><label className="form-label">Color</label><select className="form-select velora-input" value={params.get('color') || ''} onChange={(e) => update('color', e.target.value)}><option value="">All colors</option>{filters.colors.map((color) => <option key={color.slug} value={color.slug}>{color.name}</option>)}</select></div></aside>
      <div className="col-lg-9">{loading ? <Loader label="Curating the Velora edit..." /> : error ? <ErrorState /> : data.items.length ? <><div className="row g-3">{data.items.map((product) => <div className="col-6 col-md-4" key={product.id}><ProductCard product={product} /></div>)}</div><div className="mt-4 d-flex justify-content-center"><Pagination page={data.meta.current_page} pageCount={data.meta.last_page} onPageChange={(page) => update('page', page)} /></div></> : <EmptyState title="No pieces matched your edit." message="Try adjusting your search or filters." />}</div></div></section>
}
