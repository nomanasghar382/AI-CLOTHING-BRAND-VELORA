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
  product({ id: 1, name: "Nike Air Force 1 '07 LV8 — Men's Shoes", slug: 'air-force-1-07-lv8-mens-shoes', brand: 'Nike', line: 'footwear', category: 'Lifestyle Sneakers', price: 130, featured: true, trending: true, isNew: true, photos: [L.shoeNike, L.shoeRunner] }),
  product({ id: 2, name: 'Nike Sportswear Club — Men\'s Pullover Hoodie', slug: 'sportswear-club-mens-pullover-hoodie', brand: 'Nike', line: 'apparel', category: 'Hoodie', price: 63.97, featured: true, photos: [L.hoodie, L.joggers] }),
  product({ id: 3, name: 'Air Jordan 1 Zoom CMFT — Shoes', slug: 'air-jordan-1-zoom-cmft-shoes', brand: 'Nike', line: 'footwear', category: 'Lifestyle Sneakers', price: 150, trending: true, isNew: true, photos: [L.shoeNike, L.shoeRunner] }),
  product({ id: 4, name: 'Nike Air Max 90 SE — Men\'s Shoes', slug: 'air-max-90-se-mens-shoes', brand: 'Nike', line: 'footwear', category: 'Running Shoes', price: 119.97, featured: true, trending: true, photos: [L.shoeRunner, L.shoeNike] }),
  product({ id: 5, name: 'Zion 2 — Men\'s Basketball Shoes', slug: 'zion-2-mens-basketball-shoes', brand: 'Nike', line: 'footwear', category: 'Basketball Shoes', price: 119.97, featured: true, photos: [L.shoeNike, L.shoeRunner] }),
  product({ id: 6, name: 'Nike Sportswear — Men\'s Logo T-Shirt', slug: 'sportswear-mens-logo-t-shirt', brand: 'Nike', line: 'apparel', category: 'Training Tee', price: 28.97, trending: true, photos: [L.train, L.hoodie] }),
  product({ id: 7, name: 'Nike Air Pegasus 83 Premium — Men\'s Shoes', slug: 'air-pegasus-83-premium-mens-shoes', brand: 'Nike', line: 'footwear', category: 'Running Shoes', price: 89.97, salePrice: 79, photos: [L.shoeRunner, L.shoeNike] }),
  product({ id: 8, name: 'Chelsea FC Strike — Men\'s Nike Dri-FIT Soccer Track Jacket', slug: 'chelsea-fc-strike-mens-track-jacket', brand: 'Nike', line: 'apparel', category: 'Track Jacket', price: 90, featured: true, photos: [L.hoodie, L.train] }),
  product({ id: 9, name: 'Nike Offline Pack — Men\'s Shoes', slug: 'offline-pack-mens-shoes', brand: 'Nike', line: 'footwear', category: 'Lifestyle Sneakers', price: 92.97, isNew: true, photos: [L.shoeRunner, L.shoeNike] }),
  product({ id: 10, name: 'Nike Therma Crucial Catch (NFL Miami Dolphins) — Men\'s Pullover Hoodie', slug: 'therma-crucial-catch-miami-dolphins-hoodie', brand: 'Nike', line: 'apparel', category: 'Hoodie', price: 85, trending: true, photos: [L.hoodie, L.joggers] }),
  product({ id: 11, name: 'NFL Miami Dolphins (Mike Gesicki) — Men\'s Game Football Jersey', slug: 'miami-dolphins-mike-gesicki-jersey', brand: 'Nike', line: 'apparel', category: 'Football Jersey', price: 130, featured: true, photos: [L.train, L.hoodie] }),
  product({ id: 12, name: 'Nike Dri-FIT Vapor — Men\'s Golf Polo', slug: 'dri-fit-vapor-mens-golf-polo', brand: 'Nike', line: 'apparel', category: 'Training Tee', price: 55.97, photos: [L.train, L.joggers] }),
  product({ id: 13, name: 'Adidas Ultraboost Light', slug: 'adidas-ultraboost-light', brand: 'Adidas', line: 'footwear', category: 'Running Shoes', price: 190, trending: true, photos: [L.shoeRunner, L.shoeNike] }),
  product({ id: 14, name: 'Adidas Essentials Hoodie', slug: 'adidas-essentials-hoodie', brand: 'Adidas', line: 'apparel', category: 'Hoodie', price: 65, salePrice: 52, photos: [L.hoodie, L.joggers] }),
  product({ id: 15, name: 'Gymshark Vital Tee', slug: 'gymshark-vital-tee', brand: 'Gymshark', line: 'apparel', category: 'Compression Top', price: 40, photos: [L.train, L.joggers] }),
  product({ id: 16, name: 'Puma RS-X', slug: 'puma-rs-x', brand: 'Puma', line: 'footwear', category: 'Lifestyle Sneakers', price: 110, salePrice: 88, photos: [L.shoeRunner, L.shoeNike] }),
  product({ id: 17, name: 'UA Rival Joggers', slug: 'ua-rival-joggers', brand: 'Under Armour', line: 'apparel', category: 'Joggers', price: 60, photos: [L.joggers, L.hoodie] }),
  product({ id: 18, name: 'New Balance 574', slug: 'new-balance-574', brand: 'New Balance', line: 'footwear', category: 'Lifestyle Sneakers', price: 90, photos: [L.shoeRunner, L.shoeNike] }),
  product({ id: 19, name: 'Nike Pro — Men\'s Tights', slug: 'pro-mens-tights', brand: 'Nike', line: 'apparel', category: 'Joggers', price: 24.97, trending: true, photos: [L.joggers, L.train] }),
  product({ id: 20, name: 'Nike Dri-FIT Stretch (NFL Chicago Bears) — Men\'s Shorts', slug: 'dri-fit-stretch-chicago-bears-shorts', brand: 'Nike', line: 'apparel', category: 'Training Shorts', price: 45, photos: [L.train, L.joggers] }),
  product({ id: 21, name: 'Charlotte Hornets — Men\'s Jordan NBA T-Shirt', slug: 'charlotte-hornets-jordan-nba-t-shirt', brand: 'Nike', line: 'apparel', category: 'Training Tee', price: 35, photos: [L.train, L.hoodie] }),
  product({ id: 22, name: 'Nike Air Jordan XXXVI Low Luka PF — Men\'s Basketball Shoes', slug: 'air-jordan-xxxvi-low-luka-pf', brand: 'Nike', line: 'footwear', category: 'Basketball Shoes', price: 175, featured: true, photos: [L.shoeNike, L.shoeRunner] }),
  product({ id: 23, name: 'Nike Brasilia — Printed Training Backpack (30L)', slug: 'brasilia-printed-training-backpack', brand: 'Nike', line: 'apparel', category: 'Training Tee', price: 50, photos: [L.train, L.hoodie] }),
  product({ id: 24, name: 'Nike Essential — Ball Pump', slug: 'essential-ball-pump', brand: 'Nike', line: 'apparel', category: 'Training Tee', price: 12, photos: [L.train] }),
]

RAW[0].matched_product = { id: RAW[1].id, name: RAW[1].name, slug: RAW[1].slug, price: RAW[1].price, sale_price: RAW[1].sale_price, catalog_line: RAW[1].catalog_line, brand: RAW[1].brand, images: RAW[1].images }
RAW[1].matched_product = { id: RAW[0].id, name: RAW[0].name, slug: RAW[0].slug, price: RAW[0].price, sale_price: RAW[0].sale_price, catalog_line: RAW[0].catalog_line, brand: RAW[0].brand, images: RAW[0].images }
RAW[2].matched_product = { id: RAW[7].id, name: RAW[7].name, slug: RAW[7].slug, price: RAW[7].price, sale_price: RAW[7].sale_price, catalog_line: RAW[7].catalog_line, brand: RAW[7].brand, images: RAW[7].images }
RAW[7].matched_product = { id: RAW[2].id, name: RAW[2].name, slug: RAW[2].slug, price: RAW[2].price, sale_price: RAW[2].sale_price, catalog_line: RAW[2].catalog_line, brand: RAW[2].brand, images: RAW[2].images }

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
