import { useEffect, useState } from 'react'
import { Link } from 'react-router-dom'
import CloudinaryImage from '../common/CloudinaryImage'
import { CATALOG_TABS } from '../../constants/gymToStreet'
import { catalogService } from '../../services/catalogService'

export default function RetailTrending() {
  const [tab, setTab] = useState('featured')
  const [items, setItems] = useState([])

  useEffect(() => {
    const tabConfig = CATALOG_TABS.find((item) => item.id === tab) || CATALOG_TABS[0]
    catalogService.products({ gender: 'men', per_page: 12, sort: 'popular', ...tabConfig.params })
      .then(({ data }) => setItems(data.data?.items || []))
      .catch(() => setItems([]))
  }, [tab])

  return (
    <section className="retail-trending">
      <div className="container">
        <div className="d-flex flex-column flex-md-row justify-content-between align-items-md-end gap-3 mb-3">
          <div>
            <p className="retail-eyebrow">TRENDING</p>
            <h2 className="retail-heading h3 mb-0">Must-have gear</h2>
          </div>
          <div className="retail-tabs" role="tablist">
            {CATALOG_TABS.map((item) => (
              <button
                key={item.id}
                type="button"
                role="tab"
                aria-selected={tab === item.id}
                className={tab === item.id ? 'active' : ''}
                onClick={() => setTab(item.id)}
              >
                {item.label}
              </button>
            ))}
          </div>
        </div>
        {items.length ? (
          <div className="retail-product-grid">
            {items.map((product) => (
              <Link key={product.id} className="retail-product-card" to={`/catalog/${product.slug}`}>
                <div className={`retail-product-image ${product.catalog_line === 'footwear' ? 'is-footwear' : ''}`}>
                  <CloudinaryImage
                    src={product.images?.[0]?.url || product.images?.[0]?.thumbnail_url}
                    alt={product.name}
                    width={400}
                    sizes="(max-width: 768px) 50vw, 20vw"
                  />
                </div>
                <div className="retail-product-meta">
                  <span>{product.brand?.name}</span>
                  <strong>{product.name}</strong>
                  <em>${Number(product.sale_price || product.price).toFixed(2)}</em>
                </div>
              </Link>
            ))}
          </div>
        ) : (
          <p className="text-slate-300 mb-0">Start the backend to load products. Run <code>START-VELORA.ps1</code>.</p>
        )}
        <div className="text-center mt-4">
          <Link className="btn btn-retail-secondary" to="/catalog?gender=men">Shop all men&apos;s sport</Link>
        </div>
      </div>
    </section>
  )
}
