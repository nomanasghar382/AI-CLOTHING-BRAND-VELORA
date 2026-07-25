import { FiDownload } from 'react-icons/fi'

const escape = (value) => `"${String(value ?? '').replaceAll('"', '""')}"`

export default function AdminExportButton({ rows, columns, filename }) {
  const exportRows = () => {
    const csv = [columns.map((column) => escape(column.label)).join(','), ...rows.map((row) => columns.map((column) => escape(column.value ? column.value(row) : row[column.key])).join(','))].join('\n')
    const anchor = document.createElement('a')
    anchor.href = URL.createObjectURL(new Blob([csv], { type: 'text/csv;charset=utf-8' }))
    anchor.download = `${filename}.csv`
    anchor.click()
    URL.revokeObjectURL(anchor.href)
  }
  return <button className="admin-button admin-button-secondary" onClick={exportRows} disabled={!rows.length}><FiDownload /> Export CSV</button>
}
