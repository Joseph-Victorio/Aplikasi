import { Api } from 'boot/axios'

export function getPromotePost ({ }) {
  return Api().get('promote-posts')
}
export function getCategories ({}, params = {}) {

  let url = 'getCategories'

  if(Object.entries(params).length) {
    url += `?${ new URLSearchParams(params).toString()}`
  }

  return Api().get(url)
}
export function getSliders ({commit }) {
   Api().get('getSliders').then(res => {
    if(res.status == 200) {
      commit('SET_SLIDERS', res.data.results)
    }
   })
}

