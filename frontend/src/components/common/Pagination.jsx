export default function Pagination({ page = 1, pageCount = 1, onPageChange }) {
  if (pageCount <= 1) return null
  return (
    <nav aria-label="Pagination">
      <ul className="pagination mb-0">
        <li className="page-item">
          <button className="page-link" type="button" disabled={page === 1} onClick={() => onPageChange(page - 1)} aria-label="Go to previous page">Previous</button>
        </li>
        <li className="page-item active" aria-current="page">
          <span className="page-link">Page {page} of {pageCount}</span>
        </li>
        <li className="page-item">
          <button className="page-link" type="button" disabled={page === pageCount} onClick={() => onPageChange(page + 1)} aria-label="Go to next page">Next</button>
        </li>
      </ul>
    </nav>
  )
}
