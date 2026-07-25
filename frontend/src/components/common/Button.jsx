import { motion } from 'framer-motion'

const variants = {
  primary: 'btn-velora-primary',
  secondary: 'btn-velora-secondary',
  ghost: 'btn-velora-ghost',
}

export default function Button({ children, className = '', variant = 'primary', type = 'button', ...props }) {
  return (
    <motion.button
      type={type}
      className={`btn ${variants[variant]} ${className}`}
      whileHover={{ y: -2 }}
      whileTap={{ scale: 0.98 }}
      {...props}
    >
      {children}
    </motion.button>
  )
}
