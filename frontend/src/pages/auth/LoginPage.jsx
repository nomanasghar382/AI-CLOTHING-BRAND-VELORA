import { useState } from 'react'
import { useLocation, useNavigate, Link } from 'react-router-dom'
import Button from '../../components/common/Button'
import Input from '../../components/common/Input'
import useAuth from '../../hooks/useAuth'

export default function LoginPage() {
  const [values, setValues] = useState({ email: '', password: '', remember: false })
  const [error, setError] = useState('')
  const [submitting, setSubmitting] = useState(false)
  const { signIn } = useAuth()
  const navigate = useNavigate()
  const location = useLocation()

  async function submit(event) {
    event.preventDefault()
    setSubmitting(true); setError('')
    try { await signIn(values); navigate(location.state?.from?.pathname || '/') }
    catch (exception) { setError(exception.response?.data?.message || 'Unable to sign in. Please try again.') }
    finally { setSubmitting(false) }
  }

  return <><p className="eyebrow">WELCOME BACK</p><h1 className="h2 mb-2">Sign in to Velora</h1><p className="text-slate-300 mb-4">Your account foundation is ready.</p>
    {error && <div className="alert alert-danger">{error}</div>}
    <form onSubmit={submit}><Input label="Email address" type="email" name="email" autoComplete="email" value={values.email} onChange={(e) => setValues({ ...values, email: e.target.value })} required />
      <Input label="Password" type="password" name="password" autoComplete="current-password" value={values.password} onChange={(e) => setValues({ ...values, password: e.target.value })} required />
      <label className="form-check text-slate-300 mb-4"><input className="form-check-input" type="checkbox" checked={values.remember} onChange={(e) => setValues({ ...values, remember: e.target.checked })} /> Remember me</label>
      <Button className="w-100" type="submit" disabled={submitting}>{submitting ? 'Signing in...' : 'Sign in'}</Button></form>
    <p className="text-slate-300 mt-4 mb-0">New to Velora? <Link to="/register">Create account</Link></p></>
}
