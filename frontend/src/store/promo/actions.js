import { Api } from 'boot/axios'

export function getPromos ({commit}) {
  Api().get('promos').then(response => {
    if(response.status == 200) {
      commit('SET_PROMOS',  response.data.results)
    }
  })
}
export function storePromo ({ dispatch }, payload) {
  Api().post('promos', payload).then(() => {
    dispatch('getPromos')
  })
}
export function updatePromo ({ dispatch }, payload) {
  Api().post('promos/' + payload.id, payload).then(() => {
    dispatch('getPromos')
  })
}
export function deletePromo ({ dispatch }, id) {
  Api().delete('promos/' + id).then(() => {
    dispatch('getPromos')
  })
}
export function getProductPromo ({ commit }, id) {
  Api().get('promo/products/' + id).then((response) => {
    if(response.status == 200) {
      commit('SET_PRODUCT_PROMO', response.data.results)
    }
  })
}




