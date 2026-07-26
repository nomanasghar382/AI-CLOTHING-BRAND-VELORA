import { useState } from 'react'
import { FiChevronDown, FiGlobe } from 'react-icons/fi'
import { markets } from '../../config/markets'
import useInternational from '../../hooks/useInternational'

export default function MarketSelector() {
  const [open, setOpen] = useState(false)
  const { country, currency, language, updatePreferences } = useInternational()
  const selected = markets.find((market) => market.country === country)
  return <div className="market-selector">
    <button type="button" className="market-trigger" onClick={() => setOpen((value) => !value)} aria-expanded={open}>
      <FiGlobe /> <span>{selected?.country} · {currency}</span><FiChevronDown />
    </button>
    {open && <div className="market-menu" role="dialog" aria-label="Regional preferences">
      <p className="eyebrow mb-2">YOUR MARKET</p>
      {markets.map((market) => <button type="button" className={market.country === country ? 'active' : ''} key={market.country} onClick={() => { updatePreferences({ country: market.country }); setOpen(false) }}>
        <span>{market.label}</span><small>{market.currency} · {market.language.toUpperCase()}</small>
      </button>)}
      <label className="small mt-2">Language<select value={language} onChange={(event) => updatePreferences({ language: event.target.value })}><option value="en">English</option><option value="ar">العربية</option><option value="fr">Français</option></select></label>
    </div>}
  </div>
}
