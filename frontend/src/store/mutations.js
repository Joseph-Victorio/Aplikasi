import { AddressbarColor } from 'quasar'

export default {
  SET_ERRORS: (state,payload) => {
    state.errors = payload
  },
  PUSH_ERRORS: (state,payload) => {
    state.errors[payload.key] = payload.value
  },
  CLEAR_ERRORS: (state) => {
    state.errors = {}
  },
  SET_FORGOT_PASSWORD: (state, payload) => {
    state.forgot_password[payload.key] = payload.value
  },
  SET_LOADING: (state, payload) => {
    state.loading = payload
  },
  SET_SHOP: (state, shop) => {
    state.shop = shop
  },
  SET_BLOCK: (state, payload) => {
    state.blocks = payload
  },
  SET_META_TITLE: (state, payload) => {
    state.meta.title = payload
  },
  SET_CONFIG: (state, payload) => {

    state.config = payload

    if(!localStorage.getItem('__clr')) {
      state.config.theme_color = payload.theme_color
      localStorage.setItem('__clr', state.config.theme_color)
      
    }else {
      state.config.theme_color = localStorage.getItem('__clr')
    }
    
    document.body.style.setProperty('--q-color-primary', state.config.theme_color)
    AddressbarColor.set(state.config.theme_color)
  },
  SET_HOME_VIEW_MODE: (state, payload) => {
    state.config.home_view_mode = payload
  },
  LOAD_DEMO_CONFIG: (state) => {
    if(localStorage.getItem('__wck')) {
      let wck = localStorage.getItem('__wck')
      state.config.is_whatsapp_checkout =  wck == true || wck == 'true' ? true : false
    }
    if(localStorage.getItem('__guest')) {
      let isGuest = localStorage.getItem('__guest')
      state.config.is_whatsapp_checkout =  isGuest == true || isGuest == 'true' ? true : false
    }
    if(localStorage.getItem('__clr')) {
      state.config.theme_color =  localStorage.getItem('__clr')
    }
    if(localStorage.getItem('__theme')) {
      state.config.theme =  localStorage.getItem('__theme')
    }
  },
  SET_PRODUCT_VIEW_MODE: (state, payload) => {
    state.config.product_view_mode = payload
  },
  SET_THEME: (state, payload) => {
    state.config.theme = payload
  },
  SET_DEMO: (state, payload) => {
    state.isDemoMode = payload
  },
  SET_THEME_COLOR: (state, clr) => {

    state.config.theme_color = clr 
    document.body.style.setProperty('--q-color-primary', clr)
    AddressbarColor.set(clr)
    localStorage.setItem('__clr', clr)

  },
  SET_INSTALL_APP: (state, payload) => {
    state.deferredPrompt = payload
  },
  REMOVE_INSTALL_APP: (state) => {
    state.deferredPrompt = null
  },

  SET_GUEST_CHECKOUT: (state, status) => {
    state.config.is_guest_checkout = status
  },
  SET_WHATSAPP_CHECKOUT: (state, status) => {
    state.config.is_whatsapp_checkout = status
  },
  SET_SESSION_ID: ( state, payload) => {
    if(!state.session_id) {
      state.session_id = payload
    }
  },
  SET_MENU_CATEGORY: (state, status) => {
    state.isMenuCategory = status
  }

}