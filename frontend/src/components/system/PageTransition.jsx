import { motion } from 'framer-motion'
import { usePageMotionProps } from '../../hooks/useMotionConfig'

export default function PageTransition({ children }) {
  const motionProps = usePageMotionProps()
  return <motion.div {...motionProps}>{children}</motion.div>
}
