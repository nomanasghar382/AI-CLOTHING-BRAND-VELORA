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
            key={category.slug || 'all'}
            className="retail-category-tile"
            to={category.slug ? `/catalog?category=${category.slug}` : '/catalog?gender=men'}
          >
            <img src={category.image} alt="" referrerPolicy="no-referrer" />
            <span>{category.label}</span>
          </Link>
        ))}
      </div>
    </section>
  )
}
