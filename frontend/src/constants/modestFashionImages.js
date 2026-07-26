/** Gen Z modest fashion — curated young editorial shots only. */
const unsplashPhoto = (id, width = 720) => {
  const host = id.startsWith('premium_photo') ? 'plus.unsplash.com' : 'images.unsplash.com'
  const quality = width <= 540 ? 82 : 85
  return `https://${host}/${id}?auto=format&fit=crop&w=${width}&h=${width <= 540 ? 900 : 1200}&q=${quality}&dpr=2`
}

/** Women: young hijab / abaya / niqab editorial. */
export const WOMEN_PHOTO_IDS = [
  'photo-1774227834992-745da3b78e98',
  'photo-1772474542630-5f5822ca8421',
  'photo-1768830985958-e8d3a93d3f14',
  'photo-1626497361649-81cc097e9bfd',
  'photo-1536814294574-df49a3cc97bd',
  'photo-1585728748176-455ac5eed962',
  'photo-1561442748-c50715dc32f6',
  'photo-1744727811425-e1c0af8b4022',
]

/** Men: young kurta / thobe / shalwar editorial. */
export const MEN_PHOTO_IDS = [
  'photo-1774527929835-282b1b85cd3a',
  'photo-1759567066672-4b9f48000096',
  'photo-1756412066323-a336d2becc10',
  'photo-1774424420923-6936309c3c5e',
  'photo-1757143137159-316220046829',
  'photo-1564289851149-a9f8940f795c',
]

const poolFor = (gender) => (gender === 'men' ? MEN_PHOTO_IDS : WOMEN_PHOTO_IDS)

export const WOMEN_MODEST_IMAGES = WOMEN_PHOTO_IDS.map((id) => unsplashPhoto(id))
export const MEN_MODEST_IMAGES = MEN_PHOTO_IDS.map((id) => unsplashPhoto(id))
export const MODEST_FASHION_IMAGES = [...WOMEN_MODEST_IMAGES, ...MEN_MODEST_IMAGES]

export const womenModestImage = (index, width = 720) => unsplashPhoto(WOMEN_PHOTO_IDS[index % WOMEN_PHOTO_IDS.length], width)
export const menModestImage = (index, width = 720) => unsplashPhoto(MEN_PHOTO_IDS[index % MEN_PHOTO_IDS.length], width)

export const modestFashionImage = (index, width = 720, gender = 'women') =>
  unsplashPhoto(poolFor(gender)[index % poolFor(gender).length], width)
