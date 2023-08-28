<template>
  <q-layout view="hHh lpR fFf" class="bg-grey-2 front overflow-x-hidden">
    <notify v-if="config && config.is_notifypro" />
    <q-page-container>
      <router-view />
    </q-page-container>
    <q-footer class="bg-white text-primary footer-tab box-shadow-top">
      <q-tabs active-color="primary" class="text-grey-8 text-xs" no-caps dense switch-indicator indicator-color="primary">
        <q-route-tab icon="eva-home-outline" label="Beranda" :to="{ name: 'Home' }" exact />

        <q-route-tab icon="eva-shopping-cart-outline" :to="{ name: 'Cart' }" label="Cart" exact />

        <q-route-tab v-if="config && config.theme == 'default'" icon="eva-shopping-bag" :to="{ name: 'ProductIndex' }"
          label="Katalog" exact />
        <q-route-tab v-if="config && config.theme == 'romance'" icon="eva-shopping-bag" :to="{ name: 'ProductIndex' }"
          class="bg-primary text-white" label="Katalog" exact />
        <q-btn v-if="config && config.theme == 'elegant'" :to="{ name: 'ProductIndex' }" icon="eva-shopping-bag"
          class="text-md" color="primary" round></q-btn>

        <q-route-tab icon="eva-search" :to="{ name: 'ProductSearch' }" label="Cari" exact />

        <q-route-tab icon="eva-book-open-outline" :to="{ name: 'FrontPostIndex' }" label="Artikel" exact />

      </q-tabs>
    </q-footer>
    <BackToTop />
  </q-layout>
</template>

<script>
import Notify from 'components/Notify.vue'
import { mapGetters, mapState } from 'vuex'
import BackToTop from 'components/BackToTop.vue'
export default {
  components: { Notify, BackToTop },
  name: 'FrontLayout',
  data() {
    return {
      tab: 'images',
      onsearch: false,
      search: '',
    }
  },
  computed: {
    ...mapGetters('cart', ['cartCount']),
    ...mapGetters('product', ['favoriteCount']),
    ...mapState({
      shop: state => state.shop,
      config: state => state.config,
      categories: state => state.front.categories
    }),
    logoWidth() {
      if (this.shop && this.shop.name) {
        return 'width:35px;height:35px;object:cover'
      } else {
        return 'width:auto;height:35px;object:contain'
      }
    }
  },
  methods: {
    toDashboard() {
      if (this.currentUser) {
        if (this.currentUser.role == 'admin') {
          this.$router.push({ name: 'Settings' })
        } else {
          this.$router.push({ name: 'CustomerAccount' })
        }
      } else {
        this.$router.push({ name: 'Login' })

      }
    }
  },
  mounted() {
    if (!this.shop) {
      this.$store.dispatch('getShop')
    }
    if (!this.config) {
      this.$store.dispatch('getConfig')
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
        ogImage1: { property: 'og:image', content: this.shop?.logo },
      }

    }
  }
}
</script>