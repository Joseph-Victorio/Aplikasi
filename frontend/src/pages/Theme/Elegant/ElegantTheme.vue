<template>
   <q-page class="bg-grey-1 elegant main-page">
      <q-header reveal class="bg-brand text-white" :class="{ 'auto-padding-side': $q.platform.is.desktop }">
         <q-toolbar class="q-py-md">
            <img v-if="shop" class="logo" :src="shop.logo ? shop.logo : '/icon/icon-192x192.png'" alt="Logo" />
            <div class="col q-mx-sm inline-flex">
               <q-input dense ref="input" borderless class="input-search text-xs" v-model="search"
                  @keyup.enter="searchNow" placeholder="cari produk...">
                  <template v-slot:prepend>
                     <q-icon name="eva-search" class="cursor-pointer" @click="searchNow" />
                  </template>
               </q-input>
            </div>
            <ShoppingCart noFavorite />
         </q-toolbar>
      </q-header>

      <template v-if="!loading">
         <Slider />

         <FeaturedCarousel />

         <CategoryCarousel />

         <div id="product-promo" v-if="product_promo.length">
            <ProductPromo :product_promo="product_promo" />
         </div>

         <div v-if="banner1" class="banner auto-padding-side block-container">
            <img :src="banner1.image_url" @click="goToPost(banner1)" alt="banner">
         </div>

         <ProductSectionObserver v-if="is_category_mode" />
         <ProductContainer v-else />

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

      <q-inner-loading :showing="loading" label="Please wait...">
         <q-spinner-facebook size="50px" color="brand" />
      </q-inner-loading>

   </q-page>
</template>

<script>

import Slider from './block/Slider.vue'
import FeaturedCarousel from './../shared-components/FeaturedCarousel.vue'
import CategoryCarousel from './block/CategoryCarousel.vue'
import ProductPromo from './../shared-components/ProductPromo.vue'
import ShoppingCart from 'components/ShoppingCart.vue'
import ProductSectionObserver from './../shared-components/ProductSectionObserver.vue'
import ProductContainer from 'components/ProductContainer.vue'
export default {
   name: 'ElegantTheme',
   components: {
      ShoppingCart,
      Slider,
      FeaturedCarousel,
      CategoryCarousel,
      ProductPromo,
      ProductSectionObserver,
      ProductContainer,
      PostBlock: () => import('../shared-components/FrontPostBlock.vue'),
      FooterBock: () => import('./../shared-components/FooterBlock.vue'),
      InstallApp: () => import('components/InstallApp.vue')
   },
   data() {
      return {
         search: '',
      }
   },
   computed: {
      loading() {
         return this.$store.state.loading
      },
      banners() {
         return this.$store.state.front.blocks.banner
      },
      shop() {
         return this.$store.state.shop
      },
      config() {
         return this.$store.state.config
      },
      is_category_mode() {
         if (this.config && this.config.home_product_view == 'by_category') {
            return true
         }
         return false
      },
      product_promo() {
         return this.$store.state.front.product_promo
      },
      banner1() {
         return this.banners.find(b => b.weight == 1)
      },
      banner2() {
         return this.banners.find(b => b.weight == 2)
      },
      banner3() {
         return this.banners.find(b => b.weight == 3)
      },
   },
   methods: {
      searchNow() {
         if (!this.search || this.search == '') return
         this.$router.push({ name: 'ProductSearch', query: { q: this.search } })
      }
   },
}

</script>