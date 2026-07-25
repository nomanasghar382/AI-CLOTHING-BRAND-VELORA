import { memo, useMemo } from 'react'
import { readSavedFilters, saveFilterPreset } from '../../utils/offlineStore'
import { useNotifications } from '../../hooks/useNotifications'

function toggleValue(list, value) {
  return list.includes(value) ? list.filter((item) => item !== value) : [...list, value]
}

function CatalogFilterPanel({ filters, params, onChange, onClear }) {
  const { pushToast } = useNotifications()
  const selectedColors = useMemo(() => (params.get('colors') ? params.get('colors').split(',') : []), [params])
  const selectedBrands = useMemo(() => (params.get('brands') ? params.get('brands').split(',') : []), [params])
  const minPrice = params.get('min_price') || filters.price_range?.min || 0
  const maxPrice = params.get('max_price') || filters.price_range?.max || 500

  const savePreset = () => {
    const preset = {
      name: `Saved edit ${new Date().toLocaleDateString()}`,
      query: Object.fromEntries(params.entries()),
    }
    saveFilterPreset(preset)
    pushToast({ message: 'Filter preset saved on this device.', variant: 'success' })
  }

  return (
    <div className="velora-card p-3 filter-panel">
      <div className="d-flex justify-content-between align-items-center mb-3">
        <p className="small text-slate-300 mb-0">REFINE</p>
        <button type="button" className="btn btn-link p-0 small" onClick={onClear}>Clear</button>
      </div>

      <label className="form-label" htmlFor="catalog-sort">Sort by</label>
      <select id="catalog-sort" className="form-select velora-input mb-3" value={params.get('sort') || 'newest'} onChange={(e) => onChange('sort', e.target.value)}>
        <option value="newest">Newest</option>
        <option value="price_asc">Price: low to high</option>
        <option value="price_desc">Price: high to low</option>
        <option value="popular">Most viewed</option>
        <option value="alphabetical">A–Z</option>
      </select>

      <fieldset className="mb-3">
        <legend className="form-label">Brands</legend>
        <div className="filter-chip-grid">
          {filters.brands?.map((brand) => (
            <label key={brand.slug} className="filter-chip">
              <input type="checkbox" checked={selectedBrands.includes(brand.slug)} onChange={() => onChange('brands', toggleValue(selectedBrands, brand.slug).join(','))} />
              <span>{brand.name}</span>
            </label>
          ))}
        </div>
      </fieldset>

      <fieldset className="mb-3">
        <legend className="form-label">Colors</legend>
        <div className="filter-chip-grid">
          {filters.colors?.map((color) => (
            <label key={color.slug} className="filter-chip">
              <input type="checkbox" checked={selectedColors.includes(color.slug)} onChange={() => onChange('colors', toggleValue(selectedColors, color.slug).join(','))} />
              <span className="color-chip" style={{ background: color.hex_code || '#ddd' }} aria-hidden="true" />
              <span>{color.name}</span>
            </label>
          ))}
        </div>
      </fieldset>

      <label className="form-label" htmlFor="catalog-min-price">Price range</label>
      <div className="row g-2 mb-3">
        <div className="col-6"><input id="catalog-min-price" type="number" className="form-control velora-input" value={minPrice} min={0} onChange={(e) => onChange('min_price', e.target.value)} aria-label="Minimum price" /></div>
        <div className="col-6"><input type="number" className="form-control velora-input" value={maxPrice} min={0} onChange={(e) => onChange('max_price', e.target.value)} aria-label="Maximum price" /></div>
      </div>

      <label className="form-label" htmlFor="catalog-material">Material</label>
      <select id="catalog-material" className="form-select velora-input mb-3" value={params.get('material') || ''} onChange={(e) => onChange('material', e.target.value)}>
        <option value="">All materials</option>
        {filters.materials?.map((material) => <option key={material} value={material}>{material}</option>)}
      </select>

      <label className="form-label" htmlFor="catalog-fabric">Fabric</label>
      <select id="catalog-fabric" className="form-select velora-input mb-3" value={params.get('fabric') || ''} onChange={(e) => onChange('fabric', e.target.value)}>
        <option value="">All fabrics</option>
        {filters.fabrics?.map((fabric) => <option key={fabric} value={fabric}>{fabric}</option>)}
      </select>

      <label className="form-label" htmlFor="catalog-gender">Shop for</label>
      <select id="catalog-gender" className="form-select velora-input mb-3" value={params.get('gender') || ''} onChange={(e) => onChange('gender', e.target.value)}>
        <option value="">Women &amp; men</option>
        {filters.genders?.map((gender) => <option key={gender} value={gender}>{gender === 'men' ? 'Men' : 'Women'}</option>)}
      </select>

      <label className="form-label" htmlFor="catalog-coverage">Coverage</label>
      <select id="catalog-coverage" className="form-select velora-input mb-3" value={params.get('coverage_level') || ''} onChange={(e) => onChange('coverage_level', e.target.value)}>
        <option value="">All coverage</option>
        {filters.coverage_levels?.map((level) => <option key={level} value={level}>{level}</option>)}
      </select>

      <label className="form-label" htmlFor="catalog-occasion">Occasion</label>
      <select id="catalog-occasion" className="form-select velora-input mb-3" value={params.get('season') || ''} onChange={(e) => onChange('season', e.target.value)}>
        <option value="">All occasions</option>
        {filters.occasions?.map((occasion) => <option key={occasion} value={occasion}>{occasion}</option>)}
      </select>

      <label className="form-check mb-3">
        <input className="form-check-input" type="checkbox" checked={params.get('in_stock') === '1'} onChange={(e) => onChange('in_stock', e.target.checked ? '1' : '')} />
        <span className="form-check-label">In stock only</span>
      </label>

      <button type="button" className="btn btn-velora-secondary w-100" onClick={savePreset}>Save filters</button>
      {!!readSavedFilters().length && <p className="small text-slate-300 mt-2 mb-0">{readSavedFilters().length} saved on this device</p>}
    </div>
  )
}

export default memo(CatalogFilterPanel)
