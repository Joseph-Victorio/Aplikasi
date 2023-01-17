import { Api } from 'boot/axios'

export function addPost({ commit, dispatch }, payload) {
  let formData = new FormData();
  for(const x in payload) {
    if(payload[x] !== '') {
      formData.append(x, payload[x])
    }
  }
  let self = this
  commit('SET_LOADING', true, { root: true })
  Api().post('posts', formData, {headers: {'content-Type': 'multipart/formData'}}).then(response => {
    if(response.status == 200) {
     dispatch('getAllPost')
      self.$router.push({name: 'AdminPostIndex'})
      commit('SET_LOADING', false, { root: true })
    }
  }).finally(() => {
    commit('SET_LOADING', false, { root: true })
  })
}
export function updatePost ({ commit, dispatch }, payload) {
  commit('SET_LOADING', true, { root: true })
  let self = this
  let formData = new FormData();
  for(const x in payload) {
    if(payload[x] !== '') {
      formData.append(x, payload[x])
    }
  }
  formData.append('_method', 'PUT')

  Api().post('posts/'+payload.id, formData, {headers: {'content-Type': 'multipart/formData'}}).then(response => {
    if(response.status == 200) {
      self.$router.push({name: 'AdminPostIndex'})
      dispatch('getAllPost')
    }
  }).finally(() => {
    commit('SET_LOADING', false, { root: true })
  })
}
export function deletePost ({ commit, dispatch }, id) {
  commit('SET_LOADING', false, { root: true })
  Api().delete('posts/'+id).then(response => {
    if(response.status == 200) {
      dispatch('getAllPost')
    }
  }).finally(() => {
    commit('SET_LOADING', false, { root: true })
  })
}
export function getAllPost ({ commit }) {
  Api().get('posts').then(response => {
    if(response.status == 200) {
      commit('SET_ALL_POST', response.data.results)
    }
  })
}
export function getPosts ({ commit }) {
  Api().get('public/posts?q=listing').then(response => {
    if(response.status == 200) {
      commit('SET_LISTING_POST', response.data.results)
    }
  })
}
export function getSinglePost (context, id) {
  return Api().get('posts/'+ id)
}

