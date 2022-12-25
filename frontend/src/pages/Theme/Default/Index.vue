<template>
  <q-page class="bg-grey-1 default" :class="{'flex flex-center' : loading }">
    <q-header class="bg-white text-dark box-shadow" :class="{ 'auto-padding-side': $q.platform.is.desktop}">
      <q-toolbar class="items-center sans">
        <img v-if="shop" class="logo" :src="shop.logo? shop.logo : '/icon/icon-192x192.png'" alt="Logo"/>
        <q-toolbar-title v-if="shop && shop.name" class="text-weight-bold text-primary">{{ shop.name }}</q-toolbar-title>
        <shopping-cart  />
      </q-toolbar>
    </q-header>

     <q-inner-loading :showing="loading">
        <q-spinner-facebook size="50px" color="primary"/>
      </q-inner-loading>

    <template v-if="initial_data">

      <Slider />

      <FeaturedCarousel />

      <CategoryCarousel />
      

      <div id="product-promo" v-if="productPromo.length" >
        <product-promo :product_promo="productPromo" />
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

    </template>

  </q-page>
</template>

<script>
import { mapState } from 'vuex'
import ShoppingCart from 'components/ShoppingCart.vue'
import Slider from './block/Slider.vue'
import ProductSectionObserver from './../shared-components/ProductSectionObserver.vue'
import FeaturedCarousel from './../shared-components/FeaturedCarousel.vue'
import CategoryCarousel from './block/CategoryCarousel.vue'
import ProductPromo from './../shared-components/ProductPromo.vue'

export default {
  name: 'PageIndex',
  components: {
    ShoppingCart,
    Slider, 
    ProductSectionObserver, 
    FeaturedCarousel,
    CategoryCarousel,
    ProductPromo,
    PostBlock: () => import('../shared-components/FrontPostBlock.vue'), 
    FooterBock: () => import('./../shared-components/FooterBlock.vue'),
    InstallApp: () => import('components/InstallApp.vue')
  },
  data() {
    return {
      viewMode: 'grid',
      search: '',
      slide: 0,
    }
  },
  computed: {
    ...mapState({
      loading: state => state.loading,
      initial_data: state => state.initial_data,
      blocks: state => state.front.blocks,
      shop: state => state.shop,
      config: state => state.config,
      productPromo: state => state.product.product_promo,
    }),
    banner1() {
      if(this.blocks.banner.length) {
        let banner = this.blocks.banner.find(b => b.weight == 1)
        if(banner != undefined) {
          return banner
        }
      }
      return null
    },
    banner2() {
      if(this.blocks.banner.length) {
        let banner = this.blocks.banner.find(b => b.weight == 2)
        if(banner != undefined) {
          return banner
        }
      }
      return null
    },
    banner3() {
      if(this.blocks.banner.length) {
        let banner = this.blocks.banner.find(b => b.weight == 3)
        if(banner != undefined) {
          return banner
        }
      }
      return null
    },
  },
  methods: {
    showProductByCategory(id) {
      this.$router.push({name: 'ProductCategory', params: { id:id }})
    },
    searchNow() {
      if(!this.search || this.search == '') return
        this.$router.push({name: 'ProductSearch', query: {q: this.search }})
    },
    goToPost(block) {
      if(block.post) {
        this.$router.push({name: 'FrontPostShow', params: { slug: block.post.slug }})
      }
    }
  },
  mounted() {
    if(this.config) {
      this.viewMode = this.config.home_view_mode
    }
  }
}
</script>
