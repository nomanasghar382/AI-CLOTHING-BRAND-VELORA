/** High-quality demo imagery — Gen Z Islamic modest fashion. */

const unsplashPhoto = (id, width = 720) => {
  const host = id.startsWith('premium_photo') ? 'plus.unsplash.com' : 'images.unsplash.com'
  const quality = width <= 540 ? 82 : 85
  return `https://${host}/${id}?auto=format&fit=crop&w=${width}&q=${quality}&dpr=2`
}

const pexelsPhoto = (id, width = 720) => {
  const quality = width <= 540 ? 82 : 85
  return `https://images.pexels.com/photos/${id}/pexels-photo-${id}.jpeg?auto=compress&fit=crop&w=${width}&q=${quality}&dpr=2`
}

const photo = (id, width = 720) => (id.startsWith('pexels:') ? pexelsPhoto(id.slice(7), width) : unsplashPhoto(id, width))

/** Women: niqab, mannequin editorial, abaya, hijab street style. */
export const WOMEN_PHOTO_IDS = [
  'photo-1744727811425-e1c0af8b4022',
  'photo-1559730775-67f621597262',
  'photo-1618297655311-ab851e7045d6',
  'photo-1771162766051-c330f1d664ea',
  'photo-1772474542630-5f5822ca8421',
  'photo-1561442748-c50715dc32f6',
  'photo-1774227834992-745da3b78e98',
  'photo-1768830985958-e8d3a93d3f14',
  'photo-1585728748176-455ac5eed962',
  'photo-1626497361649-81cc097e9bfd',
  'photo-1536814294574-df49a3cc97bd',
  'photo-1708151729075-89f1fc21e19e',
  'pexels:6311392',
  'pexels:7683751',
  'pexels:7671166',
]

/** Men: young kurta, thobe, shalwar, and modest streetwear. */
export const MEN_PHOTO_IDS = [
  'photo-1774527929835-282b1b85cd3a',
  'photo-1759567066672-4b9f48000096',
  'photo-1756412066323-a336d2becc10',
  'photo-1774424420923-6936309c3c5e',
  'photo-1578507435314-e39e7852eddd',
  'photo-1757143137159-316220046829',
  'photo-1564289851149-a9f8940f795c',
  'photo-1625728273079-27996db5e7f9',
  'photo-1552374196-c4e7ffc6e126',
  'photo-1560250097-0b93528c311a',
  'photo-1557862921-37829c790f19',
  'pexels:6311584',
]

const poolFor = (gender) => (gender === 'men' ? MEN_PHOTO_IDS : WOMEN_PHOTO_IDS)

export const WOMEN_MODEST_IMAGES = WOMEN_PHOTO_IDS.map((id) => photo(id))
export const MEN_MODEST_IMAGES = MEN_PHOTO_IDS.map((id) => photo(id))
export const MODEST_FASHION_IMAGES = [...WOMEN_MODEST_IMAGES, ...MEN_MODEST_IMAGES]

export const womenModestImage = (index, width = 720) => photo(WOMEN_PHOTO_IDS[index % WOMEN_PHOTO_IDS.length], width)
export const menModestImage = (index, width = 720) => photo(MEN_PHOTO_IDS[index % MEN_PHOTO_IDS.length], width)

export const modestFashionImage = (index, width = 720, gender = 'women') =>
  photo(poolFor(gender)[index % poolFor(gender).length], width)
