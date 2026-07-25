import { useState } from 'react'
import { FiAward, FiBell, FiCheck, FiCopy, FiCreditCard, FiGift, FiHeart, FiLoader, FiShare2, FiStar, FiTag, FiTrendingUp } from 'react-icons/fi'
import useLoyalty from '../../hooks/useLoyalty'
import { loyaltyService } from '../../services/loyaltyService'

const points = (value) => new Intl.NumberFormat('en-US').format(Number(value || 0))
const Page = ({ eyebrow, title, children }) => <section className="container py-5 loyalty-page"><p className="eyebrow">{eyebrow}</p><h1 className="display-6 mb-4">{title}</h1>{children}</section>
const Empty = ({ children }) => <div className="velora-card loyalty-empty text-center p-4"><FiLoader className="feature-icon mb-2" /><p className="mb-0 text-slate-300">{children}</p></div>

export function RewardsPage() {
  const { overview, rewards, isLoading } = useLoyalty()
  return <Page eyebrow="VELORA CIRCLE" title="Rewards, made personal."><div className="loyalty-hero velora-card p-4 p-md-5 mb-4"><div><p className="eyebrow">YOUR MEMBERSHIP</p><h2>{overview?.tier || 'Velora Circle'}</h2><p className="text-slate-300 mb-0">Earn points for every considered purchase, review and referral.</p></div><strong>{points(overview?.points_balance)} <small>points</small></strong></div><div className="row g-3">{rewards.map((reward) => <div className="col-md-6 col-lg-4" key={reward.id}><article className="velora-card loyalty-reward p-4 h-100"><FiGift /><p className="eyebrow mt-3">{points(reward.points_cost)} POINTS</p><h3>{reward.name}</h3><p className="text-slate-300">{reward.description}</p><button className="btn btn-velora-secondary" type="button">Redeem reward</button></article></div>)}</div>{!isLoading && !rewards.length && <Empty>Rewards will appear here when your membership is available.</Empty>}</Page>
}

export function VipPage() {
  const { overview } = useLoyalty()
  const tiers = [['Circle', 'Everyday rewards and early access'], ['Atelier', 'Priority styling and seasonal previews'], ['Maison', 'Private events and concierge care']]
  return <Page eyebrow="MEMBERSHIP" title="Your place in the Circle."><div className="row g-3">{tiers.map(([tier, detail], index) => <div className="col-md-4" key={tier}><article className={`velora-card loyalty-tier p-4 h-100 ${overview?.tier?.toLowerCase() === tier.toLowerCase() ? 'active' : ''}`}><FiStar /><p className="eyebrow mt-3">LEVEL 0{index + 1}</p><h2>{tier}</h2><p className="text-slate-300">{detail}</p>{overview?.tier === tier && <span className="status-pill">Your current tier</span>}</article></div>)}</div></Page>
}

export function ReferralPage() {
  const [link, setLink] = useState(''); const [message, setMessage] = useState('')
  const create = async () => { setMessage(''); try { const { data } = await loyaltyService.createReferral(); setLink(data?.data?.url || data?.url || ''); setMessage('Your referral invitation is ready.') } catch { setMessage('Referral invitations are not available for this account yet.') } }
  return <Page eyebrow="SHARE THE CIRCLE" title="Good style is better shared."><article className="velora-card referral-card p-4 p-md-5"><FiShare2 className="feature-icon" /><h2 className="mt-3">Invite a friend, both receive rewards.</h2><p className="text-slate-300">Send a personal invitation after your referral program is enabled.</p><button className="btn btn-velora-primary" onClick={create} type="button">Create invitation</button>{link && <div className="input-group mt-3"><input className="form-control" readOnly value={link} /><button className="btn btn-velora-secondary" onClick={() => navigator.clipboard?.writeText(link)} type="button"><FiCopy /> Copy</button></div>}{message && <p className="small mt-3 mb-0">{message}</p>}</article></Page>
}

export function WalletPage() {
  const { wallet } = useLoyalty()
  return <Page eyebrow="ACCOUNT CREDIT" title="Your Velora wallet."><article className="velora-card wallet-card p-4 p-md-5"><FiCreditCard /><p className="eyebrow mt-4">AVAILABLE BALANCE</p><strong>${Number(wallet?.balance || 0).toFixed(2)}</strong><p className="text-slate-300 mb-0">Wallet credit and eligible refunds will be available at checkout.</p></article></Page>
}

export function GiftCardsPage() {
  const [code, setCode] = useState(''); const [message, setMessage] = useState('')
  const redeem = async (event) => { event.preventDefault(); setMessage(''); try { await loyaltyService.redeemGiftCard(code); setCode(''); setMessage('Gift card added to your wallet.') } catch { setMessage('We could not redeem that code. Check it and try again.') } }
  return <Page eyebrow="GIFTING" title="A thoughtful choice, beautifully delivered."><div className="row g-4"><div className="col-md-6"><article className="velora-card gift-card p-4 h-100"><FiGift /><h2 className="mt-3">Gift Velora</h2><p className="text-slate-300">Give someone the freedom to choose the pieces that feel like them.</p><button className="btn btn-velora-primary" type="button">Choose a gift card</button></article></div><div className="col-md-6"><form className="velora-card p-4 h-100" onSubmit={redeem}><FiTag /><h2 className="mt-3">Redeem a card</h2><label className="form-label">Gift card code<input className="form-control mt-2" value={code} onChange={(e) => setCode(e.target.value)} required /></label><button className="btn btn-velora-secondary mt-2">Apply to wallet</button>{message && <p className="small mt-3 mb-0">{message}</p>}</form></div></div></Page>
}

export function AchievementsPage() {
  const { achievements } = useLoyalty()
  return <Page eyebrow="MILESTONES" title="Your style story."><div className="row g-3">{achievements.map((item) => <div className="col-md-6 col-lg-4" key={item.id}><article className="velora-card p-4 h-100"><FiAward className="feature-icon" /><h3 className="mt-3">{item.name}</h3><p className="text-slate-300">{item.description}</p>{item.earned_at ? <span className="status-pill"><FiCheck /> Earned</span> : <small className="text-slate-300">{item.progress || 0}% complete</small>}</article></div>)}</div>{!achievements.length && <Empty>Achievements unlock as you explore Velora.</Empty>}</Page>
}

export function NotificationsPage() {
  const { notifications, markNotificationRead } = useLoyalty()
  return <Page eyebrow="UPDATES" title="Keep in the know."><div className="velora-card p-2">{notifications.map((notice) => <button className={`notification-row ${notice.read ? '' : 'unread'}`} type="button" onClick={() => !notice.read && markNotificationRead(notice.id)} key={notice.id}><FiBell /><span><strong>{notice.title}</strong><small>{notice.body || notice.message}</small></span>{!notice.read && <i />}</button>)}</div>{!notifications.length && <Empty>Order, reward and collection updates will arrive here.</Empty>}</Page>
}

export function ProductAlertsPage() {
  const [form, setForm] = useState({ email: '', type: 'price' }); const [message, setMessage] = useState('')
  const submit = async (event) => { event.preventDefault(); setMessage(''); try { await loyaltyService.createAlert({ ...form, product_id: form.product_id || undefined }); setMessage('Your alert is set. We’ll let you know when it changes.') } catch { setMessage('Sign in and select a product to create a live alert.') } }
  return <Page eyebrow="SMART SHOPPING" title="Never miss the moment."><div className="row g-4"><div className="col-md-6"><article className="velora-card p-4 h-100"><FiTrendingUp className="feature-icon" /><h2 className="mt-3">Price alert</h2><p className="text-slate-300">Hear when a saved piece reaches a price you love.</p></article></div><div className="col-md-6"><article className="velora-card p-4 h-100"><FiHeart className="feature-icon" /><h2 className="mt-3">Restock alert</h2><p className="text-slate-300">Be first to know when your size returns.</p></article></div></div><form className="velora-card p-4 mt-4" onSubmit={submit}><div className="row g-3 align-items-end"><div className="col-md-4"><label className="form-label">Alert type<select className="form-select" value={form.type} onChange={(e) => setForm({ ...form, type: e.target.value })}><option value="price">Price drop</option><option value="restock">Restock</option></select></label></div><div className="col-md-5"><label className="form-label">Email<input type="email" required className="form-control" value={form.email} onChange={(e) => setForm({ ...form, email: e.target.value })} /></label></div><div className="col-md-3"><button className="btn btn-velora-primary w-100">Set alert</button></div></div>{message && <p className="small mt-3 mb-0">{message}</p>}</form></Page>
}
