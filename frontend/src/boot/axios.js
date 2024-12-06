import Vue from 'vue'
import axios from 'axios'
import { Notify } from 'quasar'

Vue.prototype.$axios = axios

var BaseApi = axios.create({
   baseURL: process.env.API
})

BaseApi.defaults.headers.common = { 'X-Requested-With': 'XMLHttpRequest' }


export default ({ app, router, store, urlPath }) => {

   BaseApi.interceptors.request.use(config => {

      let session_id = store.state.session_id
      if (session_id) {
         config.headers['Session-User'] = session_id;
      }

      return config
   }, error => {
      // Do something with request error

      store.commit('SET_LOADING', false)

      Notify.create({
         type: 'negative',
         message: 'Network Error'
      })
      return Promise.reject(error)
   })

   // Add a response interceptor
   BaseApi.interceptors.response.use(response => {
      store.commit('SET_LOADING', false)

      return response

   }, error => {
      store.commit('SET_LOADING', false)

      if (error.response.status == 503) {

         if (error.response.data.success == false && error.response.data.is_installed == false) {
            store.commit('CAN_INSTALL', true)

            if (!urlPath.includes('install')) {
               router.push({ name: 'InstallApp' })
            }

         }
      }

      // var errors = error
      var errors = []
      let showMessage = true

      if (error.response) {

         if (401 === error.response.status) {
            showMessage = false
            errors.push(error.response.data.message)
            store.dispatch('user/exit')
         }

         // Errors from backend
         if (error.response.status == 422) {
            // errors = error.response.data.message

            for (var errorKey in error.response.data.errors) {
               for (var i = 0; i < error.response.data.errors[errorKey].length; i++) {
                  errors.push(String(error.response.data.errors[errorKey][i]))
               }
            }
         }

         // Backend error
         if (500 === error.response.status) {
            // errors = error.response.data.message
            errors.push('Something went wrong, Please try again')
            showMessage = false
         }
         if (400 === error.response.status) {
            // errors = error.response.data.message
            errors.push('Jaringan sedang bermasalah, silahkan tunggu beberapa saat.')
            showMessage = false
         }

      } else {
         store.commit('SET_LOADING', false)
         errors.push('Jaringan sedang bermasalah, silahkan tunggu beberapa saat.')
         showMessage = false
      }

      if (showMessage && errors.length) {
         Notify.create({
            type: 'negative',
            message: String(errors[0]),
         })
      }

      // Do something with response error       
      return Promise.reject(error)
   })

}

var Api = () => {

   const token = localStorage.getItem('__token')

   if (token) {
      BaseApi.defaults.headers.common['Authorization'] = `Bearer ${token}`
   }

   return BaseApi
}

export { Api }


