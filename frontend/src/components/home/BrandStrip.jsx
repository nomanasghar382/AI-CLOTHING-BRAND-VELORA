import { Link } from 'react-router-dom'
import { PRIORITY_BRANDS } from '../../constants/gymToStreet'

export default function BrandStrip() {
  return (
    <section className="container py-4 pb-lg-5">
      <p className="retail-eyebrow text-center mb-3">TOP BRANDS</p>
      <div className="retail-brand-strip">
        {PRIORITY_BRANDS.map((brand) => (
          <Link key={brand} className="retail-brand-chip" to={`/brands/${brand.toLowerCase().replace(/\s+/g, '-')}`}>
            {brand}
          </Link>
        ))}
      </div>
    </section>
  )
}
