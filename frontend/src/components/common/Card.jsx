export default function Card({ children, className = '' }) {
  return <article className={`velora-card ${className}`}>{children}</article>
}
