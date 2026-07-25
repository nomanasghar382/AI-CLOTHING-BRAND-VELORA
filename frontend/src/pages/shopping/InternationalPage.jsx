import { useMemo, useState } from 'react'
import { motion } from 'framer-motion'
import { FiBox, FiCompass, FiMapPin, FiMaximize, FiPackage, FiTruck } from 'react-icons/fi'
import useInternational from '../../hooks/useInternational'
import { internationalService } from '../../services/internationalService'

const localShipping = { US: 12, GB: 14, AE: 18, SA: 18, FR: 15 }
const sizeRows = [{ label: 'XS', bust: '80–84', waist: '62–66', hips: '86–90' }, { label: 'S', bust: '85–89', waist: '67–71', hips: '91–95' }, { label: 'M', bust: '90–94', waist: '72–76', hips: '96–100' }, { label: 'L', bust: '95–101', waist: '77–83', hips: '101–107' }]

function Result({ children }) { return <div className="international-result mt-3">{children}</div> }

export default function InternationalPage() {
  const { country, currency, market } = useInternational()
  const [shipping, setShipping] = useState({ value: '', weight: '1', result: null })
  const [duty, setDuty] = useState({ value: '', result: null })
  const [tracking, setTracking] = useState({ reference: '', result: null })
  const [warehouse, setWarehouse] = useState(null)
  const format = useMemo(() => new Intl.NumberFormat('en-US', { style: 'currency', currency, maximumFractionDigits: 2 }), [currency])
  const calculateShipping = async (event) => {
    event.preventDefault()
    const fallback = Number(localShipping[country] || 18) + Math.max(0, Number(shipping.weight) - 1) * 4
    try { const { data } = await internationalService.shippingEstimate({ country, currency, locale: 'en' }); setShipping((current) => ({ ...current, result: { amount: data.data.shipping_total, arrival: `${data.data.delivery_days.min}–${data.data.delivery_days.max} business days` } })) } catch { setShipping((current) => ({ ...current, result: { amount: fallback, arrival: '4–8 business days', fallback: true } })) }
  }
  const calculateDuty = async (event) => {
    event.preventDefault()
    const rate = ['AE', 'SA'].includes(country) ? 0.05 : country === 'GB' || country === 'FR' ? 0.2 : 0
    try { const { data } = await internationalService.dutyEstimate({ country, currency, locale: 'en' }); setDuty((current) => ({ ...current, result: { amount: data.data.duty_total, rate: duty.value ? (Number(data.data.duty_total) / Number(duty.value)) * 100 : undefined } })) } catch { setDuty((current) => ({ ...current, result: { amount: Number(duty.value || 0) * rate, rate: rate * 100, fallback: true } })) }
  }
  const findTracking = async (event) => {
    event.preventDefault()
    try { const { data } = await internationalService.track(tracking.reference); setTracking((current) => ({ ...current, result: data.data })) } catch { setTracking((current) => ({ ...current, result: tracking.reference ? { status: 'Reference received', detail: 'Sign in to view live carrier milestones for this order.' } : null })) }
  }
  const checkWarehouse = async () => {
    try { const { data } = await internationalService.availability({ country, currency, locale: 'en' }); setWarehouse({ name: data.data.warehouse?.name || 'Regional warehouse', status: 'Available to ship', dispatch: `Estimated delivery: ${data.data.delivery_days.min}–${data.data.delivery_days.max} business days` }) } catch { setWarehouse({ name: country === 'US' ? 'New York' : country === 'AE' || country === 'SA' ? 'Dubai' : 'Amsterdam', status: 'Available to ship', dispatch: 'Dispatches within 1–2 business days' }) }
  }
  return <section className="container py-5 py-lg-6 international-page">
    <motion.div initial={{ opacity: 0, y: 16 }} animate={{ opacity: 1, y: 0 }} className="international-intro">
      <p className="eyebrow">GLOBAL CLIENT SERVICES</p><h1>Considered delivery,<br /><em>wherever you are.</em></h1><p>Prices, delivery, sizing and import guidance tailored to {market?.label}.</p>
    </motion.div>
    <div className="row g-4">
      <div className="col-lg-6"><article className="international-card"><FiTruck /><p className="eyebrow">SHIPPING CALCULATOR</p><h2>Plan your delivery</h2><form onSubmit={calculateShipping}><input aria-label="Order value" type="number" min="0" placeholder="Order value" value={shipping.value} onChange={(e) => setShipping({ ...shipping, value: e.target.value })} required /><input aria-label="Package weight" type="number" min=".1" step=".1" placeholder="Weight in kg" value={shipping.weight} onChange={(e) => setShipping({ ...shipping, weight: e.target.value })} required /><button className="btn btn-velora-primary">Estimate shipping</button></form>{shipping.result && <Result>{format.format(shipping.result.amount)} · {shipping.result.arrival}</Result>}</article></div>
      <div className="col-lg-6"><article className="international-card"><FiCompass /><p className="eyebrow">IMPORT DUTIES</p><h2>Know before you order</h2><form onSubmit={calculateDuty}><input aria-label="Item value" type="number" min="0" placeholder="Merchandise value" value={duty.value} onChange={(e) => setDuty({ ...duty, value: e.target.value })} required /><button className="btn btn-velora-primary">Estimate duties</button></form>{duty.result && <Result>{format.format(duty.result.amount)} estimated duties {duty.result.rate !== undefined && `(${duty.result.rate}%)`}</Result>}</article></div>
      <div className="col-lg-6"><article className="international-card"><FiPackage /><p className="eyebrow">ORDER TRACKING</p><h2>Follow your piece</h2><form onSubmit={findTracking}><input aria-label="Tracking reference" placeholder="Order or tracking reference" value={tracking.reference} onChange={(e) => setTracking({ ...tracking, reference: e.target.value })} required /><button className="btn btn-velora-primary">Track order</button></form>{tracking.result && <Result><strong>{tracking.result.status}</strong><br />{tracking.result.detail}</Result>}</article></div>
      <div className="col-lg-6"><article className="international-card"><FiMapPin /><p className="eyebrow">WAREHOUSE AVAILABILITY</p><h2>Closer to your door</h2><p>We route orders from the most considerate available location.</p><button type="button" className="btn btn-velora-secondary" onClick={checkWarehouse}>Check availability</button>{warehouse && <Result><strong>{warehouse.name}</strong><br />{warehouse.status} · {warehouse.dispatch}</Result>}</article></div>
    </div>
    <article className="size-guide mt-4"><FiMaximize /><div><p className="eyebrow">REGIONAL SIZE GUIDE</p><h2>Measure once, choose confidently.</h2><p>All measurements are in centimetres. Consider the garment’s intended ease before selecting your size.</p></div><div className="table-responsive"><table className="table"><thead><tr><th>Size</th><th>Bust</th><th>Waist</th><th>Hips</th></tr></thead><tbody>{sizeRows.map((row) => <tr key={row.label}><td>{row.label}</td><td>{row.bust}</td><td>{row.waist}</td><td>{row.hips}</td></tr>)}</tbody></table></div><FiBox className="size-guide-mark" /></article>
  </section>
}
