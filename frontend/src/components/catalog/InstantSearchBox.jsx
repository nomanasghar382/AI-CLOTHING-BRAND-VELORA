import { memo, useEffect, useRef, useState } from 'react'
import { useNavigate } from 'react-router-dom'
import { FiMic, FiMicOff, FiSearch } from 'react-icons/fi'
import useInstantSearch from '../../hooks/useInstantSearch'

function InstantSearchBox({ initial = '', onSubmit }) {
  const navigate = useNavigate()
  const panelRef = useRef(null)
  const recognitionRef = useRef(null)
  const { query, setQuery, suggestions, loading } = useInstantSearch(initial)
  const [open, setOpen] = useState(false)
  const [listening, setListening] = useState(false)
  const [voiceSupported] = useState(() => typeof window !== 'undefined' && ('SpeechRecognition' in window || 'webkitSpeechRecognition' in window))

  useEffect(() => {
    const onClick = (event) => {
      if (!panelRef.current?.contains(event.target)) setOpen(false)
    }
    document.addEventListener('mousedown', onClick)
    return () => document.removeEventListener('mousedown', onClick)
  }, [])

  useEffect(() => () => recognitionRef.current?.stop?.(), [])

  const submit = (value = query) => {
    const term = value.trim()
    if (!term) return
    if (onSubmit) onSubmit(term)
    else navigate(`/catalog?q=${encodeURIComponent(term)}&gender=men`)
    setOpen(false)
  }

  const toggleVoice = () => {
    if (!voiceSupported) return
    if (listening) {
      recognitionRef.current?.stop()
      setListening(false)
      return
    }
    const SpeechRecognition = window.SpeechRecognition || window.webkitSpeechRecognition
    const recognition = new SpeechRecognition()
    recognition.lang = 'en-US'
    recognition.interimResults = false
    recognition.maxAlternatives = 1
    recognition.onresult = (event) => {
      const transcript = event.results[0][0].transcript
      setQuery(transcript)
      submit(transcript)
      setListening(false)
    }
    recognition.onerror = () => setListening(false)
    recognition.onend = () => setListening(false)
    recognitionRef.current = recognition
    recognition.start()
    setListening(true)
    setOpen(true)
  }

  return (
    <div className="instant-search" ref={panelRef}>
      <form className="input-group" onSubmit={(event) => { event.preventDefault(); submit() }}>
        <span className="input-group-text velora-input border-end-0"><FiSearch aria-hidden="true" /></span>
        <input
          className="form-control velora-input border-start-0 border-end-0"
          value={query}
          onChange={(event) => { setQuery(event.target.value); setOpen(true) }}
          onFocus={() => setOpen(true)}
          placeholder="Search gymshark hoodie, nike joggers, training shoes..."
          aria-label="Search sportswear catalog"
          aria-expanded={open}
          aria-controls="instant-search-panel"
          autoComplete="off"
        />
        {voiceSupported && (
          <button
            type="button"
            className={`input-group-text velora-input border-start-0 ${listening ? 'text-danger' : ''}`}
            onClick={toggleVoice}
            aria-label={listening ? 'Stop voice search' : 'Start voice search'}
            title={listening ? 'Listening…' : 'Voice search'}
          >
            {listening ? <FiMicOff aria-hidden="true" /> : <FiMic aria-hidden="true" />}
          </button>
        )}
      </form>
      {open && (
        <div className="instant-search-panel" id="instant-search-panel" role="listbox">
          {listening && <p className="instant-search-meta">Listening… say a brand, style, or sport.</p>}
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
