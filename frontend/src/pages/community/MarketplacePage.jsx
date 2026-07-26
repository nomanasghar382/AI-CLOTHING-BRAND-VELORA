import { useEffect, useState } from 'react'
import { Link } from 'react-router-dom'
import { FiArrowRight, FiBarChart2, FiCheck, FiUsers } from 'react-icons/fi'
import Loader from '../../components/feedback/Loader'
import ErrorState from '../../components/feedback/ErrorState'
import { communityService } from '../../services/communityService'
import useCommunity from '../../hooks/useCommunity'
import LookCard from '../../components/community/LookCard'

export function CreatorProfilePage() {
  return <MarketplacePage profile />
}

export function CreatorDashboardPage() {
  const stats = [['This month', '$2,480', '+18.2%'], ['Look saves', '1,284', '+9.4%'], ['Link clicks', '892', '+14.7%']]
  return <section className="container py-5 feature-page"><p className="eyebrow">CREATOR STUDIO</p><div className="d-flex justify-content-between align-items-end flex-wrap gap-3 mb-4"><div><h1>Your edit is resonating.</h1><p className="text-slate-300 mb-0">A clear read on the community around your work.</p></div><Link className="btn btn-velora-primary" to="/creator/analytics"><FiBarChart2 /> View analytics</Link></div><div className="row g-3 mb-4">{stats.map(([label, value, change]) => <div className="col-md-4" key={label}><div className="velora-card p-4 metric-card"><p>{label}</p><strong>{value}</strong><span><FiArrowRight /> {change} vs last month</span></div></div>)}</div><div className="velora-card p-4"><div className="d-flex justify-content-between"><div><p className="eyebrow">PUBLISHED LOOKS</p><h2 className="h4">Your latest edit</h2></div><button className="btn btn-velora-secondary">Create look</button></div><div className="dashboard-bars">{[42, 74, 58, 91, 65, 82, 74].map((height, index) => <div key={height} className="dashboard-bar"><i style={{ height: `${height}%` }} /><small>{['M', 'T', 'W', 'T', 'F', 'S', 'S'][index]}</small></div>)}</div></div></section>
}

export function AnalyticsPage() {
  return <section className="container py-5 feature-page"><p className="eyebrow">CREATOR ANALYTICS</p><h1>Understand what moves your audience.</h1><div className="row g-3 mt-2"><div className="col-lg-8"><div className="velora-card p-4 h-100"><p className="eyebrow">ENGAGEMENT OVERVIEW</p><h2 className="h4">3,942 actions this month</h2><div className="line-chart" aria-label="Engagement chart"><svg viewBox="0 0 600 180" preserveAspectRatio="none"><polyline points="0,145 80,112 160,132 240,76 320,102 400,38 480,71 600,20" /></svg></div></div></div><div className="col-lg-4"><div className="velora-card p-4 h-100"><p className="eyebrow">AUDIENCE</p><h2 className="h4 mb-4">Growing with intent</h2>{[['New followers', '468'], ['Returning viewers', '62%'], ['Top source', 'Community feed']].map(([label, value]) => <div className="stat-row" key={label}><span>{label}</span><strong>{value}</strong></div>)}</div></div></div></section>
}

export default function MarketplacePage({ profile = false }) {
  const [creators, setCreators] = useState([])
  const [looks, setLooks] = useState([])
  const [loading, setLoading] = useState(true)
  const [error, setError] = useState(false)
  const { following, toggleFollow } = useCommunity()
  const [saved, setSaved] = useState([])

  const load = () => {
    setLoading(true)
    setError(false)
    Promise.all([communityService.creators(), communityService.looks()])
      .then(([creatorData, lookData]) => { setCreators(creatorData); setLooks(lookData) })
      .catch(() => setError(true))
      .finally(() => setLoading(false))
  }

  useEffect(() => { load() }, [])

  const lead = creators[0]
  if (loading) return <section className="container py-5 feature-page"><Loader label="Loading creators..." /></section>
  if (error) return <section className="container py-5 feature-page"><ErrorState title="Creators are unavailable right now." onRetry={load} /></section>
  if (profile && lead) return <section className="container py-5 feature-page"><div className="creator-profile velora-card p-4 p-md-5"><img src={lead.image} alt={lead.name} /><div><p className="eyebrow">CREATOR PROFILE</p><h1>{lead.name} <FiCheck className="verified" /></h1><p className="text-slate-300">{lead.bio}</p><div className="d-flex gap-4 mb-3"><strong>{lead.followers} <small>followers</small></strong><strong>42 <small>looks</small></strong></div><button className="btn btn-velora-primary" onClick={() => toggleFollow(lead.id)}>{following.includes(lead.id) ? 'Following' : 'Follow creator'}</button></div></div><h2 className="h3 mt-5 mb-3">Amara’s latest looks</h2><div className="look-grid">{looks.map((look) => <LookCard key={look.id} look={look} saved={saved.includes(look.id)} onSave={(id) => setSaved((current) => current.includes(id) ? current.filter((item) => item !== id) : [...current, id])} />)}</div></section>
  return <section className="feature-page"><div className="marketplace-hero"><div className="container py-5 py-lg-6"><p className="eyebrow">THE CREATOR MARKETPLACE</p><h1 className="display-hero">Follow taste,<br /><em>not trends.</em></h1><p className="hero-copy">A living marketplace of independent style voices, shoppable edits, and ideas worth saving.</p><Link to="/community" className="btn btn-velora-primary">Explore community <FiArrowRight /></Link></div></div><div className="container py-5"><div className="d-flex justify-content-between align-items-end mb-4"><div><p className="eyebrow">CURATED VOICES</p><h2 className="h2 mb-0">Creators shaping the edit.</h2></div><Link to="/followers">Your following <FiUsers /></Link></div><div className="row g-3">{creators.map((creator) => <div className="col-md-4" key={creator.id}><article className="creator-card velora-card p-3"><img src={creator.image} alt={creator.name} /><p className="eyebrow mt-3 mb-1">{creator.specialty}</p><h3 className="h5 mb-1">{creator.name}</h3><p className="text-slate-300 small">{creator.followers} followers · {creator.handle}</p><button className="btn btn-velora-secondary w-100" onClick={() => toggleFollow(creator.id)}>{following.includes(creator.id) ? 'Following' : 'Follow'}</button></article></div>)}</div></div></section>
}
