<template>
  <div>
    <component :is="isActiveComponent" />
  </div>
</template>

<script>

import defaultTheme from 'pages/Theme/Default/Index.vue';
import romanceTheme from 'pages/Theme/Romance/Index.vue';
import elegantTheme from 'pages/Theme/Elegant/Index.vue';

export default {
  name: 'PageIndex',
  components: {
    elegant: elegantTheme, 
    default: defaultTheme, 
    romance: romanceTheme,
  },
  computed: {
    isActiveComponent() { 
      return this.$store.getters['getTheme']
    },
    loading() {
      return this.$store.state.loading
    },
    shop() {
      return this.$store.state.shop
    },
    config() {
      return this.$store.state.config
    },
    title() {
      if(this.shop) {
        return `Beranda - ${this.shop.name}`
      }
      return 'Beranda'
    }
  },
  meta() {
    return {
      title: this.title,
    }
  }
}
</script>
