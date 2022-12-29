import { Api } from 'boot/axios'
import Cookies from 'js-cookie';
import { Notify } from 'quasar'

export function login ({commit, dispatch}, payload) {
  commit('SET_LOADING', true, { root: true })

  Api().post('user/login', payload)
  .then(response => {
    if (response.status == 200) {
      let user = response.data.results
      commit('SET_USER', user)
      Cookies.set('__token', response.data.token, { expires: 1 })
      if(user.role == 'admin') {
        this.$router.push({name: 'Settings'})
      } else {
        this.$router.push({name: 'CustomerAccount'})
      }
    }
  })
  .finally(() => {
    commit('SET_LOADING', false, { root: true })
  })

}
export function register ({commit, dispatch}, payload) {
  commit('SET_LOADING', true, { root: true })

  Api().post('user/register', payload)
  .then(response => {
    if (response.status == 201) {
      Cookies.set('__token', response.data.token, { expires: 1 })
      let user = response.data.results
      commit('SET_USER', user)
      if(user.role == 'admin') {
        this.$router.push({name: 'Settings'})
      } else {
        this.$router.push({name: 'CustomerAccount'})
      }
    }
  })
  .finally(() => {
    commit('SET_LOADING', false, { root: true })
  })

}
export function logout({ commit, dispatch }) {
  commit('LOGOUT')
  Cookies.remove('__token')
  this.$router.push('/')
  Api().post('user/logout')
  setTimeout(() => dispatch('getConfig', null, { root: true }), 1000)
}
export function exit({ commit }) {
  commit('LOGOUT')
  Cookies.remove('__token')
  this.$router.push('/')
}
export function getUser ({ commit }) {
  Api().get('user').then(response => {
    if(response.status == 200) {
      commit('SET_USER', response.data.results)
    }
  }).catch((error) => {
    if(401 === error.response.status) {
        commit('LOGOUT')
        this.$router.push('/')
    }
  })
}
export function updateUser ({commit}, payload) {
  Api().post('user/update', payload).then(response => {
    if(response.status == 200) {
      commit('SET_USER', response.data.results)
      commit('SET_LOADING', false, {root: true})
      // this.$router.push({name: 'Settings'})
      Notify.create({
        type: 'positive',
        message: 'Berhasil mengedit data'
      })
    }
  }).catch((error) => {
      commit('SET_LOADING', false, {root: true})
      if(401 === error.response.status) {
          commit('LOGOUT')
          this.$router.push('/')
      }
  })
}

export function requestPasswordToken({commit}, payload) {
  return Api().post('requestPasswordToken', payload)
}

export function resetPassword({}, payload) {

  Api().post('resetPassword', payload).then(response => {
    if(response.status == 200 && response.data.success) {
      localStorage.removeItem('is_reqpwd')

      Notify.create({
        type: 'positive',
        message: response.data.message
      })

      this.$router.push({name: 'Login'})
    }
  })
}
