const money = (value, currency = 'USD') => new Intl.NumberFormat('en-US', { style: 'currency', currency }).format(Number(value || 0))

export default function PriceSummary({ subtotal, discount = 0, shipping = 0, tax = 0, total, currency = 'USD', children }) {
  const calculatedTotal = total ?? Number(subtotal || 0) - Number(discount || 0) + Number(shipping || 0) + Number(tax || 0)
  return <aside className="velora-card price-summary p-4">
    <p className="eyebrow">ORDER SUMMARY</p>
    <div className="summary-line"><span>Subtotal</span><span>{money(subtotal, currency)}</span></div>
    {Number(discount) > 0 && <div className="summary-line"><span>Discount</span><span>-{money(discount, currency)}</span></div>}
    <div className="summary-line"><span>Shipping</span><span>{Number(shipping) ? money(shipping, currency) : 'Calculated at checkout'}</span></div>
    {Number(tax) > 0 && <div className="summary-line"><span>Tax</span><span>{money(tax, currency)}</span></div>}
    <div className="summary-total"><span>Total</span><strong>{money(calculatedTotal, currency)}</strong></div>
    {children && <div className="mt-4">{children}</div>}
  </aside>
}
