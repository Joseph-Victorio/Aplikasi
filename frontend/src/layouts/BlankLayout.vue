<template>
  <q-layout view="hHh lpR fFf" class="front">
    <q-page-container>
      <router-view />
    </q-page-container>
  </q-layout>
</template>

<script>
import { mapState } from 'vuex'
export default {
  name: 'BlankLayout',
  computed: {
    ...mapState({
      shop: state => state.shop,
      config: state => state.config,
    })
  },
  created() {
    if (!this.shop) {
      this.$store.dispatch('getShop')
    }
    if (!this.config) {
      this.$store.dispatch('getConfig')
    } else {
      this.$store.commit('SET_THEME_COLOR', this.config.theme_color)
    }
    if (localStorage.getItem('__token')) {
      if (!this.currentUser) {
        this.$store.dispatch('user/getUser')
      }
    }
  },
  meta() {
    return {
      meta: {
        ogUrl: { property: 'og:url', content: location.href },
        ogImage: { property: 'og:image', content: this.shop?.logo },
      }

    }
  }
}
</script>