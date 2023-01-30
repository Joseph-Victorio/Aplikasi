import { Api } from 'boot/axios'

export function getPromotePost ({ commit }) {
  return Api().get('promote-posts')
}
export function getCategories ({ commit }) {
  return Api().get('getCategories')
}

