import { motion } from 'framer-motion'
import { FiArrowUpRight } from 'react-icons/fi'
import { Link } from 'react-router-dom'
import Card from '../components/common/Card'
import CloudinaryImage from '../components/common/CloudinaryImage'
import { CURATED_FIT_COUNT, FEATURED_EDITORIAL, FIT_STEPS, NICHE_PROMISE, NICHE_TAGLINE, PRIORITY_BRANDS } from '../constants/gymToStreet'
import { usePageMotionProps } from '../hooks/useMotionConfig'

export default function HomePage() {
  const motionProps = usePageMotionProps()

  return (
    <>
      <section className="hero-section">
        <div className="hero-orbit hero-orbit-one" /><div className="hero-orbit hero-orbit-two" />
        <div className="container position-relative py-5 py-lg-6">
          <motion.p className="eyebrow" {...motionProps}>VELORA / {NICHE_TAGLINE.toUpperCase()}</motion.p>
          <motion.h1 className="display-hero" {...motionProps} transition={{ ...motionProps.transition, delay: 0.08 }}>
            Train hard.<br /><em>Walk out clean.</em>
          </motion.h1>
          <motion.p className="hero-copy" {...motionProps} transition={{ ...motionProps.transition, delay: 0.16 }}>
            {NICHE_PROMISE} {CURATED_FIT_COUNT} precision-matched pieces from {PRIORITY_BRANDS.slice(0, 4).join(', ')} &amp; more — built only for Gen Z guys who live in the gym and on the street.
          </motion.p>
          <motion.div className="d-flex flex-wrap gap-2" {...motionProps} transition={{ ...motionProps.transition, delay: 0.24 }}>
            <Link className="btn btn-velora-primary" to="/ai/occasion">Build my gym-to-street fit <FiArrowUpRight /></Link>
            <Link className="btn btn-velora-secondary" to="/avatar">Try on my avatar <FiArrowUpRight /></Link>
            <Link className="btn btn-velora-secondary" to="/catalog?gender=men">Shop training + kicks <FiArrowUpRight /></Link>
          </motion.div>
        </div>
      </section>

      <section className="container py-5 py-lg-6">
        <div className="mb-4">
          <p className="eyebrow mb-2">HOW IT WORKS</p>
          <h2 className="display-6 mb-0">One niche. Done right.</h2>
        </div>
        <div className="row g-3">
          {FIT_STEPS.map((item) => (
            <div className="col-md-4" key={item.step}>
              <Card className="p-4 h-100 niche-step-card">
                <p className="eyebrow mb-2">{item.step}</p>
                <h3 className="h5">{item.title}</h3>
                <p className="text-slate-300 mb-0">{item.copy}</p>
              </Card>
            </div>
          ))}
        </div>
      </section>

      <section className="container pb-5 pb-lg-6">
        <div className="d-flex flex-wrap justify-content-between align-items-end gap-3 mb-4">
          <div>
            <p className="eyebrow mb-2">CURATED DROPS</p>
            <h2 className="display-6 mb-0">Leg day to post-gym street.</h2>
          </div>
          <Link className="nav-link-velora" to="/catalog?gender=men">Shop gym-to-street <FiArrowUpRight /></Link>
        </div>
        <div className="row g-3">
          {FEATURED_EDITORIAL.map((item) => (
            <div className="col-6 col-md-3" key={item.name}>
              <Card className="icon-card h-100 overflow-hidden">
                <Link to={`/catalog?gender=men&moment=${item.moment}`} className="icon-card-image-wrap">
                  <CloudinaryImage src={item.image} alt={item.name} className="icon-card-image" width={360} sizes="(max-width: 768px) 50vw, 25vw" />
                  <span className="badge icon-badge">{item.tag}</span>
                </Link>
                <div className="p-3">
                  <p className="icon-card-name mb-1">{item.name}</p>
                  <p className="small text-slate-300 mb-0">Gym-to-street · Gen Z</p>
                </div>
              </Card>
            </div>
          ))}
        </div>
      </section>
    </>
  )
}
