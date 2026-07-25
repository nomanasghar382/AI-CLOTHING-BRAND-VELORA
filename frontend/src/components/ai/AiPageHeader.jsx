export default function AiPageHeader({ eyebrow = 'VELORA AI', title, copy }) {
  return <header className="mb-4"><p className="eyebrow mb-2">{eyebrow}</p><h2 className="h3">{title}</h2>{copy && <p className="text-slate-300 mb-0">{copy}</p>}</header>
}
