import { Api } from 'boot/axios'

export default {
  getAdminConfig({ commit }) {
    commit('SET_LOADING', true)
    Api().get('admin-config').then((response) => {
      if (response.status == 200) {
        this.commit('SET_CONFIG', response.data.results)
      }
    })
      .finally(() => {
        commit('SET_LOADING', false)
      })
  },
  getShop: ({ commit }) => {
    Api().get('shop').then(response => {
      if (response.status == 200) {
        commit('SET_SHOP', response.data.results.shop)
        commit('SET_CONFIG', response.data.results.config)
      }
    })
  },
  getConfig: ({ commit }) => {
    Api().get('config').then(response => {
      if (response.status == 200) {
        commit('SET_CONFIG', response.data.results)
      }
    })
  },
  flushData() {
    Api().get('clear-cache')
  },
  getInitialData: async ({ commit }) => {
    document.body.classList.add('is_loading')
    commit('SET_LOADING', true)
    let response = await Api().get('getInitialData')
    if (response.status == 200) {
      commit('SET_SHOP', response.data.results.shop),
        commit('SET_CONFIG', response.data.results.config)
      commit('front/SET_BLOCKS', response.data.results.blocks, { root: true }),
        commit('front/SET_SLIDERS', response.data.results.sliders, { root: true }),
        commit('front/SET_PRODUCT_PROMO', response.data.results.product_promo, { root: true })
      commit('front/SET_CATEGORIES', response.data.results.categories, { root: true })
      commit('SET_SESSION_ID', response.data.results.sess_id)
      commit('front/SET_LOADED', null, { root: true })
    }

    document.body.classList.remove('is_loading')
    commit('SET_LOADING', false)
  },
}