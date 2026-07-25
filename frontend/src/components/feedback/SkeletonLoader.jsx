export default function SkeletonLoader({ lines = 3 }) {
  return <div aria-label="Loading content">{Array.from({ length: lines }, (_, index) => <div className="placeholder-glow mb-2" key={index}><span className="placeholder col-12" /></div>)}</div>
}
