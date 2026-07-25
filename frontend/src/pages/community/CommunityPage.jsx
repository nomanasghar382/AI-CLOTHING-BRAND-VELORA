import { useState } from 'react'
import { FiBookmark, FiFlag, FiHeart, FiMessageCircle, FiMoreHorizontal, FiSend } from 'react-icons/fi'
import useCommunity from '../../hooks/useCommunity'

function Post({ post }) {
  const { toggleLike, toggleBookmark, addComment, reportPost } = useCommunity()
  const [comment, setComment] = useState('')
  const [commentsOpen, setCommentsOpen] = useState(false)
  const submit = (event) => { event.preventDefault(); addComment(post.id, comment); setComment('') }

  return (
    <article className="feed-post velora-card p-3 p-md-4">
      <header>
        <div className="post-avatar" aria-hidden="true">{post.author.charAt(0)}</div>
        <div><strong>{post.author}</strong><small>{post.handle} · {post.time}</small></div>
        <button className="icon-action ms-auto" type="button" onClick={() => reportPost(post.id)} aria-label="Report post"><FiMoreHorizontal /></button>
      </header>
      <p className="post-copy">{post.text}</p>
      {post.image && <img className="feed-image" src={post.image} alt={post.text ? `Outfit shared by ${post.author}` : ''} />}
      <div className="post-actions">
        <button type="button" onClick={() => toggleLike(post.id)} className={post.liked ? 'active' : ''} aria-label={`Like post (${post.likes} likes)`} aria-pressed={post.liked}><FiHeart /> {post.likes}</button>
        <button type="button" onClick={() => setCommentsOpen(!commentsOpen)} aria-label={`View comments (${post.comments.length})`} aria-expanded={commentsOpen}><FiMessageCircle /> {post.comments.length}</button>
        <button type="button" onClick={() => toggleBookmark(post.id)} className={post.bookmarked ? 'active' : ''} aria-label={post.bookmarked ? 'Remove bookmark' : 'Bookmark post'} aria-pressed={post.bookmarked}><FiBookmark /></button>
        <button type="button" onClick={() => reportPost(post.id)} className="ms-auto" aria-label={post.reported ? 'Post reported' : 'Report post'}>{post.reported ? 'Reported' : <><FiFlag /> Report</>}</button>
      </div>
      {commentsOpen && (
        <div className="comments">
          {post.comments.map((item) => <p key={item.id}><strong>{item.author}</strong> {item.text}</p>)}
          <form onSubmit={submit}>
            <input value={comment} onChange={(event) => setComment(event.target.value)} className="velora-input form-control" placeholder="Add a thoughtful comment..." aria-label="Comment on post" />
            <button className="icon-action" type="submit" aria-label="Post comment"><FiSend /></button>
          </form>
        </div>
      )}
    </article>
  )
}

export default function CommunityPage() {
  const { posts, addPost } = useCommunity()
  const [draft, setDraft] = useState('')
  const [page, setPage] = useState(1)
  const submit = (event) => { event.preventDefault(); addPost(draft); setDraft('') }
  const display = [...posts, ...posts.slice(0, Math.max(0, page - 1))]

  return (
    <section className="container py-5 feature-page">
      <div className="community-head">
        <div><p className="eyebrow">THE COMMUNITY</p><h1>Style is better in conversation.</h1><p className="text-slate-300">Ideas, outfit notes, and looks from people dressing with intention.</p></div>
        <span className="live-pill">● Live feed</span>
      </div>
      <div className="row g-4">
        <div className="col-lg-8">
          <form className="composer velora-card p-3 mb-3" onSubmit={submit}>
            <div className="post-avatar" aria-hidden="true">Y</div>
            <input value={draft} onChange={(event) => setDraft(event.target.value)} className="form-control velora-input" placeholder="Share a styling thought..." aria-label="Write a community post" />
            <button className="btn btn-velora-primary" type="submit" disabled={!draft.trim()}>Post</button>
          </form>
          <div className="d-grid gap-3">{display.map((post, index) => <Post post={post} key={`${post.id}-${index}`} />)}</div>
          <button className="btn btn-velora-secondary w-100 mt-4" type="button" onClick={() => setPage((current) => current + 1)}>Load more looks</button>
        </div>
        <aside className="col-lg-4">
          <div className="velora-card p-4 feed-aside">
            <p className="eyebrow">THIS WEEK</p>
            <h2 className="h4">Community prompts</h2>
            <p className="text-slate-300">How do you make one favorite piece work harder?</p>
            <hr />
            <p className="eyebrow">TRENDING TAGS</p>
            <div className="d-flex flex-wrap gap-2">{['#linenlayers', '#quietcolor', '#repeatrepeat', '#cityuniform'].map((tag) => <span className="tag-pill" key={tag}>{tag}</span>)}</div>
          </div>
        </aside>
      </div>
    </section>
  )
}
