import { motion } from 'framer-motion'
import { FiArrowUpRight, FiStar } from 'react-icons/fi'
import { Link } from 'react-router-dom'
import Card from '../components/common/Card'
import CloudinaryImage from '../components/common/CloudinaryImage'
import { CATALOG_DISPLAY_TOTAL, FEATURED_EDITORIAL } from '../constants/veloraIcons'
import { usePageMotionProps } from '../hooks/useMotionConfig'

export default function HomePage() {
  const motionProps = usePageMotionProps()
  return (
    <>
      <section className="hero-section">
        <div className="hero-orbit hero-orbit-one" /><div className="hero-orbit hero-orbit-two" />
        <div className="container position-relative py-5 py-lg-6">
          <motion.p className="eyebrow" {...motionProps}>VELORA SPORT / GEN Z ATHLETICS</motion.p>
          <motion.h1 className="display-hero" {...motionProps} transition={{ ...motionProps.transition, delay: 0.08 }}>
            Men&apos;s sportswear<br /><em>+ matching kicks.</em>
          </motion.h1>
          <motion.p className="hero-copy" {...motionProps} transition={{ ...motionProps.transition, delay: 0.16 }}>
            {CATALOG_DISPLAY_TOTAL} styles from Nike, Adidas, Puma, Jordan, Gymshark &amp; more. Every outfit ships with AI-matched sport shoes in the same colorway.
          </motion.p>
          <motion.div className="d-flex flex-wrap gap-2" {...motionProps} transition={{ ...motionProps.transition, delay: 0.24 }}>
            <Link className="btn btn-velora-primary" to="/catalog?gender=men">Shop sportswear <FiArrowUpRight /></Link>
            <Link className="btn btn-velora-secondary" to="/catalog?gender=men&line=footwear">Shop sneakers <FiArrowUpRight /></Link>
          </motion.div>
        </div>
      </section>

      <section className="container py-5 py-lg-6">
        <div className="d-flex flex-wrap justify-content-between align-items-end gap-3 mb-4">
          <div>
            <p className="eyebrow mb-2"><FiStar /> TOP SPORTS BRANDS</p>
            <h2 className="display-6 mb-0">Gen Z heat from the brands you already wear.</h2>
          </div>
          <Link className="nav-link-velora" to="/catalog?gender=men">Shop {CATALOG_DISPLAY_TOTAL} SKUs <FiArrowUpRight /></Link>
        </div>
        <div className="row g-3">
          {FEATURED_EDITORIAL.map((item) => (
            <div className="col-6 col-md-3" key={item.name}>
              <Card className="icon-card h-100 overflow-hidden">
                <Link to="/catalog?gender=men" className="icon-card-image-wrap">
                  <CloudinaryImage src={item.image} alt={item.name} className="icon-card-image" width={360} sizes="(max-width: 768px) 50vw, 25vw" />
                  <span className="badge icon-badge">{item.tag}</span>
                </Link>
                <div className="p-3">
                  <p className="icon-card-name mb-1">{item.name}</p>
                  <p className="small text-slate-300 mb-0">Men&apos;s sport · Gen Z drop</p>
                </div>
              </Card>
            </div>
          ))}
        </div>
      </section>
    </>
  )
}
