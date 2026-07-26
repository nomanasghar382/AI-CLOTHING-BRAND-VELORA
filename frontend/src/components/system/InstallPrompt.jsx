import { useEffect, useState } from 'react'
import Button from '../common/Button'

export default function InstallPrompt() {
  const [prompt, setPrompt] = useState(null)
  const [visible, setVisible] = useState(false)

  useEffect(() => {
    const onBeforeInstall = (event) => {
      event.preventDefault()
      setPrompt(event)
      setVisible(true)
    }
    window.addEventListener('beforeinstallprompt', onBeforeInstall)
    return () => window.removeEventListener('beforeinstallprompt', onBeforeInstall)
  }, [])

  if (!visible || !prompt) return null

  return (
    <aside className="install-prompt" role="region" aria-label="Install VELORA app">
      <div>
        <p className="eyebrow mb-1">INSTALL VELORA</p>
        <strong>Add VELORA to your home screen for faster access.</strong>
      </div>
      <div className="d-flex gap-2">
        <Button onClick={async () => { await prompt.prompt(); setVisible(false) }}>Install</Button>
        <button type="button" className="btn btn-velora-ghost" onClick={() => setVisible(false)}>Not now</button>
      </div>
    </aside>
  )
}
