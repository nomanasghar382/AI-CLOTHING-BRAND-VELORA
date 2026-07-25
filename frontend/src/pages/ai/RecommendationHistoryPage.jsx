import AiPageHeader from '../../components/ai/AiPageHeader'
import OutfitCard from '../../components/ai/OutfitCard'
import EmptyState from '../../components/feedback/EmptyState'
import useAiStylist from '../../hooks/useAiStylist'

export default function RecommendationHistoryPage() {
  const { history, savedOutfits, saveOutfit } = useAiStylist()
  return <div><AiPageHeader eyebrow="STYLIST ARCHIVE" title="Recommendation history" copy="Every occasion and budget edit remains here for you to revisit." />
    {!history.length ? <EmptyState title="Your archive is quiet." message="Create an occasion or budget edit and it will appear here." /> : <div className="d-grid gap-3">{history.map((outfit) => <OutfitCard key={outfit.id} outfit={outfit} saved={savedOutfits.some((saved) => saved.id === outfit.id)} onSave={saveOutfit} />)}</div>}
  </div>
}
