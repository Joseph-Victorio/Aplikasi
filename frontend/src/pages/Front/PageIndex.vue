<template>
  <keep-alive>
    <component :is="isActiveComponent" />
  </keep-alive>
</template>

<script>

// import defaultTheme from 'src/pages/Theme/Default/DefaultTheme.vue';
// import romanceTheme from 'src/pages/Theme/Romance/RomanceTheme.vue';
// import elegantTheme from 'src/pages/Theme/Elegant/ElegantTheme.vue';

export default {
  name: 'PageIndex',
  components: {
    default: () =>  import('src/pages/Theme/Default/DefaultTheme.vue'),
    romance: () =>  import('src/pages/Theme/Romance/RomanceTheme.vue'),
    elegant: () =>  import('src/pages/Theme/Elegant/ElegantTheme.vue'),
    // elegant: elegantTheme, 
    // default: defaultTheme, 
    // romance: romanceTheme,
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
