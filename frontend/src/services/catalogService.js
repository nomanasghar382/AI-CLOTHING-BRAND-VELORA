import apiClient from './apiClient'
import { DEMO_FILTERS, getDemoProduct, queryDemoProducts } from '../data/demoCatalog'
import { getCatalogMode, setCatalogMode } from './catalogMode'

const ok = (data) => Promise.resolve({ data: { data } })

async function liveOrDemo(liveCall, demoCall) {
  try {
    const response = await liveCall()
    const total = response.data?.data?.meta?.total ?? response.data?.data?.items?.length ?? 1
    setCatalogMode(total > 0 ? 'live' : 'demo')
    if (total > 0) return response
    return demoCall()
  } catch {
    setCatalogMode('demo')
    return demoCall()
  }
}

export const catalogService = {
  products: (params) => liveOrDemo(
    () => apiClient.get('/products', { params }),
    () => ok(queryDemoProducts(params)),
  ),
  product: (slug) => liveOrDemo(
    () => apiClient.get(`/products/${slug}`),
    () => {
      const item = getDemoProduct(slug)
      if (!item) return Promise.reject(new Error('not found'))
      return ok(item)
    },
  ),
  categories: () => liveOrDemo(
    () => apiClient.get('/categories'),
    () => ok(DEMO_FILTERS.men_categories.map((c) => ({ ...c, children: [] }))),
  ),
  filters: () => liveOrDemo(
    () => apiClient.get('/catalog/filters'),
    () => ok(DEMO_FILTERS),
  ),
  isDemo: () => getCatalogMode() === 'demo',
}
