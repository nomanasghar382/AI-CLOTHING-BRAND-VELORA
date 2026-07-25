import { motion } from 'framer-motion'
import { FiArrowUpRight, FiStar } from 'react-icons/fi'
import { Link } from 'react-router-dom'
import Card from '../components/common/Card'
import CloudinaryImage from '../components/common/CloudinaryImage'
import { FEATURED_VELORA_ICONS } from '../constants/veloraIcons'
import { usePageMotionProps } from '../hooks/useMotionConfig'

export default function HomePage() {
  const motionProps = usePageMotionProps()
  return (
    <>
      <section className="hero-section">
        <div className="hero-orbit hero-orbit-one" /><div className="hero-orbit hero-orbit-two" />
        <div className="container position-relative py-5 py-lg-6">
          <motion.p className="eyebrow" {...motionProps}>VELORA ICON SERIES</motion.p>
          <motion.h1 className="display-hero" {...motionProps} transition={{ ...motionProps.transition, delay: 0.08 }}>
            Every face.<br /><em>One exclusive drop.</em>
          </motion.h1>
          <motion.p className="hero-copy" {...motionProps} transition={{ ...motionProps.transition, delay: 0.16 }}>
            500+ unique Velora Icons — Gen Z modest fashion with niqab, hijab, abaya, thobe, kurta, shalwar kameez & more. No repeated faces. Ever.
          </motion.p>
          <motion.div className="d-flex flex-wrap gap-2" {...motionProps} transition={{ ...motionProps.transition, delay: 0.24 }}>
            <Link className="btn btn-velora-primary" to="/catalog?gender=women">Shop women <FiArrowUpRight /></Link>
            <Link className="btn btn-velora-secondary" to="/catalog?gender=men">Shop men <FiArrowUpRight /></Link>
          </motion.div>
        </div>
      </section>

      <section className="container py-5 py-lg-6">
        <div className="d-flex flex-wrap justify-content-between align-items-end gap-3 mb-4">
          <div>
            <p className="eyebrow mb-2"><FiStar /> VELORA ICONS</p>
            <h2 className="display-6 mb-0">The faces wearing Velora right now.</h2>
          </div>
          <Link className="nav-link-velora" to="/catalog">See every icon drop <FiArrowUpRight /></Link>
        </div>
        <div className="row g-3">
          {FEATURED_VELORA_ICONS.map((icon) => (
            <div className="col-6 col-md-3" key={icon.handle}>
              <Card className="icon-card h-100 overflow-hidden">
                <Link to={`/catalog?gender=${icon.gender}`} className="icon-card-image-wrap">
                  <CloudinaryImage src={icon.image} alt={icon.name} className="icon-card-image" width={360} sizes="(max-width: 768px) 50vw, 25vw" />
                  <span className="badge icon-badge">{icon.handle}</span>
                </Link>
                <div className="p-3">
                  <p className="icon-card-name mb-1">{icon.name}</p>
                  <p className="small text-slate-300 mb-0">Velora Icon · {icon.gender === 'men' ? "Men's" : "Women's"} campaign</p>
                </div>
              </Card>
            </div>
          ))}
        </div>
        <p className="small text-slate-300 mt-4 mb-0">
          Velora Icons are fictional campaign ambassadors for this demo. For real celebrity partnerships, upload licensed brand photoshoots via your admin panel.
        </p>
      </section>
    </>
  )
}
