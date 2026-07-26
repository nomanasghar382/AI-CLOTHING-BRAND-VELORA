import { Link } from 'react-router-dom'
import { RETAIL_CATEGORIES } from '../../constants/gymToStreet'

export default function CategoryTileGrid() {
  return (
    <section className="container py-5">
      <div className="d-flex justify-content-between align-items-end gap-3 mb-3">
        <div>
          <p className="retail-eyebrow">SHOP BY CATEGORY</p>
          <h2 className="retail-heading h3 mb-0">Men&apos;s Sport</h2>
        </div>
        <Link className="retail-link" to="/catalog?gender=men">View all →</Link>
      </div>
      <div className="retail-category-grid">
        {RETAIL_CATEGORIES.map((category) => (
          <Link
            key={category.slug}
            className="retail-category-tile"
            to={`/catalog?category=${category.slug}`}
            style={{ backgroundImage: `linear-gradient(180deg, rgba(0,0,0,.05) 40%, rgba(0,0,0,.75) 100%), url(${category.image})` }}
          >
            <span>{category.label}</span>
          </Link>
        ))}
      </div>
    </section>
  )
}
