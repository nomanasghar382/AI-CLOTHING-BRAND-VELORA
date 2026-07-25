import { motion } from 'framer-motion'
import { useInteractiveMotionProps } from '../../hooks/useMotionConfig'

const variants = {
  primary: 'btn-velora-primary',
  secondary: 'btn-velora-secondary',
  ghost: 'btn-velora-ghost',
}

export default function Button({ children, className = '', variant = 'primary', type = 'button', ...props }) {
  const motionProps = useInteractiveMotionProps()
  return (
    <motion.button
      type={type}
      className={`btn ${variants[variant]} ${className}`}
      {...motionProps}
      {...props}
    >
      {children}
    </motion.button>
  )
}
