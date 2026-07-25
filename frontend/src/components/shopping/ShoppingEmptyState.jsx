import { Link } from 'react-router-dom'
import { FiHeart, FiShoppingBag } from 'react-icons/fi'

export default function ShoppingEmptyState({ type }) {
  const isCart = type === 'cart'
  return <div className="state-panel text-center">
    {isCart ? <FiShoppingBag size={32} /> : <FiHeart size={32} />}
    <h1 className="h4 mt-3">{isCart ? 'Your bag is waiting.' : 'Your wishlist is waiting.'}</h1>
    <p className="text-slate-300">{isCart ? 'Add a signature piece and return here whenever you are ready.' : 'Save pieces you love so they are easy to find later.'}</p>
    <Link className="btn btn-velora-primary mt-2" to="/catalog">Explore the collection</Link>
  </div>
}
