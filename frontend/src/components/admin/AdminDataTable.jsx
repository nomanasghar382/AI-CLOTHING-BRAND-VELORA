export default function AdminDataTable({ columns, rows, emptyMessage = 'No records found.', onRowClick }) {
  return <div className="admin-table-wrap">
    <table className="admin-table">
      <thead><tr>{columns.map((column) => <th key={column.label}>{column.label}</th>)}</tr></thead>
      <tbody>{rows.length ? rows.map((row, index) => <tr key={row.id || index} onClick={() => onRowClick?.(row)}>
        {columns.map((column) => <td key={column.label}>{column.render ? column.render(row) : row[column.key] || '—'}</td>)}
      </tr>) : <tr><td colSpan={columns.length} className="admin-table-empty">{emptyMessage}</td></tr>}</tbody>
    </table>
  </div>
}
