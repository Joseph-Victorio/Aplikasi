<template>
  <keep-alive>
    <component :is="isActiveComponent" />
  </keep-alive>
</template>

<script>
export default {
  name: 'PageIndex',
  components: {
    default: () =>  import('src/pages/Theme/Default/DefaultTheme.vue'),
    romance: () =>  import('src/pages/Theme/Romance/RomanceTheme.vue'),
    elegant: () =>  import('src/pages/Theme/Elegant/ElegantTheme.vue'),
  },
  computed: {
    isActiveComponent() { 
      return this.$store.getters['getTheme']
    },
    shop() {
      return this.$store.state.shop
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
