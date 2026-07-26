import { motion } from 'framer-motion'
import { FiArrowUpRight, FiCamera, FiMic, FiSearch, FiStar, FiUser } from 'react-icons/fi'
import { Link } from 'react-router-dom'
import Card from '../components/common/Card'
import CloudinaryImage from '../components/common/CloudinaryImage'
import { CATALOG_DISPLAY_TOTAL, FEATURED_EDITORIAL } from '../constants/veloraIcons'
import { usePageMotionProps } from '../hooks/useMotionConfig'

const FEATURES = [
  { icon: FiUser, title: 'Avatar builder', copy: 'Create your sport body profile and save sizes for faster checkout.', to: '/avatar', cta: 'Build avatar' },
  { icon: FiCamera, title: 'Virtual try-on', copy: 'Preview hoodies, joggers & kicks on your avatar before you buy.', to: '/avatar', cta: 'Try it on' },
  { icon: FiMic, title: 'Voice search', copy: 'Say "Nike tech fleece" or "Jordan 4 black" — find heat hands-free.', to: '/catalog?gender=men', cta: 'Start talking' },
  { icon: FiSearch, title: 'Image search', copy: 'Drop a fit pic and we match it to Nike, Adidas, Puma & more.', to: '/visual-search', cta: 'Upload image' },
  { icon: FiStar, title: 'AI fashion designer', copy: 'Get full sport outfits with matching sneakers tailored to you.', to: '/ai', cta: 'Design my fit' },
]

export default function HomePage() {
  const motionProps = usePageMotionProps()
  return (
    <>
      <section className="hero-section">
        <div className="hero-orbit hero-orbit-one" /><div className="hero-orbit hero-orbit-two" />
        <div className="container position-relative py-5 py-lg-6">
          <motion.p className="eyebrow" {...motionProps}>VELORA SPORT / GEN Z ATHLETICS</motion.p>
          <motion.h1 className="display-hero" {...motionProps} transition={{ ...motionProps.transition, delay: 0.08 }}>
            The #1 destination for<br /><em>men&apos;s sportswear.</em>
          </motion.h1>
          <motion.p className="hero-copy" {...motionProps} transition={{ ...motionProps.transition, delay: 0.16 }}>
            {CATALOG_DISPLAY_TOTAL} styles from Nike, Adidas, Puma, Jordan, Gymshark &amp; more. Built for guys 16–35 — avatar try-on, voice search, image search &amp; AI outfit designer included.
          </motion.p>
          <motion.div className="d-flex flex-wrap gap-2" {...motionProps} transition={{ ...motionProps.transition, delay: 0.24 }}>
            <Link className="btn btn-velora-primary" to="/catalog?gender=men">Shop sportswear <FiArrowUpRight /></Link>
            <Link className="btn btn-velora-secondary" to="/avatar">Create your avatar <FiArrowUpRight /></Link>
            <Link className="btn btn-velora-secondary" to="/ai">AI fashion designer <FiArrowUpRight /></Link>
          </motion.div>
        </div>
      </section>

      <section className="container py-5">
        <div className="d-flex flex-wrap justify-content-between align-items-end gap-3 mb-4">
          <div>
            <p className="eyebrow mb-2">POWERED FOR GEN Z</p>
            <h2 className="display-6 mb-0">Search how you actually shop.</h2>
          </div>
        </div>
        <div className="row g-3">
          {FEATURES.map((feature) => (
            <div className="col-md-6 col-lg-4" key={feature.title}>
              <Card className="p-4 h-100">
                <feature.icon className="feature-icon" />
                <h3 className="h5 mt-3">{feature.title}</h3>
                <p className="text-slate-300">{feature.copy}</p>
                <Link className="nav-link-velora" to={feature.to}>{feature.cta} <FiArrowUpRight /></Link>
              </Card>
            </div>
          ))}
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
