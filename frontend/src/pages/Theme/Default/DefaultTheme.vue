<template>
  <q-page class="bg-grey-1 default">
    <q-header class="bg-white text-dark box-shadow" :class="{ 'auto-padding-side': $q.platform.is.desktop}">
      <q-toolbar class="items-center sans">
        <img v-if="shop" class="logo" :src="shop.logo? shop.logo : '/icon/icon-192x192.png'" alt="Logo"/>
        <q-toolbar-title v-if="shop && shop.name" class="text-weight-bold text-brand">{{ shop.name }}</q-toolbar-title>
        <shopping-cart  />
      </q-toolbar>
    </q-header>

      <Slider />

      <FeaturedCarousel />

      <CategoryCarousel />

      <div id="product-promo" v-if="product_promo.length" >
        <product-promo :product_promo="product_promo" />
      </div>
      
      <div v-if="banner1" class="banner auto-padding-side block-container">
        <img :src="banner1.image_url" @click="goToPost(banner1)" alt="banner">
      </div>
      
      <ProductSectionObserver />
      
      <div v-if="banner2" class="banner auto-padding-side block-container">
        <img :src="banner2.image_url" @click="goToPost(banner2)" alt="banner">
      </div>

      <PostBlock />

      <div v-if="banner3" class="banner auto-padding block-container">
        <img :src="banner3.image_url" @click="goToPost(banner3)" alt="banner">
      </div>

     <InstallApp />

      <FooterBock />

  </q-page>
</template>

<script>
import { mapState } from 'vuex'
import ShoppingCart from 'components/ShoppingCart.vue'
import Slider from './block/Slider.vue'
import FeaturedCarousel from './../shared-components/FeaturedCarousel.vue'
import CategoryCarousel from './block/CategoryCarousel.vue'
import ProductPromo from './../shared-components/ProductPromo.vue'

export default {
  name: 'DefaultTheme',
  components: {
    ShoppingCart,
    Slider, 
    FeaturedCarousel,
    CategoryCarousel,
    ProductPromo,
    ProductSectionObserver: () => import('./../shared-components/ProductSectionObserver.vue'),
    PostBlock: () => import('../shared-components/FrontPostBlock.vue'), 
    FooterBock: () => import('./../shared-components/FooterBlock.vue'),
    InstallApp: () => import('components/InstallApp.vue')
  },
  computed: {
    ...mapState({
      loading: state => state.loading,
      initial_data: state => state.initial_data,
      banners: state => state.front.blocks.banner,
      shop: state => state.shop,
      config: state => state.config,
      product_promo: state => state.front.product_promo,
    }),
    banner1() {
      return this.banners.find(b => b.weight == 1)
    },
    banner2() {
      return this.banners.find(b => b.weight == 2)
    },
    banner3() {
      return this.banners.find(b => b.weight == 3)
    }
  }
}
</script>
