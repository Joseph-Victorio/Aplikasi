<template>
  <q-layout view="hHh lpR fFf" class="bg-white">
    <q-page-container>
      <router-view />
    </q-page-container>
  </q-layout>
</template>
<script>
import { mapState } from 'vuex'
export default {
  name: 'AdminLayout',
  computed: {
    ...mapState({
      shop: state => state.shop,
      config: state => state.config
    })
  },
  created() {
    if (!this.shop) {
      this.$store.dispatch('getShop')
    }
    if (localStorage.getItem('__token')) {
      if (!this.currentUser) {
        this.$store.dispatch('user/getUser')
      }
    }
    if (this.config) {
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
