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
      },
      config() {
         return this.$store.state.config
      }
   },
   methods: {
      pageResize() {
         this.$store.commit('SET_PAGE_WIDTH', window.innerWidth)
      },
   },
   created() {
      if (this.config) {
         this.$store.commit('SET_CURRENT_THEME');
      }

      this.$store.commit('REMOVE_INSTALL_APP')

      window.addEventListener('beforeinstallprompt', (e) => {
         this.$store.commit('SET_INSTALL_APP', e)
      })

      window.addEventListener('appinstalled', () => {
         this.$store.commit('REMOVE_INSTALL_APP')
      })
      window.addEventListener('resize', this.pageResize)

   },
   beforeDestroy() {
      window.removeEventListener('resize', this.pageResize)
      window.removeEventListener('beforeinstallprompt')
   },
   meta: {
      meta: {
         equiv: { 'http-equiv': 'Content-Type', content: 'text/html; charset=UTF-8' },
         ogUrl: { property: 'og:url', content: location.href },
      },
      noscript: {
         default: 'This is content for browsers with no JS (or disabled JS)'
      }
   }
}
</script>
