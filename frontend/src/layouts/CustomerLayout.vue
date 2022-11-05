<template>
  <q-layout view="hHh lpR fFf" class="bg-white">
    <q-page-container>
        <router-view />
    </q-page-container>
  </q-layout>
</template>
<script>
import { mapState } from 'vuex'
import Cookies from 'js-cookie';
export default {
  name: 'AdminLayout',
  computed: {
        ...mapState({
      isCheckLogin: state => state.user.isCheckLogin,
      shop: state => state.shop,
      user: state => state.user.user,
      config: state => state.config
    })
  },
  created() {
    if(!this.shop) {
      this.$store.dispatch('getShop')
    }
    if(Cookies.get('__token')) {
      if(!this.user) {
        this.$store.dispatch('user/getUser')
      }
    }
    if(this.config) {
      this.$store.commit('SET_THEME_COLOR', this.config.theme_color)
    }
  },
  methods: {
     LogOut() {
      this.$store.dispatch('user/logout')
    },
  }
}
</script>
