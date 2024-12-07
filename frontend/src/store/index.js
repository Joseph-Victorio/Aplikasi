import Vue from 'vue'
import Vuex from 'vuex'

import createPersistedState from "vuex-persistedstate";

import SecureLS from "secure-ls";
var ls = new SecureLS({ isCompression: false });

import user from './user'
import product from './product'
import category from './categories'
import slider from './slider'
import post from './post'
import block from './block'
import order from './order'
import bank from './bank'
import cart from './cart'
import promo from './promo'
import front from './front';

import mutations from './mutations'
import * as getters from './getters'
import actions from './actions'

const stateData = createPersistedState({
   key: '__state',
   paths: ['user', 'cart', 'shop', 'config', 'session_id', 'product.favorites', 'forgot_password'],
   storage: {
      getItem: (key) => ls.get(key),
      setItem: (key, value) => ls.set(key, value, { expires: 1 }),
      removeItem: (key) => ls.remove(key),
   }
})

Vue.use(Vuex)

export default function (/* { ssrContext } */) {

   const Store = new Vuex.Store({
      state: {
         themes: ['default', 'elegant', 'romance'],
         errors: {},
         loading: false,
         shop: null,
         config: null,
         deferredPrompt: null,
         isMenuCategory: false,
         session_id: null,
         page_width: window.innerWidth,
         initial_data: false,
         can_install: false,
         forgot_password: {
            token: '',
            email: '',
            hide_email: 'anda'
         },
         meta: {
            title: '',
            description: 'Simple Web Commerce checkout whatsapp'
         }
      },
      actions,
      mutations,
      getters,
      modules: {
         user,
         product,
         category,
         slider,
         post,
         block,
         order,
         bank,
         cart,
         promo,
         front
      },
      plugins: [stateData],

      // enable strict mode (adds overhead!)
      // for dev mode only
      strict: process.env.DEBUGGING
   })

   return Store
}
