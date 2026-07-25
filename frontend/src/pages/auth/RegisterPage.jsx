import { useState } from 'react'
import { Link } from 'react-router-dom'
import Button from '../../components/common/Button'
import Input from '../../components/common/Input'
import { authService } from '../../services/authService'

export default function RegisterPage() {
  const [values, setValues] = useState({ name: '', email: '', password: '', password_confirmation: '' })
  const [feedback, setFeedback] = useState({ error: '', success: '' })
  const [submitting, setSubmitting] = useState(false)
  const update = (event) => setValues({ ...values, [event.target.name]: event.target.value })

  async function submit(event) {
    event.preventDefault(); setSubmitting(true); setFeedback({ error: '', success: '' })
    try { await authService.register(values); setFeedback({ error: '', success: 'Account created. Check your email to verify your account.' }) }
    catch (exception) { setFeedback({ success: '', error: exception.response?.data?.message || 'Unable to create your account.' }) }
    finally { setSubmitting(false) }
  }

  return <><p className="eyebrow">CREATE ACCOUNT</p><h1 className="h2 mb-2">Start with intention.</h1><p className="text-slate-300 mb-4">Join the Velora foundation.</p>
    {feedback.error && <div className="alert alert-danger">{feedback.error}</div>}{feedback.success && <div className="alert alert-success">{feedback.success}</div>}
    <form onSubmit={submit}><Input label="Full name" name="name" value={values.name} onChange={update} required /><Input label="Email address" type="email" name="email" value={values.email} onChange={update} required />
      <Input label="Password" type="password" name="password" minLength="12" value={values.password} onChange={update} required /><Input label="Confirm password" type="password" name="password_confirmation" minLength="12" value={values.password_confirmation} onChange={update} required />
      <Button className="w-100" type="submit" disabled={submitting}>{submitting ? 'Creating account...' : 'Create account'}</Button></form>
    <p className="text-slate-300 mt-4 mb-0">Already a member? <Link to="/login">Sign in</Link></p></>
}
