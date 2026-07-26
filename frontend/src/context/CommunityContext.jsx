import { useCallback, useMemo, useState } from 'react'
import { menModestImage, womenModestImage } from '../constants/modestFashionImages'
import { CommunityContext } from './communityContext'

const starterPosts = [
  { id: 'post-1', author: 'Amara Idris', handle: '@amaraedits', time: '18 min', text: 'The outfit formula I keep returning to: one generous layer, one crisp shape, and one small point of shine.', image: womenModestImage(0), likes: 312, liked: false, bookmarked: true, comments: [{ id: 'c1', author: 'Nora', text: 'That last detail changes everything.' }] },
  { id: 'post-2', author: 'Omar Hassan', handle: '@omarstyle', time: '42 min', text: 'A well-cut thobe is the foundation of every Islamic wardrobe. Invest in fabric that breathes and drapes with dignity.', image: menModestImage(1), likes: 187, liked: true, bookmarked: false, comments: [] },
]

export function CommunityProvider({ children }) {
  const [posts, setPosts] = useState(starterPosts)
  const [following, setFollowing] = useState(['amara'])

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
    setPosts((current) => [{ id: `post-${Date.now()}`, author: 'You', handle: '@yourcloset', time: 'now', text: text.trim(), likes: 0, liked: false, bookmarked: false, comments: [] }, ...current])
  }, [])

  const value = useMemo(() => ({ posts, following, toggleLike, toggleBookmark, addComment, reportPost, toggleFollow, addPost }), [posts, following, toggleLike, toggleBookmark, addComment, reportPost, toggleFollow, addPost])
  return <CommunityContext.Provider value={value}>{children}</CommunityContext.Provider>
}
