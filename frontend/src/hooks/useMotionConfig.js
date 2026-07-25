import { useReducedMotion } from 'framer-motion'

export function usePageMotionProps() {
  const reduce = useReducedMotion()
  if (reduce) {
    return { initial: false, animate: {}, transition: { duration: 0 } }
  }
  return {
    initial: { opacity: 0, y: 8 },
    animate: { opacity: 1, y: 0 },
    transition: { duration: 0.28, ease: 'easeOut' },
  }
}

export function useInteractiveMotionProps() {
  const reduce = useReducedMotion()
  if (reduce) return {}
  return { whileHover: { y: -2 }, whileTap: { scale: 0.98 } }
}
