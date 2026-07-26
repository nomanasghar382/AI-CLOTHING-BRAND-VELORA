import AiPageHeader from '../../components/ai/AiPageHeader'
import OutfitCard from '../../components/ai/OutfitCard'
import EmptyState from '../../components/feedback/EmptyState'
import useAiStylist from '../../hooks/useAiStylist'

export default function SavedOutfitsPage() {
  const { savedOutfits, removeOutfit } = useAiStylist()
  return <div><AiPageHeader eyebrow="YOUR PRIVATE ARCHIVE" title="Saved outfits" copy="Keep considered looks close for the next time you need them." />
    {!savedOutfits.length ? <EmptyState title="No saved outfits yet." message="Save an edit from your recommendation history to build your private archive." /> : <div className="d-grid gap-3">{savedOutfits.map((outfit) => <OutfitCard key={outfit.id} outfit={outfit} onRemove={removeOutfit} />)}</div>}
  </div>
}
