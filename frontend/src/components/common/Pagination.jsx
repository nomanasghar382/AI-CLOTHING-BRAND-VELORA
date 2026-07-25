export default function Pagination({ page = 1, pageCount = 1, onPageChange }) {
  if (pageCount <= 1) return null
  return <nav aria-label="Pagination"><ul className="pagination mb-0"><li className="page-item"><button className="page-link" disabled={page === 1} onClick={() => onPageChange(page - 1)}>Previous</button></li><li className="page-item active"><span className="page-link">{page}</span></li><li className="page-item"><button className="page-link" disabled={page === pageCount} onClick={() => onPageChange(page + 1)}>Next</button></li></ul></nav>
}
