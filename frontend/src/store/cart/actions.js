import { Api } from 'boot/axios'

export function getCarts ({ commit }) {
  Api().get('public/carts').then(response => {
    if(response.status == 200) {
      commit('SET_CARTS', response.data.results)
      commit('COMMIT_CARTS')
    }
  })

}
export function addToCart ({ commit }, payload) {

  commit('ADD_TO_CART', payload)
  
  Api().post('public/carts', payload)
  .then(response => {
    if(response.status == 200) {
      commit('COMMIT_CARTS')
    }
  })
  .catch(() => {
    commit('ROLLBACK_CARTS')
  })

}
export function updateCart ({ commit }, payload) {

  commit('UPDATE_CART', payload)

  Api().post('public/carts/' + payload.sku , {...payload, _method: 'PUT'})
  .then(response => {
    if(response.status == 200) {
      commit('COMMIT_CARTS')
    }
  })
  .catch(() => {
    commit('ROLLBACK_CARTS')
  })

}

export function removeCart ({ commit }, payload) {

  commit('REMOVE_CART', payload)
  Api().delete('public/carts/' + payload.sku)
  .then(response => {
    if(response.status == 200) {
      commit('COMMIT_CARTS')
    }
  })
  .catch(() => {
    commit('ROLLBACK_CARTS')
  })

}

export function clearCart({commit}) {
  commit('CLEAR_CART')
  Api().post('public/carts/clear')
  .then(response => {
    if(response.status == 200) {
      commit('COMMIT_CARTS')
    }
  })
  .catch(() => {
    commit('ROLLBACK_CARTS')
  })
}
