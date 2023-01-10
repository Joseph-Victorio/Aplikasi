<template>
  <q-page class="romance bg-light" :class="{'flex flex-center' : loading }">
    <q-header reveal :reveal-offset="10" class="bg-white box-shadow" :class="{ 'auto-padding-side': $q.platform.is.desktop}">
        <q-toolbar class="q-py-sm">
          <img v-if="shop" class="logo" :src="shop.logo? shop.logo : '/icon/icon-192x192.png'" alt="Logo" />
          <div class="col q-ml-sm row items-center">
            <div class="col">
              <q-input borderless ref="input" color="grey-4" dense class="input-search text-xs bg-grey-1" v-model="search" @keyup.enter="searchNow" placeholder="cari produk..."
                >
                <template v-slot:prepend>
                  <q-icon
                    name="eva-search"
                    class="cursor-pointer"
                    @click="searchNow"
                  />
                </template>
              </q-input>
            </div>
            <div class="q-pl-sm">
              <shopping-cart  />
            </div>
            </div>
        </q-toolbar>
      </q-header>

       <q-inner-loading :showing="loading">
        <q-spinner-facebook size="50px" color="primary"/>
      </q-inner-loading>

      <template v-if="initial_data">

        <Slider />

        <FeaturedCarousel />

        <CategoryCarousel />

        <div id="product-promo" v-if="product_promo.length" >
          <ProductPromo :product_promo="product_promo" />
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
import FeaturedCarousel from './../shared-components/FeaturedCarousel.vue'
import CategoryCarousel from './block/CategoryCarousel.vue'
import ProductPromo from './../shared-components/ProductPromo.vue'
import ProductSectionObserver from './../shared-components/ProductSectionObserver.vue'

export default {
  name: 'PageIndex',
  components: {
    ShoppingCart,
    Slider, 
    FeaturedCarousel,
    CategoryCarousel,
    ProductPromo,
    ProductSectionObserver,
    // ProductSectionObserver: () => import('./../shared-components/ProductSectionObserver.vue'),
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
      product_promo: state => state.front.product_promo,
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
    },
    onObserve(val) {
      console.log(val);
    }
  },
  mounted() {
    if(this.config) {
      this.viewMode = this.config.home_view_mode
    }
  }
}
</script>
