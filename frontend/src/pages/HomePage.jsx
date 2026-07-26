import { motion } from 'framer-motion'
import { FiArrowUpRight, FiZap } from 'react-icons/fi'
import { Link } from 'react-router-dom'
import MomentPicker from '../components/home/MomentPicker'
import TrendingFits from '../components/home/TrendingFits'
import { FEATURED_EDITORIAL, NICHE_TAGLINE } from '../constants/gymToStreet'
import { usePageMotionProps } from '../hooks/useMotionConfig'

export default function HomePage() {
  const motionProps = usePageMotionProps()
  const heroImage = FEATURED_EDITORIAL[0].image

  return (
    <>
      <section className="genz-hero" style={{ backgroundImage: `linear-gradient(180deg, rgba(7,9,15,.55) 0%, rgba(7,9,15,.92) 72%), url(${heroImage})` }}>
        <div className="container py-5 py-lg-6">
          <motion.div className="genz-hero-badges" {...motionProps}>
            <span className="live-pill-genz"><FiZap /> {NICHE_TAGLINE}</span>
            <span className="stat-pill">2.4k+ fits built this week</span>
            <span className="stat-pill">Matching kicks every look</span>
          </motion.div>
          <motion.h1 className="genz-hero-title" {...motionProps} transition={{ ...motionProps.transition, delay: 0.06 }}>
            LEG DAY UNIFORM.<br />
            <span className="genz-gradient-text">POST-GYM HEAT.</span>
          </motion.h1>
          <motion.p className="genz-hero-sub" {...motionProps} transition={{ ...motionProps.transition, delay: 0.12 }}>
            For guys who train serious and dress clean after. Pick your moment — we build the full fit + sneakers.
          </motion.p>
          <motion.div className="d-flex flex-wrap gap-2 mt-4" {...motionProps} transition={{ ...motionProps.transition, delay: 0.18 }}>
            <Link className="btn btn-genz-primary btn-lg" to="/ai/occasion">Build my fit in 60s <FiArrowUpRight /></Link>
            <Link className="btn btn-genz-ghost btn-lg" to="/avatar">Try on avatar</Link>
          </motion.div>
        </div>
      </section>

      <section className="container py-5">
        <MomentPicker />
      </section>

      <TrendingFits />

      <section className="container py-5 pb-lg-6">
        <div className="genz-proof-bar">
          <div><strong>Nike</strong><span>Training</span></div>
          <div><strong>Gymshark</strong><span>Core</span></div>
          <div><strong>Adidas</strong><span>Street</span></div>
          <div><strong>UA</strong><span>Performance</span></div>
        </div>
      </section>
    </>
  )
}
