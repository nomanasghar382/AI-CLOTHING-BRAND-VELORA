import { Component } from 'react'
import ErrorState from '../feedback/ErrorState'
import Button from '../common/Button'

export default class ErrorBoundary extends Component {
  constructor(props) {
    super(props)
    this.state = { hasError: false }
  }

  static getDerivedStateFromError() {
    return { hasError: true }
  }

  render() {
    if (this.state.hasError) {
      return (
        <section className="container py-5">
          <ErrorState title="Something interrupted your experience." message="Please refresh the page or return home." />
          <div className="text-center mt-4">
            <Button onClick={() => window.location.assign('/')}>Return home</Button>
          </div>
        </section>
      )
    }

    return this.props.children
  }
}
