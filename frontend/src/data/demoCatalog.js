const u = (id, w = 1080) =>
  `https://images.unsplash.com/${id}?auto=format&fit=crop&w=${w}&h=${Math.round(w * 1.25)}&q=88&dpr=2`

const sizes = ['S', 'M', 'L', 'XL']
const shoeSizes = ['US 8', 'US 9', 'US 10', 'US 11']

function product({
  id, name, slug, brand, line, category, price, salePrice, featured, trending, isNew, imageIds, match,
}) {
  const images = imageIds.map((photoId, index) => ({
    url: u(photoId),
    thumbnail_url: u(photoId, 540),
    alt_text: name,
    is_primary: index === 0,
  }))
  return {
    id,
    name,
    slug,
    short_description: `${brand} men's ${category.toLowerCase()} — gym to street.`,
    description: `Performance ${category.toLowerCase()} built for training and everyday wear. Pairs with matching kicks in the same colorway.`,
    sku: `DEMO-${id}`,
    price,
    sale_price: salePrice ?? null,
    discount_percent: salePrice ? Math.round((1 - salePrice / price) * 100) : 0,
    fabric: line === 'footwear' ? 'Synthetic upper' : 'Dri-FIT polyester',
    material: line === 'footwear' ? 'Rubber outsole' : 'Performance mesh',
    fit_type: 'Athletic',
    coverage_level: 'Sport',
    gender: 'men',
    season: 'All season',
    stock_quantity: 42,
    catalog_line: line,
    is_featured: !!featured,
    is_trending: !!trending,
    is_new_arrival: !!isNew,
    brand: { name: brand, slug: brand.toLowerCase().replace(/ & /g, '-').replace(/\s+/g, '-') },
    category: { name: category, slug: `mens-sport-${category.toLowerCase().replace(/\s+/g, '-')}` },
    images,
    colors: [{ name: 'Black', hex_code: '#111827' }],
    sizes: (line === 'footwear' ? shoeSizes : sizes).map((name) => ({ name, international_size: name })),
    variants: (line === 'footwear' ? shoeSizes : sizes).map((size, index) => ({
      id: id * 10 + index,
      sku: `DEMO-${id}-${index}`,
      size,
      color: 'Black',
    })),
    matched_product: match || null,
  }
}

const RAW = [
  product({ id: 1, name: 'Nike Air Max Pulse', slug: 'nike-air-max-pulse', brand: 'Nike', line: 'footwear', category: 'Lifestyle Sneakers', price: 150, salePrice: 127, featured: true, trending: true, isNew: true, imageIds: ['photo-1542291026-7eec264c27ff', 'photo-1606107557195-0e29a4b5b4aa', 'photo-1460353589841-049ca37d260b'] }),
  product({ id: 2, name: 'Nike Dri-FIT Legend Tee', slug: 'nike-dri-fit-legend-tee', brand: 'Nike', line: 'apparel', category: 'Training Tee', price: 35, featured: true, imageIds: ['photo-1571019614242-c5c5dee9f50b', 'photo-1534438327276-14e5300c3a48'] }),
  product({ id: 3, name: 'Adidas Ultraboost Light', slug: 'adidas-ultraboost-light', brand: 'Adidas', line: 'footwear', category: 'Running Shoes', price: 190, trending: true, isNew: true, imageIds: ['photo-1605348532761-9731b25a9ad8', 'photo-1542291026-7eec264c27ff'] }),
  product({ id: 4, name: 'Adidas Essentials Hoodie', slug: 'adidas-essentials-hoodie', brand: 'Adidas', line: 'apparel', category: 'Hoodie', price: 65, salePrice: 52, featured: true, imageIds: ['photo-1556821840-3a63f95609a7', 'photo-1620799140408-edc2dcb6d122'] }),
  product({ id: 5, name: 'Jordan Retro 4', slug: 'jordan-retro-4', brand: 'Jordan', line: 'footwear', category: 'Basketball Shoes', price: 215, featured: true, trending: true, imageIds: ['photo-1551107696-a4b0c96065fb', 'photo-1595950653106-6c9ebd614d3a'] }),
  product({ id: 6, name: 'Gymshark Vital Seamless Tee', slug: 'gymshark-vital-tee', brand: 'Gymshark', line: 'apparel', category: 'Compression Top', price: 40, trending: true, imageIds: ['photo-1583454110551-21f2d2b999cf', 'photo-1517836357463-d25dfeac3438'] }),
  product({ id: 7, name: 'Puma RS-X Reinvention', slug: 'puma-rs-x', brand: 'Puma', line: 'footwear', category: 'Lifestyle Sneakers', price: 110, salePrice: 88, imageIds: ['photo-1549298916-b41d501d3772', 'photo-1460353589841-049ca37d260b'] }),
  product({ id: 8, name: 'Under Armour Rival Fleece Joggers', slug: 'ua-rival-joggers', brand: 'Under Armour', line: 'apparel', category: 'Joggers', price: 60, featured: true, imageIds: ['photo-1518611012118-696072aa579a', 'photo-1594381898411-846e7d193883'] }),
  product({ id: 9, name: 'New Balance 574 Core', slug: 'new-balance-574', brand: 'New Balance', line: 'footwear', category: 'Lifestyle Sneakers', price: 90, isNew: true, imageIds: ['photo-1606107557195-0e29a4b5b4aa', 'photo-1549298916-b41d501d3772'] }),
  product({ id: 10, name: 'Nike Pro Compression Shorts', slug: 'nike-pro-shorts', brand: 'Nike', line: 'apparel', category: 'Training Shorts', price: 45, trending: true, imageIds: ['photo-1620799139839-3bcdc4bf0a35', 'photo-1594736797933-d0401ba2a2b9'] }),
  product({ id: 11, name: 'Nike Metcon 9', slug: 'nike-metcon-9', brand: 'Nike', line: 'footwear', category: 'Training Shoes', price: 140, featured: true, imageIds: ['photo-1556906781-9a412961c28c', 'photo-1608231387042-66d1773070a5'] }),
  product({ id: 12, name: 'Adidas Tiro Track Jacket', slug: 'adidas-tiro-jacket', brand: 'Adidas', line: 'apparel', category: 'Track Jacket', price: 85, imageIds: ['photo-1591045893001-91aecb16a9be', 'photo-1617137968427-85924c800a22'] }),
  product({ id: 13, name: 'Jordan Flight Heritage Jersey', slug: 'jordan-flight-jersey', brand: 'Jordan', line: 'apparel', category: 'Basketball Jersey', price: 75, salePrice: 60, imageIds: ['photo-1546519638-68e109498ffc', 'photo-1519861537503-f9a1d01d99c4'] }),
  product({ id: 14, name: 'Gymshark Crest Joggers', slug: 'gymshark-crest-joggers', brand: 'Gymshark', line: 'apparel', category: 'Joggers', price: 55, isNew: true, imageIds: ['photo-1556821840-3a63f95609a7', 'photo-1518611012118-696072aa579a'] }),
  product({ id: 15, name: 'Adidas Adizero Running', slug: 'adidas-adizero', brand: 'Adidas', line: 'footwear', category: 'Running Shoes', price: 160, trending: true, imageIds: ['photo-1542291026-7eec264c27ff', 'photo-1605348532761-9731b25a9ad8'] }),
  product({ id: 16, name: 'Nike Windrunner Jacket', slug: 'nike-windrunner', brand: 'Nike', line: 'apparel', category: 'Windbreaker', price: 120, featured: true, imageIds: ['photo-1611312440404-42a3fad756fd', 'photo-1556828680-fc6a0a84cd8a'] }),
  product({ id: 17, name: 'Puma Deviate Nitro', slug: 'puma-deviate-nitro', brand: 'Puma', line: 'footwear', category: 'Running Shoes', price: 130, imageIds: ['photo-1595950653106-6c9ebd614d3a', 'photo-1542291026-7eec264c27ff'] }),
  product({ id: 18, name: 'Nike Sportswear Club Fleece Hoodie', slug: 'nike-club-fleece-hoodie', brand: 'Nike', line: 'apparel', category: 'Hoodie', price: 70, salePrice: 56, trending: true, imageIds: ['photo-1620799140408-edc2dcb6d122', 'photo-1556821840-3a63f95609a7'] }),
  product({ id: 19, name: 'Under Armour Curry Flow', slug: 'ua-curry-flow', brand: 'Under Armour', line: 'footwear', category: 'Basketball Shoes', price: 170, isNew: true, imageIds: ['photo-1551107696-a4b0c96065fb', 'photo-1556906781-9a412961c28c'] }),
  product({ id: 20, name: 'Adidas Own The Run Tank', slug: 'adidas-run-tank', brand: 'Adidas', line: 'apparel', category: 'Running Tank', price: 30, imageIds: ['photo-1571019613454-1cb2f99b2d8b', 'photo-1581009146145-b5ef050c2df1'] }),
  product({ id: 21, name: 'Nike Victori One Slides', slug: 'nike-victori-slides', brand: 'Nike', line: 'footwear', category: 'Sport Slides', price: 35, salePrice: 28, imageIds: ['photo-1603487747361-f5e1ab5ab8fc', 'photo-1606107557195-0e29a4b5b4aa'] }),
  product({ id: 22, name: 'Gymshark Arrival 5" Shorts', slug: 'gymshark-arrival-shorts', brand: 'Gymshark', line: 'apparel', category: 'Training Shorts', price: 38, featured: true, imageIds: ['photo-1594736797933-d0401ba2a2b9', 'photo-1620799139839-3bcdc4bf0a35'] }),
  product({ id: 23, name: 'New Balance Fresh Foam X', slug: 'new-balance-fresh-foam', brand: 'New Balance', line: 'footwear', category: 'Running Shoes', price: 155, trending: true, imageIds: ['photo-1608231387042-66d1773070a5', 'photo-1605348532761-9731b25a9ad8'] }),
  product({ id: 24, name: 'Puma Training Essential Tee', slug: 'puma-training-tee', brand: 'Puma', line: 'apparel', category: 'Training Tee', price: 32, isNew: true, imageIds: ['photo-1534438327276-14e5300c3a48', 'photo-1571019614242-c5c5dee9f50b'] }),
]

// Link a few apparel ↔ shoe pairs for "matching kicks"
RAW[1].matched_product = {
  id: RAW[0].id,
  name: RAW[0].name,
  slug: RAW[0].slug,
  price: RAW[0].price,
  sale_price: RAW[0].sale_price,
  catalog_line: RAW[0].catalog_line,
  brand: RAW[0].brand,
  images: RAW[0].images,
}
RAW[0].matched_product = {
  id: RAW[1].id,
  name: RAW[1].name,
  slug: RAW[1].slug,
  price: RAW[1].price,
  sale_price: RAW[1].sale_price,
  catalog_line: RAW[1].catalog_line,
  brand: RAW[1].brand,
  images: RAW[1].images,
}

export const DEMO_PRODUCTS = RAW

export const DEMO_FILTERS = {
  brands: [...new Map(DEMO_PRODUCTS.map((p) => [p.brand.slug, p.brand])).values()].sort((a, b) => a.name.localeCompare(b.name)),
  colors: [{ name: 'Black', slug: 'black', hex_code: '#111827' }],
  sizes: shoeSizes.concat(sizes).map((name) => ({ name, slug: name.toLowerCase().replace(/\s+/g, '-'), international_size: name })),
  men_categories: [...new Map(DEMO_PRODUCTS.map((p) => [p.category.slug, p.category])).values()],
  catalog_lines: ['apparel', 'footwear'],
  materials: ['Rubber outsole', 'Performance mesh'],
  fabrics: ['Dri-FIT polyester', 'Synthetic upper'],
  occasions: ['All season'],
  price_range: { min: 28, max: 215 },
  in_stock_count: DEMO_PRODUCTS.length,
}

function matches(product, params = {}) {
  if (params.gender && params.gender !== 'men') return false
  if (params.line && product.catalog_line !== params.line) return false
  if (params.brand && product.brand.slug !== params.brand) return false
  if (params.brands) {
    const list = String(params.brands).split(',')
    if (!list.includes(product.brand.slug)) return false
  }
  if (params.category && product.category.slug !== params.category) return false
  if (params.featured === '1' && !product.is_featured) return false
  if (params.new === '1' && !product.is_new_arrival) return false
  if (params.sale === '1' && !product.sale_price) return false
  if (params.q) {
    const term = String(params.q).toLowerCase()
    if (!product.name.toLowerCase().includes(term) && !product.brand.name.toLowerCase().includes(term)) return false
  }
  if (params.min_price && product.price < Number(params.min_price)) return false
  if (params.max_price && product.price > Number(params.max_price)) return false
  return true
}

function sortItems(items, sort = 'newest') {
  const list = [...items]
  switch (sort) {
    case 'price_asc': return list.sort((a, b) => (a.sale_price || a.price) - (b.sale_price || b.price))
    case 'price_desc': return list.sort((a, b) => (b.sale_price || b.price) - (a.sale_price || a.price))
    case 'popular': return list.sort((a, b) => Number(b.is_trending) - Number(a.is_trending))
    case 'alphabetical': return list.sort((a, b) => a.name.localeCompare(b.name))
    default: return list.sort((a, b) => b.id - a.id)
  }
}

export function queryDemoProducts(params = {}) {
  const perPage = Math.min(Math.max(Number(params.per_page) || 12, 1), 48)
  const page = Math.max(Number(params.page) || 1, 1)
  const filtered = sortItems(DEMO_PRODUCTS.filter((p) => matches(p, params)), params.sort)
  const start = (page - 1) * perPage
  const items = filtered.slice(start, start + perPage)
  return {
    items,
    meta: {
      current_page: page,
      last_page: Math.max(1, Math.ceil(filtered.length / perPage)),
      per_page: perPage,
      total: filtered.length,
    },
  }
}

export function getDemoProduct(slug) {
  return DEMO_PRODUCTS.find((p) => p.slug === slug) || null
}
