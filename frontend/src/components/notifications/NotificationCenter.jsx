import { useEffect, useMemo, useRef, useState } from 'react'
import { Link } from 'react-router-dom'
import { FiBell } from 'react-icons/fi'
import useAuth from '../../hooks/useAuth'
import useLoyalty from '../../hooks/useLoyalty'
import { useNotifications } from '../../hooks/useNotifications'
import { notificationService } from '../../services/notificationService'

const FOCUSABLE = 'button, [href], input, select, textarea, [tabindex]:not([tabindex="-1"])'

function groupNotifications(items) {
  return items.reduce((groups, item) => {
    const key = item.type || 'General'
    groups[key] = groups[key] ? [...groups[key], item] : [item]
    return groups
  }, {})
}

export default function NotificationCenter() {
  const { isAuthenticated } = useAuth()
  const { notifications, markNotificationRead } = useLoyalty()
  const { pushToast } = useNotifications()
  const [open, setOpen] = useState(false)
  const panelRef = useRef(null)
  const triggerRef = useRef(null)
  const unread = useMemo(() => notifications.filter((item) => !item.read).length, [notifications])

  useEffect(() => {
    if (!open) return undefined
    const onClick = (event) => {
      if (!panelRef.current?.contains(event.target)) setOpen(false)
    }
    const onKeyDown = (event) => {
      if (event.key === 'Escape') {
        setOpen(false)
        triggerRef.current?.focus()
        return
      }
      if (event.key !== 'Tab' || !panelRef.current) return
      const focusable = [...panelRef.current.querySelectorAll(FOCUSABLE)].filter((el) => !el.disabled)
      if (!focusable.length) return
      const first = focusable[0]
      const last = focusable[focusable.length - 1]
      if (event.shiftKey && document.activeElement === first) {
        event.preventDefault()
        last.focus()
      } else if (!event.shiftKey && document.activeElement === last) {
        event.preventDefault()
        first.focus()
      }
    }
    document.addEventListener('mousedown', onClick)
    window.addEventListener('keydown', onKeyDown)
    const timer = window.setTimeout(() => panelRef.current?.querySelector(FOCUSABLE)?.focus(), 0)
    return () => {
      document.removeEventListener('mousedown', onClick)
      window.removeEventListener('keydown', onKeyDown)
      window.clearTimeout(timer)
    }
  }, [open])

  if (!isAuthenticated) return null

  const grouped = groupNotifications(notifications)

  const markAll = async () => {
    try {
      await notificationService.markAllRead()
      notifications.filter((item) => !item.read).forEach((item) => markNotificationRead(item.id))
      pushToast({ message: 'All notifications marked as read.', variant: 'success' })
    } catch {
      pushToast({ message: 'Could not update notifications.', variant: 'danger' })
    }
  }

  return (
    <div className="notification-center" ref={panelRef}>
      <button
        ref={triggerRef}
        type="button"
        className="nav-icon-link"
        aria-expanded={open}
        aria-haspopup="dialog"
        aria-controls="notification-panel"
        aria-label={`Notifications, ${unread} unread`}
        onClick={() => setOpen((value) => !value)}
      >
        <FiBell />
        {unread > 0 && <span className="nav-counter">{unread}</span>}
      </button>
      {open && (
        <div className="notification-panel" id="notification-panel" role="dialog" aria-modal="true" aria-label="Notification center">
          <div className="notification-panel-head">
            <strong>Notifications</strong>
            <button type="button" className="btn btn-link p-0" onClick={markAll}>Mark all read</button>
          </div>
          {Object.keys(grouped).length ? Object.entries(grouped).map(([group, items]) => (
            <section key={group} className="notification-group">
              <p className="notification-group-label">{group}</p>
              {items.map((notice) => (
                <button key={notice.id} type="button" className={`notification-row ${notice.read ? '' : 'unread'}`} onClick={() => !notice.read && markNotificationRead(notice.id)}>
                  <span>
                    <strong>{notice.title}</strong>
                    <small>{notice.body || notice.message}</small>
                  </span>
                  {!notice.read && <i aria-hidden="true" />}
                </button>
              ))}
            </section>
          )) : <p className="notification-empty">You are all caught up.</p>}
          <Link className="notification-view-all" to="/notifications" onClick={() => setOpen(false)}>View all notifications</Link>
        </div>
      )}
    </div>
  )
}
