import { StrictMode } from 'react'
import { createRoot } from 'react-dom/client'
import { registerSW } from 'virtual:pwa-register'
import 'bootstrap/dist/css/bootstrap.min.css'
import './index.css'
import App from './App.jsx'
import { BrowserRouter } from 'react-router-dom'
import { AuthProvider } from './context/AuthContext.jsx'
import { CartProvider } from './context/CartContext.jsx'
import { WishlistProvider } from './context/WishlistContext.jsx'
import { AiStylistProvider } from './context/AiStylistContext.jsx'
import { CommunityProvider } from './context/CommunityContext.jsx'
import { InternationalProvider } from './context/InternationalContext.jsx'
import { LoyaltyProvider } from './context/LoyaltyContext.jsx'
import { NotificationProvider } from './context/NotificationContext.jsx'

registerSW({ immediate: true })

createRoot(document.getElementById('root')).render(
  <StrictMode>
    <BrowserRouter>
      <NotificationProvider>
        <AuthProvider>
          <CartProvider>
            <WishlistProvider>
              <CommunityProvider>
                <InternationalProvider>
                  <LoyaltyProvider>
                    <AiStylistProvider>
                      <App />
                    </AiStylistProvider>
                  </LoyaltyProvider>
                </InternationalProvider>
              </CommunityProvider>
            </WishlistProvider>
          </CartProvider>
        </AuthProvider>
      </NotificationProvider>
    </BrowserRouter>
  </StrictMode>,
)
