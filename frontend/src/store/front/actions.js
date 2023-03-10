import { Api } from 'boot/axios'

export function getPromotePost ({ commit }) {
  return Api().get('promote-posts')
}
export function getCategories ({ commit }) {
  return Api().get('getCategories')
}
export function getSliders ({commit }) {
   Api().get('getSliders').then(res => {
    if(res.status == 200) {
      commit('SET_SLIDERS', res.data.results)
    }
   })
}

