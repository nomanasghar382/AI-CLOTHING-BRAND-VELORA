import { memo, useEffect, useRef, useState } from 'react'
import { useNavigate } from 'react-router-dom'
import { FiSearch } from 'react-icons/fi'
import useInstantSearch from '../../hooks/useInstantSearch'

function InstantSearchBox({ initial = '', onSubmit }) {
  const navigate = useNavigate()
  const panelRef = useRef(null)
  const { query, setQuery, suggestions, loading } = useInstantSearch(initial)
  const [open, setOpen] = useState(false)

  useEffect(() => {
    const onClick = (event) => {
      if (!panelRef.current?.contains(event.target)) setOpen(false)
    }
    document.addEventListener('mousedown', onClick)
    return () => document.removeEventListener('mousedown', onClick)
  }, [])

  const submit = (value = query) => {
    const term = value.trim()
    if (!term) return
    if (onSubmit) onSubmit(term)
    else navigate(`/catalog?q=${encodeURIComponent(term)}`)
    setOpen(false)
  }

  return (
    <div className="instant-search" ref={panelRef}>
      <form className="input-group" onSubmit={(event) => { event.preventDefault(); submit() }}>
        <span className="input-group-text velora-input border-end-0"><FiSearch aria-hidden="true" /></span>
        <input
          className="form-control velora-input border-start-0"
          value={query}
          onChange={(event) => { setQuery(event.target.value); setOpen(true) }}
          onFocus={() => setOpen(true)}
          placeholder="Search abayas, thobes, hijabs..."
          aria-label="Search catalog"
          aria-expanded={open}
          aria-controls="instant-search-panel"
          autoComplete="off"
        />
      </form>
      {open && (
        <div className="instant-search-panel" id="instant-search-panel" role="listbox">
          {loading && <p className="instant-search-meta">Searching...</p>}
          {!!suggestions.suggestions?.length && (
            <section>
              <p className="instant-search-meta">Suggestions</p>
              {suggestions.suggestions.map((item) => (
                <button type="button" key={`suggest-${item}`} className="instant-search-item" onClick={() => submit(item)}>{item}</button>
              ))}
            </section>
          )}
          {!!suggestions.recent?.length && (
            <section>
              <p className="instant-search-meta">Recent</p>
              {suggestions.recent.map((item) => (
                <button type="button" key={`recent-${item}`} className="instant-search-item" onClick={() => submit(item)}>{item}</button>
              ))}
            </section>
          )}
          {!!suggestions.popular?.length && (
            <section>
              <p className="instant-search-meta">Popular</p>
              {suggestions.popular.map((item) => (
                <button type="button" key={`popular-${item}`} className="instant-search-item" onClick={() => submit(item)}>{item}</button>
              ))}
            </section>
          )}
        </div>
      )}
    </div>
  )
}

export default memo(InstantSearchBox)
