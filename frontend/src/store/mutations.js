import { AddressbarColor, colors } from 'quasar'

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

    if(!localStorage.getItem('__theme_cnfg')) {
      let data = JSON.parse(localStorage.getItem('__theme_cnfg'))

      state.config.theme = data.theme
      state.config.theme_color = data.theme_color
      state.config.primary_color = data.primary_color
      state.config.secondary_color = data.secondary_color
      state.config.accent_color = data.accent_color

    }else {
        let themeCfg = {
          theme: state.config.theme,
          theme_color: state.config.theme_color,
          primary_color: state.config.primary_color,
          secondary_color: state.config.secondary_color,
          accent_color: state.config.accent_color,
        }

        localStorage.setItem('__theme_cnfg', JSON.stringify(themeCfg))
    }

    colors.setBrand('brand', state.config.theme_color);
    colors.setBrand('primary', state.config.primary_color);
    colors.setBrand('secondary', state.config.secondary_color);
    colors.setBrand('accent', state.config.accent_color);

    AddressbarColor.set(state.config.theme_color)

  },
  SET_HOME_VIEW_MODE: (state, payload) => {
    state.config.home_view_mode = payload
  },
  SET_PRODUCT_VIEW_MODE: (state, payload) => {
    state.config.product_view_mode = payload
  },
  SET_THEME: (state, payload) => {
    state.config.theme = payload
  },
  SET_THEME_COLOR: (state, clr) => {
    state.config.theme_color = clr 
    colors.setBrand('brand', state.config.theme_color)
    AddressbarColor.set(state.config.theme_color)
  },
  SET_PRIMARY_COLOR: (state, clr) => {
    state.config.primary_color = clr 
    colors.setBrand('primary', state.config.primary_color)
  },
  SET_SECONDARY_COLOR: (state, clr) => {
    state.config.secondary_color = clr 
    colors.setBrand('secondary', state.config.secondary_color)
  },
  SET_ACCENT_COLOR: (state, clr) => {
    state.config.accent_color = clr 
    colors.setBrand('accent', state.config.accent_color)
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
  },
  SET_PAGE_WIDTH: ( state, width) => {
    state.page_width = width
  },
  SET_INITIAL_DATA: (state, status) => {
    state.initial_data = status
  }
}