import { useCallback, useMemo, useState } from 'react'
import { FEATURED_EDITORIAL } from '../constants/veloraIcons'
import { CommunityContext } from './communityContext'

const sportImage = (index) => FEATURED_EDITORIAL[index % FEATURED_EDITORIAL.length].image

const starterPosts = [
  { id: 'post-1', author: 'Jax Rivera', handle: '@jaxfit', time: '18 min', text: 'Gym fit formula: compression tee, oversized hoodie, and a fresh pair of trainers. Simple wins.', image: sportImage(2), likes: 412, liked: false, bookmarked: true, comments: [{ id: 'c1', author: 'Mike', text: 'Need that Gymshark drop.' }] },
  { id: 'post-2', author: 'Mike Chen', handle: '@mikecourt', time: '42 min', text: 'Jordan 1s with tapered joggers — court to street without switching shoes.', image: sportImage(3), likes: 287, liked: true, bookmarked: false, comments: [] },
]

export function CommunityProvider({ children }) {
  const [posts, setPosts] = useState(starterPosts)
  const [following, setFollowing] = useState(['jax'])

  const toggleLike = useCallback((id) => setPosts((current) => current.map((post) => post.id === id ? { ...post, liked: !post.liked, likes: post.likes + (post.liked ? -1 : 1) } : post)), [])
  const toggleBookmark = useCallback((id) => setPosts((current) => current.map((post) => post.id === id ? { ...post, bookmarked: !post.bookmarked } : post)), [])
  const addComment = useCallback((id, text) => {
    if (!text.trim()) return
    setPosts((current) => current.map((post) => post.id === id ? { ...post, comments: [...post.comments, { id: `${id}-${Date.now()}`, author: 'You', text: text.trim() }] } : post))
  }, [])
  const reportPost = useCallback((id) => setPosts((current) => current.map((post) => post.id === id ? { ...post, reported: true } : post)), [])
  const toggleFollow = useCallback((id) => setFollowing((current) => current.includes(id) ? current.filter((item) => item !== id) : [...current, id]), [])
  const addPost = useCallback((text) => {
    if (!text.trim()) return
    setPosts((current) => [{ id: `post-${Date.now()}`, author: 'You', handle: '@yourfits', time: 'now', text: text.trim(), likes: 0, liked: false, bookmarked: false, comments: [] }, ...current])
  }, [])

  const value = useMemo(() => ({ posts, following, toggleLike, toggleBookmark, addComment, reportPost, toggleFollow, addPost }), [posts, following, toggleLike, toggleBookmark, addComment, reportPost, toggleFollow, addPost])
  return <CommunityContext.Provider value={value}>{children}</CommunityContext.Provider>
}
