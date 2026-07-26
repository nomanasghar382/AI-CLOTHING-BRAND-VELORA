import { FEATURED_EDITORIAL } from '../constants/veloraIcons'

const sportImage = (index, width = 700) => FEATURED_EDITORIAL[index % FEATURED_EDITORIAL.length].image.replace(/w=\d+/, `w=${width}`)

export const creators = [
  { id: 'jax', name: 'Jax Rivera', handle: '@jaxfit', specialty: 'Gym & training fits', followers: '22.4K', image: sportImage(2), bio: 'Heavy lifts, clean fits — Nike & Gymshark only.' },
  { id: 'mike', name: 'Mike Chen', handle: '@mikecourt', specialty: 'Basketball & court heat', followers: '18.2K', image: sportImage(3), bio: 'Jordan rotations and game-day layers.' },
  { id: 'leo', name: 'Leo Santos', handle: '@leosprint', specialty: 'Running & streetwear', followers: '15.1K', image: sportImage(0), bio: 'Marathon training fits with street-ready sneakers.' },
]

export const looks = [
  { id: 'look-1', title: 'Gym shark session', creator: creators[0], image: sportImage(2), likes: 412, tags: ['Gymshark', 'Training'] },
  { id: 'look-2', title: 'Jordan court night', creator: creators[1], image: sportImage(3), likes: 296, tags: ['Jordan', 'Basketball'] },
  { id: 'look-3', title: 'Nike street sprint', creator: creators[2], image: sportImage(0), likes: 347, tags: ['Nike', 'Run'] },
  { id: 'look-4', title: 'Adidas recovery day', creator: creators[0], image: sportImage(1), likes: 228, tags: ['Adidas', 'Rest day'] },
]

export const lookProducts = [
  { id: 'look-product-1', name: 'Nike Tech Fleece Hoodie', brand: 'Nike', price: 128, image: sportImage(2, 500) },
  { id: 'look-product-2', name: 'Jordan 1 Mid Heat', brand: 'Jordan', price: 142, image: sportImage(3, 500) },
  { id: 'look-product-3', name: 'Adidas Ultraboost Run', brand: 'Adidas', price: 176, image: sportImage(1, 500) },
]

export const communityService = {
  creators: () => Promise.resolve(creators),
  looks: () => Promise.resolve(looks),
  products: () => Promise.resolve(lookProducts),
}
