/** Featured Velora Icons for homepage and demo pages. */
const pexels = (id, width = 720) =>
  `https://images.pexels.com/photos/${id}/pexels-photo-${id}.jpeg?auto=compress&fit=crop&w=${width}&h=${width <= 540 ? 900 : 1200}&q=85&dpr=2`

export const FEATURED_VELORA_ICONS = [
  { name: 'Amira Noor', handle: '@amiranoor.velora', image: pexels(6311300), gender: 'women' },
  { name: 'Noor Saeed', handle: '@noorsaeed.velora', image: pexels(6311301), gender: 'women' },
  { name: 'Safa Rahman', handle: '@safarahman.velora', image: pexels(6311302), gender: 'women' },
  { name: 'Layla Hussain', handle: '@laylahussain.velora', image: pexels(6311303), gender: 'women' },
  { name: 'Yusuf Noor', handle: '@yusufnoor.velora', image: pexels(7683700), gender: 'men' },
  { name: 'Omar Haleem', handle: '@omarhaleem.velora', image: pexels(7683701), gender: 'men' },
  { name: 'Hamza Karim', handle: '@hamzakarim.velora', image: pexels(7683702), gender: 'men' },
  { name: 'Zayn Saeed', handle: '@zaynsaeed.velora', image: pexels(7683703), gender: 'men' },
]
