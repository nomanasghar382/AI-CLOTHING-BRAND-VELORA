import { Navigate, Outlet, useLocation } from 'react-router-dom'
import useAuth from '../hooks/useAuth'
import Loader from '../components/feedback/Loader'

export default function ProtectedRoute({ roles = [] }) {
  const { isAuthenticated, isLoading, user } = useAuth()
  const location = useLocation()

  if (isLoading) return <div className="container py-5"><Loader /></div>
  if (!isAuthenticated) return <Navigate to="/login" replace state={{ from: location }} />
  if (roles.length && !user?.roles?.some((role) => roles.includes(role))) return <Navigate to="/forbidden" replace />

  return <Outlet />
}
