/** Demo products — images served from /public/catalog (always load, no API). */

const L = {
  shoeNike: '/catalog/shoe-nike.jpg',
  shoeRunner: '/catalog/shoe-runner.jpg',
  train: '/catalog/apparel-train.jpg',
  hoodie: '/catalog/apparel-hoodie.jpg',
  joggers: '/catalog/apparel-joggers.jpg',
  hero: '/catalog/hero-gym.jpg',
}

const sizes = ['S', 'M', 'L', 'XL']
const shoeSizes = ['US 8', 'US 9', 'US 10', 'US 11']

function img(path) {
  return { url: path, thumbnail_url: path, alt_text: '', is_primary: true }
}

function product({
  id, name, slug, brand, line, category, price, salePrice, featured, trending, isNew, photos,
}) {
  const images = photos.map((path, index) => ({ url: path, thumbnail_url: path, alt_text: name, is_primary: index === 0 }))
  const brandSlug = brand.toLowerCase().replace(/ & /g, '-').replace(/\s+/g, '-')
  return {
    id, name, slug,
    short_description: `${brand} men's ${category.toLowerCase()}.`,
    description: `Men's ${category.toLowerCase()} for training and street. Nike-style fit and feel.`,
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
    brand: { name: brand, slug: brandSlug },
    category: { name: category, slug: `mens-sport-${category.toLowerCase().replace(/\s+/g, '-')}` },
    images,
    colors: [{ name: 'Black', hex_code: '#111111' }],
    sizes: (line === 'footwear' ? shoeSizes : sizes).map((name) => ({ name, international_size: name })),
    variants: (line === 'footwear' ? shoeSizes : sizes).map((size, index) => ({
      id: id * 10 + index,
      sku: `DEMO-${id}-${index}`,
      size,
      color: 'Black',
    })),
    matched_product: null,
  }
}

const RAW = [
  product({ id: 1, name: 'Nike Air Max Pulse', slug: 'nike-air-max-pulse', brand: 'Nike', line: 'footwear', category: 'Lifestyle Sneakers', price: 150, salePrice: 127, featured: true, trending: true, isNew: true, photos: [L.shoeNike, L.shoeRunner] }),
  product({ id: 2, name: 'Nike Dri-FIT Legend Tee', slug: 'nike-dri-fit-legend-tee', brand: 'Nike', line: 'apparel', category: 'Training Tee', price: 35, featured: true, photos: [L.train, L.hoodie] }),
  product({ id: 3, name: 'Adidas Ultraboost Light', slug: 'adidas-ultraboost-light', brand: 'Adidas', line: 'footwear', category: 'Running Shoes', price: 190, trending: true, isNew: true, photos: [L.shoeRunner, L.shoeNike] }),
  product({ id: 4, name: 'Adidas Essentials Hoodie', slug: 'adidas-essentials-hoodie', brand: 'Adidas', line: 'apparel', category: 'Hoodie', price: 65, salePrice: 52, featured: true, photos: [L.hoodie, L.joggers] }),
  product({ id: 5, name: 'Jordan Retro 4', slug: 'jordan-retro-4', brand: 'Jordan', line: 'footwear', category: 'Basketball Shoes', price: 215, featured: true, trending: true, photos: [L.shoeNike, L.shoeRunner] }),
  product({ id: 6, name: 'Gymshark Vital Tee', slug: 'gymshark-vital-tee', brand: 'Gymshark', line: 'apparel', category: 'Compression Top', price: 40, trending: true, photos: [L.train, L.joggers] }),
  product({ id: 7, name: 'Puma RS-X', slug: 'puma-rs-x', brand: 'Puma', line: 'footwear', category: 'Lifestyle Sneakers', price: 110, salePrice: 88, photos: [L.shoeRunner, L.shoeNike] }),
  product({ id: 8, name: 'UA Rival Joggers', slug: 'ua-rival-joggers', brand: 'Under Armour', line: 'apparel', category: 'Joggers', price: 60, featured: true, photos: [L.joggers, L.hoodie] }),
  product({ id: 9, name: 'New Balance 574', slug: 'new-balance-574', brand: 'New Balance', line: 'footwear', category: 'Lifestyle Sneakers', price: 90, isNew: true, photos: [L.shoeRunner, L.shoeNike] }),
  product({ id: 10, name: 'Nike Pro Shorts', slug: 'nike-pro-shorts', brand: 'Nike', line: 'apparel', category: 'Training Shorts', price: 45, trending: true, photos: [L.train, L.joggers] }),
  product({ id: 11, name: 'Nike Metcon 9', slug: 'nike-metcon-9', brand: 'Nike', line: 'footwear', category: 'Training Shoes', price: 140, featured: true, photos: [L.shoeNike, L.shoeRunner] }),
  product({ id: 12, name: 'Adidas Tiro Jacket', slug: 'adidas-tiro-jacket', brand: 'Adidas', line: 'apparel', category: 'Track Jacket', price: 85, photos: [L.hoodie, L.train] }),
]

RAW[0].matched_product = { id: RAW[1].id, name: RAW[1].name, slug: RAW[1].slug, price: RAW[1].price, sale_price: RAW[1].sale_price, catalog_line: RAW[1].catalog_line, brand: RAW[1].brand, images: RAW[1].images }
RAW[1].matched_product = { id: RAW[0].id, name: RAW[0].name, slug: RAW[0].slug, price: RAW[0].price, sale_price: RAW[0].sale_price, catalog_line: RAW[0].catalog_line, brand: RAW[0].brand, images: RAW[0].images }

export const DEMO_PRODUCTS = RAW

export const DEMO_FILTERS = {
  brands: [...new Map(DEMO_PRODUCTS.map((p) => [p.brand.slug, p.brand])).values()].sort((a, b) => a.name.localeCompare(b.name)),
  colors: [{ name: 'Black', slug: 'black', hex_code: '#111111' }],
  sizes: shoeSizes.concat(sizes).map((name) => ({ name, slug: name.toLowerCase().replace(/\s+/g, '-'), international_size: name })),
  men_categories: [...new Map(DEMO_PRODUCTS.map((p) => [p.category.slug, p.category])).values()],
  catalog_lines: ['apparel', 'footwear'],
  materials: ['Rubber outsole', 'Performance mesh'],
  fabrics: ['Dri-FIT polyester', 'Synthetic upper'],
  occasions: ['All season'],
  price_range: { min: 35, max: 215 },
  in_stock_count: DEMO_PRODUCTS.length,
}

function matches(product, params = {}) {
  if (params.line && product.catalog_line !== params.line) return false
  if (params.brand && product.brand.slug !== params.brand) return false
  if (params.brands && !String(params.brands).split(',').includes(product.brand.slug)) return false
  if (params.category && product.category.slug !== params.category) return false
  if (params.featured === '1' && !product.is_featured) return false
  if (params.new === '1' && !product.is_new_arrival) return false
  if (params.sale === '1' && !product.sale_price) return false
  if (params.q) {
    const term = String(params.q).toLowerCase()
    if (!product.name.toLowerCase().includes(term) && !product.brand.name.toLowerCase().includes(term)) return false
  }
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
  return {
    items: filtered.slice(start, start + perPage),
    meta: { current_page: page, last_page: Math.max(1, Math.ceil(filtered.length / perPage)), per_page: perPage, total: filtered.length },
  }
}

export function getDemoProduct(slug) {
  return DEMO_PRODUCTS.find((p) => p.slug === slug) || null
}

export { L as DEMO_IMAGES }
