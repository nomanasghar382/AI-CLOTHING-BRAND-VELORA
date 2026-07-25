import { StrictMode } from 'react'
import { createRoot } from 'react-dom/client'
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

createRoot(document.getElementById('root')).render(
  <StrictMode>
    <BrowserRouter>
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
    </BrowserRouter>
  </StrictMode>,
)
