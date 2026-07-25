import { motion } from 'framer-motion'
import { FiArrowUpRight, FiLayers, FiShield, FiZap } from 'react-icons/fi'
import { Link } from 'react-router-dom'
import Card from '../components/common/Card'

const pillars = [
  { icon: FiLayers, title: 'Composable architecture', body: 'Independent modules connect through stable API and UI boundaries.' },
  { icon: FiShield, title: 'Secure by default', body: 'Sanctum tokens, validation, RBAC, rate limits and consistent API responses.' },
  { icon: FiZap, title: 'Ready to evolve', body: 'Commerce, AI and creator features can plug in without rewriting the core.' },
]

export default function HomePage() {
  return (
    <>
      <section className="hero-section">
        <div className="hero-orbit hero-orbit-one" /><div className="hero-orbit hero-orbit-two" />
        <div className="container position-relative py-5 py-lg-6">
          <motion.p className="eyebrow" initial={{ opacity: 0, y: 12 }} animate={{ opacity: 1, y: 0 }}>VELORA / FOUNDATION 01</motion.p>
          <motion.h1 className="display-hero" initial={{ opacity: 0, y: 16 }} animate={{ opacity: 1, y: 0 }} transition={{ delay: 0.08 }}>
            Fashion infrastructure<br /><em>with intention.</em>
          </motion.h1>
          <motion.p className="hero-copy" initial={{ opacity: 0, y: 12 }} animate={{ opacity: 1, y: 0 }} transition={{ delay: 0.16 }}>
            A refined, secure starting point for the next generation of global modest fashion.
          </motion.p>
          <motion.div initial={{ opacity: 0, y: 12 }} animate={{ opacity: 1, y: 0 }} transition={{ delay: 0.24 }}>
            <Link className="btn btn-velora-primary" to="/about">Explore the foundation <FiArrowUpRight /></Link>
          </motion.div>
        </div>
      </section>
      <section className="container py-5 py-lg-6">
        <p className="eyebrow">DESIGNED TO SCALE</p>
        <div className="row g-4">
          {pillars.map(({ icon: Icon, title, body }) => (
            <div className="col-md-4" key={title}><Card className="h-100 p-4"><Icon className="feature-icon" /><h2 className="h4 mt-4">{title}</h2><p className="text-slate-300 mb-0">{body}</p></Card></div>
          ))}
        </div>
      </section>
    </>
  )
}
