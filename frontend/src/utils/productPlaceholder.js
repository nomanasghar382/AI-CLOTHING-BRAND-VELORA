export const PLACEHOLDER_FOOTWEAR = '/catalog/shoe-nike.jpg'
export const PLACEHOLDER_APPAREL = '/catalog/apparel-train.jpg'

export function placeholderForLine(catalogLine) {
  return catalogLine === 'footwear' ? PLACEHOLDER_FOOTWEAR : PLACEHOLDER_APPAREL
}
