import { motion } from 'framer-motion'
import { FiArrowUpRight } from 'react-icons/fi'
import { Link } from 'react-router-dom'
import { NICHE_TAGLINE } from '../constants/gymToStreet'
import { usePageMotionProps } from '../hooks/useMotionConfig'

export default function HomePage() {
  const motionProps = usePageMotionProps()

  return (
    <section className="hero-section simple-hero">
      <div className="container position-relative py-5 py-lg-6 text-center">
        <motion.p className="eyebrow" {...motionProps}>VELORA · {NICHE_TAGLINE.toUpperCase()}</motion.p>
        <motion.h1 className="display-hero mx-auto" style={{ maxWidth: '14ch' }} {...motionProps} transition={{ ...motionProps.transition, delay: 0.08 }}>
          Gym fit.<br /><em>Street ready.</em>
        </motion.h1>
        <motion.p className="hero-copy mx-auto" style={{ maxWidth: '36rem' }} {...motionProps} transition={{ ...motionProps.transition, delay: 0.16 }}>
          Pick a look. See it on your avatar. Buy training gear + matching sneakers. That&apos;s it.
        </motion.p>
        <motion.div className="d-flex flex-column flex-sm-row gap-2 justify-content-center mt-4" {...motionProps} transition={{ ...motionProps.transition, delay: 0.24 }}>
          <Link className="btn btn-velora-primary btn-lg" to="/catalog?gender=men">Shop gym fits <FiArrowUpRight /></Link>
          <Link className="btn btn-velora-secondary btn-lg" to="/ai/occasion">Build my fit <FiArrowUpRight /></Link>
        </motion.div>
        <motion.p className="small text-slate-300 mt-4 mb-0" {...motionProps} transition={{ ...motionProps.transition, delay: 0.32 }}>
          Nike · Gymshark · Adidas · Under Armour
        </motion.p>
      </div>
    </section>
  )
}
