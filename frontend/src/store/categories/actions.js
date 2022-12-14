import { Api } from 'boot/axios'
import { Notify } from 'quasar'

export function getAdminCategories({commit}) {
  Api().get('categories').then(response => {
    if(response.status == 200) {
      commit('SET_CATEGORIES', response.data.results);
    }
  })
}
export function getCategory ({}, payload) {
    return Api().get('category/' + payload)
   }
export function categoryStore({dispatch, commit}, payload) {
    let self = this
    commit('SET_LOADING', true, { root: true })
    return Api().post('categories', payload, {headers: {'content-Type': 'multipart/formData'}})
    .then(response => {
      if(response.status == 201) {
        dispatch('getAdminCategories')
        Notify.create({
          type: 'positive',
          message: 'Berhasil menambah data'
        })
        self.$router.push({name: 'CategoryIndex'})
      }
    })
    .finally(() => {
      commit('SET_LOADING', false, { root: true })
    })
  }
export function categoryUpdate({dispatch, commit}, payload) {
  let self = this
  commit('SET_LOADING', true, { root: true })
  // console.log(Object.fromEntries(payload));
    Api().post('categories/' + payload.id , payload.data, {headers: {'content-Type': 'multipart/formData'}})
    .then(response => {
      if(response.status == 200) {
        dispatch('getAdminCategories');
        Notify.create({
          type: 'positive',
          message: 'Berhasil memperbarui data'
        })
        self.$router.push({name: 'CategoryIndex'})
      }
    }).
    finally(() => {
      commit('SET_LOADING', false, { root: true })
    })
  }
export function categoryDelete ({commit, dispatch}, payload) {
  commit('SET_LOADER_CATEGORY')
    Api().delete('categories/' + payload)
    .then(response => {
      commit('SET_LOADING', false, { root: true })
      dispatch('getAdminCategories')
      Notify.create({
        type: 'positive',
        message: 'Berhasil menghapus data'
      })
    })

  }
  // Front
export function getCategories({commit}) {
  Api().get('getCategories').then(response => {
    if(response.status == 200) {
      commit('SET_CATEGORIES', response.data.results);
    }
  })
}