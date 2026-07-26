import { useEffect, useState } from 'react'
import { Link } from 'react-router-dom'
import CloudinaryImage from '../common/CloudinaryImage'
import { catalogService } from '../../services/catalogService'

export default function TrendingFits() {
  const [items, setItems] = useState([])

  useEffect(() => {
    catalogService.products({ gender: 'men', per_page: 8, sort: 'popular' })
      .then(({ data }) => setItems(data.data?.items || []))
      .catch(() => setItems([]))
  }, [])

  if (!items.length) return null

  return (
    <section className="trending-fits">
      <div className="container">
        <div className="d-flex justify-content-between align-items-end gap-3 mb-3">
          <div>
            <p className="genz-eyebrow">TRENDING NOW</p>
            <h2 className="genz-heading h3 mb-0">Guys are copping these</h2>
          </div>
          <Link className="genz-link" to="/catalog?gender=men">See all →</Link>
        </div>
        <div className="trending-scroll">
          {items.map((product) => (
            <Link key={product.id} className="trending-card" to={`/catalog/${product.slug}`}>
              <CloudinaryImage
                src={product.images?.[0]?.url || product.images?.[0]?.thumbnail_url}
                alt={product.name}
                className="trending-card-img"
                width={280}
              />
              <div className="trending-card-meta">
                <span>{product.brand?.name}</span>
                <strong>{product.name}</strong>
              </div>
            </Link>
          ))}
        </div>
      </div>
    </section>
  )
}
