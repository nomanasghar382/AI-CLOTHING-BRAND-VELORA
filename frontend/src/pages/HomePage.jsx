import { motion } from 'framer-motion'
import { FiArrowUpRight, FiStar } from 'react-icons/fi'
import { Link } from 'react-router-dom'
import Card from '../components/common/Card'
import CloudinaryImage from '../components/common/CloudinaryImage'
import { FEATURED_EDITORIAL } from '../constants/veloraIcons'
import { usePageMotionProps } from '../hooks/useMotionConfig'

export default function HomePage() {
  const motionProps = usePageMotionProps()
  return (
    <>
      <section className="hero-section">
        <div className="hero-orbit hero-orbit-one" /><div className="hero-orbit hero-orbit-two" />
        <div className="container position-relative py-5 py-lg-6">
          <motion.p className="eyebrow" {...motionProps}>VELORA / GEN Z MODEST</motion.p>
          <motion.h1 className="display-hero" {...motionProps} transition={{ ...motionProps.transition, delay: 0.08 }}>
            Young modest fashion<br /><em>that actually sells.</em>
          </motion.h1>
          <motion.p className="hero-copy" {...motionProps} transition={{ ...motionProps.transition, delay: 0.16 }}>
            Editorial campaign photos. Young models. Hijab, abaya, niqab, kurta, thobe & shalwar — styled for ages 16–35.
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
            <p className="eyebrow mb-2"><FiStar /> EDITORIAL DROPS</p>
            <h2 className="display-6 mb-0">Styled like the brands Gen Z already buys from.</h2>
          </div>
          <Link className="nav-link-velora" to="/catalog">Shop the edit <FiArrowUpRight /></Link>
        </div>
        <div className="row g-3">
          {FEATURED_EDITORIAL.map((item) => (
            <div className="col-6 col-md-3" key={item.name}>
              <Card className="icon-card h-100 overflow-hidden">
                <Link to={`/catalog?gender=${item.gender}`} className="icon-card-image-wrap">
                  <CloudinaryImage src={item.image} alt={item.name} className="icon-card-image" width={360} sizes="(max-width: 768px) 50vw, 25vw" />
                  <span className="badge icon-badge">{item.tag}</span>
                </Link>
                <div className="p-3">
                  <p className="icon-card-name mb-1">{item.name}</p>
                  <p className="small text-slate-300 mb-0">{item.gender === 'men' ? "Men's" : "Women's"} · Gen Z edit</p>
                </div>
              </Card>
            </div>
          ))}
        </div>
      </section>
    </>
  )
}
