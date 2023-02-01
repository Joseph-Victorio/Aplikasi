<template>
  <q-page :class="{'flex flex-center':!products.available}" class="q-pb-xl bg-grey-1">
     <q-header class="text-grey-9 bg-white box-shadow">
       <q-toolbar>
         <q-btn @click="backButton"
            flat round dense
            icon="eva-arrow-back" />
          <q-toolbar-title class="text-weight-bold brand">{{ title }}</q-toolbar-title>
          <shopping-cart  />
       </q-toolbar>
    </q-header>
    <template>
      <product-section title="Produk Katalog" :products="products"></product-section>
      <div class="flex justify-center q-py-lg" v-if="products.total > products.data.length">
        <q-btn label="loadmore" color="primary" outline :loading="isLoadmore" @click="paginate"></q-btn>
      </div>
    </template>
    <template v-if="!products.available">
      <div class="column items-center">
        <p class="text-grey-8 text-weight-bold text-center">Tidak ada produk ditemukan</p>
        <q-btn unelevated :to="{name: 'ProductIndex'}" rounded text-color="white" color="primary"
            icon="keyboard_backspace" label="Kehalaman Produk" no-caps/>
      </div>
    </template>
    <q-inner-loading :showing="loading"></q-inner-loading>
  </q-page>
</template>

<script>
import { mapActions } from 'vuex'
import ProductSection from 'components/ProductSection.vue'
import ShoppingCart from 'components/ShoppingCart.vue'
import { Api } from 'boot/axios'
export default {
  name: 'ProductCategory',
  components: { ProductSection, ShoppingCart },
  data () {
    return {
      likes: [],
      description: this.$store.state.meta.description,
      shop: this.$store.state.shop,
      isLoadmore: false,
    }
  },
  computed: {
    favorites: function() {
      return this.$store.state.product.favorites
    },
    products() {
      return this.$store.state.product.productsByCategory
    },
    categories() {
      return this.$store.state.category.categories
    },
    title() {
      return this.$store.state.meta.title
    },
    config() {
      return this.$store.state.config
    },
    loading() {
      return this.$store.state.loading
    }
      
  },
  methods: {
    ...mapActions('product', ['productsByCategory']),
    backButton() {
      this.$router.push({name: 'ProductIndex'})
    },
    paginate() {
      this.isLoadmore = true
      let url ='product-category'
      let param = {
          category_id: this.$route.params.id,
          per_page: this.config.catalog_product_limit,
          order_by: this.config.catalog_product_sort,
          offset: this.products.data.length
        }
        url += `?${new URLSearchParams(param).toString()}`

      Api().get(url).then(response => {
        if(response.status == 200) {
          this.$store.commit('product/PAGINATE_PRODUCT_CATEGORY', response.data)
        }
      }).finally(() =>  this.isLoadmore = false)
    }
  },
  created() {
    if(this.products.data.length) {

      if(this.products.data[0].id != this.$route.params.id) {

        this.$store.commit('product/CLEAR_PRODUCT_CATEGORY')
        this.productsByCategory({
          category_id: this.$route.params.id,
          per_page: this.config.catalog_product_limit,
          order_by: this.config.catalog_product_sort,
        })

      }

    } else {

      this.$store.commit('product/CLEAR_PRODUCT_CATEGORY')
      this.productsByCategory({
        category_id: this.$route.params.id,
        per_page: this.config.catalog_product_limit,
        order_by: this.config.catalog_product_sort,
      })
    }
  },
  meta() {
  return {
    title: this.title,
    meta: {
        description: { name: 'description', content: this.description },
        ogDescription: { name: 'og:description', content: this.description },
        ogTitle:  { name: 'og:title', content: this.title },
        ogUrl:  { name: 'og:url', content: location.href },
        ogImage:  { name: 'og:image', content: this.shop?.logo ? this.shop.logo: ''},
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