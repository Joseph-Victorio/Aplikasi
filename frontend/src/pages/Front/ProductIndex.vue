<template>
  <q-page class="q-pb-xl bg-grey-1">
    <q-header class="text-brand bg-white box-shadow">
       <q-toolbar>
         <q-btn :to="{name: 'Home'}"
            flat round dense
            icon="eva-arrow-back" />
          <q-toolbar-title class="text-weight-bold brand">{{ title }}</q-toolbar-title>
          <shopping-cart  />
       </q-toolbar>
    </q-header>
    <product-section :title="title" :products="products"></product-section>
    <div class="flex justify-center q-py-lg" v-if="products && products.links">
     <q-btn label="loadmore" color="primary" outline :loading="isLoadmore" v-if="products.links.next" @click="paginate(products.links.next)"></q-btn>
   </div>
   <q-page-sticky :offset="[20,20]">
     <q-btn v-show="showTotop" icon="eva-chevron-up" round color="grey-7" glossy @click="jumpTo('q-app')"></q-btn>
   </q-page-sticky>
  </q-page>
</template>

<script>
import ProductSection from 'components/ProductSection.vue'
import ShoppingCart from 'components/ShoppingCart.vue'

import { Api } from 'boot/axios'
export default {
  name: 'ProductIndex',
  components: { ProductSection, ShoppingCart },
  data() {
    return {
      title: 'Katalog Produk',
      description: this.$store.state.meta.description,
      isLoadmore: false,
      isFilter: true,
      showTotop: false
    }
  },
  computed: {
    products() {
      return this.$store.state.product.products
    },
    config() {
      return this.$store.state.config
    }
  },
  methods: {
    paginate(url) {
      this.isLoadmore = true

      let param = {
          per_page: this.config.catalog_product_limit,
          order_by: this.config.catalog_product_sort,
        }
        url += `&${new URLSearchParams(param).toString()}`

      Api().get(url).then(response => {
        if(response.status == 200) {
          this.$store.commit('product/PAGINATE_PRODUCTS', response.data)
        }
      }).finally(() =>  this.isLoadmore = false)
    },
    setTotop() {
      console.log(window.scrollY);
      if(window.scrollY > 1000) {
        this.showTotop = true
      }else {
        this.showTotop = false
      }
    }
  },
  created() {
    if(this.$route.query.q){
      this.title = 'Produk ' + this.$route.query.q
    }
    if(!this.products.data.length) {
      this.$store.dispatch('product/getProducts', {
        per_page: this.config.catalog_product_limit,
        order_by: this.config.catalog_product_sort,
      })
    }
    window.addEventListener('scroll', this.setTotop)
  },
  destroyed() {
    window.removeEventListener('scroll', this.setTotop)
  },
  meta() {
    return {
      title: 'Katalog Produk',
      meta: {
        description: { name: 'description', content: this.description },
        ogDescription: { name: 'og:description', content: this.description },
        ogTitle:  { name: 'og:title', content: this.title },
        ogUrl:  { name: 'og:url', content: location.href },
      }
      
    }
  }
}
</script>
<style lang="scss">
.relative {
  position:relative;
}
.absolute {
  position:absolute;
  &__top-right{
    top:0;
    right:0;
  }
}
.mini .q-field__marginal,
.mini .q-field__control{
 height: 30px;
}
</style>