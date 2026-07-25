import { useEffect, useState } from 'react'
import { FiShoppingBag, FiUsers } from 'react-icons/fi'
import useCart from '../../hooks/useCart'
import useCommunity from '../../hooks/useCommunity'
import { communityService } from '../../services/communityService'
import LookCard from '../../components/community/LookCard'
import { modestFashionImage } from '../../constants/modestFashionImages'

export function ShopTheLookPage() {
  const [products, setProducts] = useState([]); const [notice, setNotice] = useState('')
  const { addItem } = useCart()
  useEffect(() => { communityService.products().then(setProducts) }, [])
  const add = async (product) => {
    try { await addItem({ product_id: product.id, quantity: 1 }); setNotice(`${product.name} added to your bag.`) } catch { setNotice('Sign in to add this piece to your bag.') }
  }
  const addAll = async () => {
    for (const product of products) { try { await addItem({ product_id: product.id, quantity: 1 }) } catch { setNotice('Sign in to add the look to your bag.'); return } }
    setNotice('The complete look is in your bag.')
  }
  return <section className="container py-5 feature-page"><div className="row g-4"><div className="col-lg-5"><img className="shop-look-image" src={modestFashionImage(0, 1000)} alt="Modest layered look" /></div><div className="col-lg-7"><p className="eyebrow">SHOP THE LOOK</p><h1>After-hours linen</h1><p className="text-slate-300">A study in relaxed structure from Amara’s summer edit. Pick individual pieces or add the full look.</p>{notice && <div className="ai-server-notice">{notice}</div>}<div className="d-grid gap-2 my-4">{products.map((product) => <div className="look-product velora-card p-2" key={product.id}><img src={product.image} alt={product.name} /><div><p className="eyebrow mb-1">{product.brand}</p><strong>{product.name}</strong><small>${product.price}</small></div><button className="btn btn-velora-secondary" onClick={() => add(product)}>Add</button></div>)}</div><button className="btn btn-velora-primary w-100" onClick={addAll}><FiShoppingBag /> Add complete look · $316</button></div></div></section>
}

export function SavedLooksPage() {
  const [looks, setLooks] = useState([]); const [saved, setSaved] = useState(['look-1', 'look-2'])
  useEffect(() => { communityService.looks().then(setLooks) }, [])
  return <section className="container py-5 feature-page"><p className="eyebrow">YOUR COLLECTION</p><h1>Saved looks.</h1><p className="text-slate-300">A personal reference library for getting dressed.</p><div className="look-grid mt-4">{looks.filter((look) => saved.includes(look.id)).map((look) => <LookCard key={look.id} look={look} saved onSave={(id) => setSaved((items) => items.filter((item) => item !== id))} />)}</div></section>
}

export function FollowersPage() {
  const [creators, setCreators] = useState([]); const { following, toggleFollow } = useCommunity()
  useEffect(() => { communityService.creators().then(setCreators) }, [])
  return <section className="container py-5 feature-page"><p className="eyebrow">YOUR NETWORK</p><h1>Following</h1><p className="text-slate-300">Keep up with the people whose edits you return to.</p><div className="row g-3 mt-2">{creators.filter((creator) => following.includes(creator.id)).map((creator) => <div className="col-md-6" key={creator.id}><div className="follow-row velora-card p-3"><img src={creator.image} alt={creator.name} /><div><strong>{creator.name}</strong><small>{creator.handle} · {creator.followers} followers</small></div><button className="btn btn-velora-secondary ms-auto" onClick={() => toggleFollow(creator.id)}>Following</button></div></div>)}</div>{!following.length && <div className="velora-card p-4 mt-3"><FiUsers /> <strong className="ms-2">Follow creators to build your circle.</strong></div>}</section>
}
