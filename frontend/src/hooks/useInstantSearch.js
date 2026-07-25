import { useEffect, useState } from 'react'
import { searchService } from '../services/searchService'
import useDebouncedValue from './useDebouncedValue'

export default function useInstantSearch(initial = '') {
  const [query, setQuery] = useState(initial)
  const debounced = useDebouncedValue(query)
  const [suggestions, setSuggestions] = useState({ suggestions: [], popular: [], recent: [] })
  const [loading, setLoading] = useState(false)

  useEffect(() => {
    let active = true
    setLoading(true)
    searchService.suggestions(debounced).then(({ data }) => {
      if (active) setSuggestions(data.data)
    }).finally(() => active && setLoading(false))
    return () => { active = false }
  }, [debounced])

  return { query, setQuery, suggestions, loading }
}
