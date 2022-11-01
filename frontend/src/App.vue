<template>
  <div id="q-app" class="mobile-view bg-grey-1">
    <router-view />
  </div>
</template>
<script>
export default {
  name: 'App',
  computed: {
    session_id() {
      return this.$store.state.session_id
    }
  },
  mounted() {
    this.$store.dispatch('cart/getCarts')
  },
  created() {
    this.$store.commit('REMOVE_INSTALL_APP')
    window.addEventListener('beforeinstallprompt', (e) => {
      this.$store.commit('SET_INSTALL_APP', e)
    })

    window.addEventListener('appinstalled', () => {
      this.$store.commit('REMOVE_INSTALL_APP')
    })
  },
  meta: {
    meta: {
      equiv: { 'http-equiv': 'Content-Type', content: 'text/html; charset=UTF-8' },
      // note: for Open Graph type metadata you will need to use SSR, to ensure page is rendered by the server
      ogUrl:  { property: 'og:url', content: location.href },
    },
    noscript: {
      default: 'This is content for browsers with no JS (or disabled JS)'
    }
  }
}
</script>
